<?php

namespace App\Services;

use App\Models\StockTransaction;
use App\Models\WarehouseBatch;
use App\Models\WarehouseProduct;
use Exception;
use Illuminate\Support\Facades\DB;

class StockService
{
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

            StockTransaction::create([
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

    // Xuất kho chỉ qua đơn hàng
    public function exportByOrder($order): void
    {
        if ($this->isOrderExported($order->id)) return;

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
        if (!$this->isOrderExported($order->id)) return;

        if (StockTransaction::where('reference_type', 'order_cancel')
            ->where('reference_id', $order->id)
            ->exists()) return;

        DB::transaction(function () use ($order) {
            $exports = StockTransaction::where('reference_type', 'order')
                ->where('reference_id', $order->id)
                ->where('type', 'export')
                ->get();

            foreach ($exports as $log) {
                $batch = WarehouseBatch::lockForUpdate()->find($log->batch_id);
                if (!$batch) continue;

                $restore = abs($log->quantity);
                $before  = $batch->quantity;
                $after   = $before + $restore;

                $batch->update(['quantity' => $after]);

                StockTransaction::create([
                    'warehouse_id'    => $batch->warehouse_id,
                    'product_id'      => $batch->product_id,
                    'variant_id'      => $batch->variant_id,
                    'batch_id'        => $batch->id,
                    'batch_code'      => $batch->batch_code,
                    'type'            => 'import',
                    'quantity'        => $restore,
                    'before_quantity' => $before,
                    'after_quantity'  => $after,
                    'reference_type'  => 'order_cancel',
                    'reference_id'    => $order->id,
                ]);

                $this->syncWarehouseProduct(
                    $batch->warehouse_id,
                    $batch->product_id,
                    $batch->variant_id,
                    $restore
                );
            }
        });
    }

    public function isOrderExported(int $orderId): bool
    {
        return StockTransaction::where('reference_type', 'order')
            ->where('reference_id', $orderId)
            ->where('type', 'export')
            ->exists();
    }

    protected function exportFIFO(
        int $warehouseId,
        int $productId,
        ?int $variantId,
        int $quantity,
        string $referenceType,
        ?int $referenceId = null
    ): void {
        $currentStock = WarehouseProduct::where([
            'warehouse_id' => $warehouseId,
            'product_id'   => $productId,
            'variant_id'   => $variantId,
        ])->value('quantity') ?? 0;

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
            if ($need <= 0) break;

            $take   = min($need, $batch->quantity);
            $before = $batch->quantity;
            $after  = $before - $take;

            $batch->update(['quantity' => $after]);

            StockTransaction::create([
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
