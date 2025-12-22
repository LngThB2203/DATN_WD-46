<?php
namespace App\Services;

use App\Helpers\OrderStatusHelper;
use App\Models\StockTransaction;
use App\Models\WarehouseBatch;
use App\Models\WarehouseProduct;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StockService
{
    protected function createStockTransaction(array $data): void
    {
        static $hasTypeColumn = null;
        static $hasBatchIdColumn = null;

        if ($hasTypeColumn === null) {
            $hasTypeColumn = Schema::hasColumn('stock_transactions', 'type');
        }
        if ($hasBatchIdColumn === null) {
            $hasBatchIdColumn = Schema::hasColumn('stock_transactions', 'batch_id');
        }

        if (! $hasTypeColumn) {
            unset($data['type']);
        }
        if (! $hasBatchIdColumn) {
            unset($data['batch_id']);
        }

        StockTransaction::create($data);
    }

    // Nhập kho
    public function import(array $data, string $referenceType, ?int $referenceId = null): void
    {
        DB::transaction(function () use ($data, $referenceType, $referenceId) {

            $batch = WarehouseBatch::lockForUpdate()->firstOrCreate(
                [
                    'warehouse_id' => $data['warehouse_id'],
                    'product_id'   => $data['product_id'],
                    'variant_id'   => $data['variant_id'],
                    'batch_code'   => $data['batch_code'],
                ],
                [
                    'quantity'     => 0,
                    'expired_at'   => $data['expired_at'] ?? null,
                    'import_price' => $data['import_price'] ?? 0,
                ]
            );

            $before = $batch->quantity;
            $after  = $before + $data['quantity'];

            $batch->update(['quantity' => $after]);

            $this->createStockTransaction([
                'warehouse_id'    => $batch->warehouse_id,
                'product_id'      => $batch->product_id,
                'variant_id'      => $batch->variant_id,
                'batch_id'        => $batch->id,
                'batch_code'      => $batch->batch_code,
                'type'            => 'import',
                'quantity'        => $data['quantity'],
                'before_quantity' => $before,
                'after_quantity'  => $after,
                'reference_type'  => $referenceType,
                'reference_id'    => $referenceId,
            ]);

            $this->syncWarehouseProduct(
                $batch->warehouse_id,
                $batch->product_id,
                $batch->variant_id,
                $data['quantity']
            );
        });
    }

    // Xuất kho thủ công
    public function export(
        int $warehouseId,
        int $productId,
        ?int $variantId,
        int $quantity,
        string $referenceType,
        ?int $referenceId = null,
        ?string $note = null
    ): void {
        $this->exportFIFO(
            $warehouseId,
            $productId,
            $variantId,
            $quantity,
            $referenceType,
            $referenceId
        );
    }

    // Xuất kho chỉ qua đơn hàng
    public function exportByOrder($order): void
    {
        if ($this->isOrderExported($order->id)) {
            return;
        }

        DB::transaction(function () use ($order) {
            foreach ($order->details as $detail) {
                $this->exportFIFO(
                    $order->warehouse_id,
                    $detail->product_id,
                    $detail->variant_id,
                    $detail->quantity,
                    'order',
                    $order->id
                );
            }
        });
    }

    // Hủy đơn hàng (trả kho)
    public function cancelOrder($order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->details as $detail) {
                $restoreQty = $detail->quantity;

                // Sync tồn kho tổng (WarehouseProduct)
                $wp = WarehouseProduct::lockForUpdate()->firstOrCreate(
                    [
                        'warehouse_id' => $order->warehouse_id,
                        'product_id'   => $detail->product_id,
                        'variant_id'   => $detail->variant_id,
                    ],
                    ['quantity' => 0]
                );
                $before = $wp->quantity;
                $after  = $before + $restoreQty;
                $wp->update(['quantity' => $after]);

                // Nếu có giao dịch xuất kho trước đó, cộng lại batch
                $baseExportsQuery = StockTransaction::where('reference_type', 'order')
                    ->where('reference_id', $order->id)
                    ->where('product_id', $detail->product_id)
                    ->where('variant_id', $detail->variant_id);

                try {
                    $exportsQuery = clone $baseExportsQuery;
                    if (Schema::hasColumn('stock_transactions', 'type')) {
                        $exportsQuery->where('type', 'export');
                    } else {
                        $exportsQuery->where('quantity', '<', 0);
                    }

                    $exports = $exportsQuery->get();
                } catch (QueryException $e) {
                    if (str_contains($e->getMessage(), "Unknown column 'type'")) {
                        $exports = (clone $baseExportsQuery)->where('quantity', '<', 0)->get();
                    } else {
                        throw $e;
                    }
                }

                foreach ($exports as $log) {
                    $batch = WarehouseBatch::lockForUpdate()->find($log->batch_id);
                    if (! $batch) {
                        continue;
                    }

                    $batchBefore = $batch->quantity;
                    $batchAfter  = $batchBefore + abs($log->quantity);
                    $batch->update(['quantity' => $batchAfter]);

                    $this->createStockTransaction([
                        'warehouse_id'    => $batch->warehouse_id,
                        'product_id'      => $batch->product_id,
                        'variant_id'      => $batch->variant_id,
                        'batch_id'        => $batch->id,
                        'batch_code'      => $batch->batch_code,
                        'type'            => 'import',
                        'quantity'        => abs($log->quantity),
                        'before_quantity' => $batchBefore,
                        'after_quantity'  => $batchAfter,
                        'reference_type'  => 'order_cancel',
                        'reference_id'    => $order->id,
                    ]);
                }

                // Ghi log tổng cho đơn hủy
                $this->createStockTransaction([
                    'warehouse_id'    => $order->warehouse_id,
                    'product_id'      => $detail->product_id,
                    'variant_id'      => $detail->variant_id,
                    'batch_id'        => null,
                    'batch_code'      => null,
                    'type'            => 'import',
                    'quantity'        => $restoreQty,
                    'before_quantity' => $before,
                    'after_quantity'  => $after,
                    'reference_type'  => 'order_cancel',
                    'reference_id'    => $order->id,
                ]);
            }

            // Cập nhật trạng thái đơn
            $order->update([
                'order_status' => OrderStatusHelper::CANCELLED,
                'cancelled_at' => now(),
                'completed_at' => null,
            ]);
        });
    }

    public function isOrderExported(int $orderId): bool
    {
        $baseQuery = StockTransaction::where('reference_type', 'order')
            ->where('reference_id', $orderId);

        try {
            $query = clone $baseQuery;

            if (Schema::hasColumn('stock_transactions', 'type')) {
                $query->where('type', 'export');
            } else {
                $query->where('quantity', '<', 0);
            }

            return $query->exists();
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), "Unknown column 'type'")) {
                return (clone $baseQuery)->where('quantity', '<', 0)->exists();
            }
            throw $e;
        }
    }

    protected function exportFIFO(
        int $warehouseId,
        int $productId,
        ?int $variantId,
        int $quantity,
        string $referenceType,
        ?int $referenceId = null
    ): void {
        $stockQuery = WarehouseProduct::where('warehouse_id', $warehouseId)
            ->where('product_id', $productId);
        
        // Xử lý variant_id null
        if (is_null($variantId)) {
            $stockQuery->whereNull('variant_id');
        } else {
            $stockQuery->where('variant_id', $variantId);
        }
        
        $currentStock = $stockQuery->value('quantity') ?? 0;

        if ($currentStock < $quantity) {
            throw new Exception("Kho không đủ hàng (còn {$currentStock})");
        }

        $batches = WarehouseBatch::lockForUpdate()
            ->where('warehouse_id', $warehouseId)
            ->where('product_id', $productId)
            ->when(is_null($variantId), fn($q) => $q->whereNull('variant_id'), fn($q) => $q->where('variant_id', $variantId))
            ->where('quantity', '>', 0)
            ->orderByRaw('expired_at IS NULL')
            ->orderBy('expired_at')
            ->orderBy('id')
            ->get();

        // Nếu không có batch nào nhưng có tồn kho, tạo batch mặc định
        if ($batches->isEmpty() && $currentStock > 0) {
            $defaultBatch = WarehouseBatch::lockForUpdate()->create([
                'warehouse_id' => $warehouseId,
                'product_id'   => $productId,
                'variant_id'   => $variantId,
                'batch_code'   => 'DEFAULT-' . date('YmdHis'),
                'expired_at'   => null,
                'import_price' => 0,
                'quantity'     => $currentStock,
            ]);
            $batches = collect([$defaultBatch]);
        }

        $need = $quantity;

        foreach ($batches as $batch) {
            if ($need <= 0) {
                break;
            }

            $take   = min($need, $batch->quantity);
            $before = $batch->quantity;
            $after  = $before - $take;

            $batch->update(['quantity' => $after]);

            $this->createStockTransaction([
                'warehouse_id'    => $warehouseId,
                'product_id'      => $productId,
                'variant_id'      => $variantId,
                'batch_id'        => $batch->id,
                'batch_code'      => $batch->batch_code,
                'type'            => 'export',
                'quantity'        => -$take,
                'before_quantity' => $before,
                'after_quantity'  => $after,
                'reference_type'  => $referenceType,
                'reference_id'    => $referenceId,
            ]);

            $this->syncWarehouseProduct($warehouseId, $productId, $variantId, -$take);
            $need -= $take;
        }

        if ($need > 0) {
            throw new Exception("Xuất kho thất bại, còn thiếu {$need} sản phẩm");
        }
    }

    protected function syncWarehouseProduct(int $warehouseId, int $productId, ?int $variantId, int $change): void
    {
        $row = WarehouseProduct::lockForUpdate()->firstOrCreate(
            [
                'warehouse_id' => $warehouseId,
                'product_id'   => $productId,
                'variant_id'   => $variantId,
            ],
            ['quantity' => 0]
        );

        $newQty = $row->quantity + $change;

        if ($newQty < 0) {
            throw new Exception('Tồn kho âm');
        }

        $row->update(['quantity' => $newQty]);
    }
}
