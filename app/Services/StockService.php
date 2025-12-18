<?php
namespace App\Services;

use App\Models\StockTransaction;
use App\Models\WarehouseProduct;
use Illuminate\Support\Facades\DB;

class StockService
{
    // Kiểm tra đơn đã xuất kho chưa
    public function isOrderExported(int $orderId): bool
    {
        return StockTransaction::where('reference_type', 'order')
            ->where('reference_id', $orderId)
            ->exists();
    }

    // Export FIFO
    private function exportFIFO(
        int $warehouseId,
        int $productId,
        ?int $variantId,
        int $quantity,
        string $referenceType,
        ?int $referenceId = null
    ) {
        $batches = WarehouseProduct::lockForUpdate()
            ->where('warehouse_id', $warehouseId)
            ->where('product_id', $productId)
            ->where('variant_id', $variantId)
            ->where('quantity', '>', 0)
            ->orderBy('expired_at')
            ->get();

        $totalStock = $batches->sum('quantity');

        if ($totalStock < $quantity) {
            throw new \Exception("Kho {$warehouseId} không đủ hàng. Còn lại $totalStock sản phẩm.");
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

            StockTransaction::create([
                'warehouse_id'    => $batch->warehouse_id,
                'product_id'      => $productId,
                'variant_id'      => $variantId,
                'batch_code'      => $batch->batch_code,
                'type'            => 'export',
                'quantity'        => -$take,
                'before_quantity' => $before,
                'after_quantity'  => $after,
                'reference_type'  => $referenceType,
                'reference_id'    => $referenceId,
            ]);

            $need -= $take;
        }
    }
    // Nhập kho thủ công
    public function import(array $item, string $referenceType, ?int $referenceId = null)
    {
        DB::transaction(function () use ($item, $referenceType, $referenceId) {
            $stock = WarehouseProduct::lockForUpdate()->firstOrCreate(
                [
                    'warehouse_id' => $item['warehouse_id'],
                    'product_id'   => $item['product_id'],
                    'variant_id'   => $item['variant_id'],
                    'batch_code'   => $item['batch_code'],
                ],
                [
                    'quantity'   => 0,
                    'expired_at' => $item['expired_at'] ?? null,
                ]
            );

            $before = $stock->quantity;
            $after  = $before + $item['quantity'];
            $stock->update(['quantity' => $after]);

            StockTransaction::create([
                'warehouse_id'    => $stock->warehouse_id,
                'product_id'      => $stock->product_id,
                'variant_id'      => $stock->variant_id,
                'batch_code'      => $stock->batch_code,
                'type'            => 'import',
                'quantity'        => $item['quantity'],
                'before_quantity' => $before,
                'after_quantity'  => $after,
                'reference_type'  => $referenceType,
                'reference_id'    => $referenceId,
            ]);
        });
    }

    // Xuất kho theo đơn hàng
    public function exportByOrder($order)
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

    // Hoàn kho khi hủy đơn
    public function cancelOrder($order)
    {
        if (! $this->isOrderExported($order->id)) {
            return;
        }

        DB::transaction(function () use ($order) {
            $logs = StockTransaction::where('reference_type', 'order')
                ->where('reference_id', $order->id)
                ->where('type', 'export')
                ->get();

            foreach ($logs as $log) {
                $stock = WarehouseProduct::lockForUpdate()
                    ->where([
                        'warehouse_id' => $log->warehouse_id,
                        'product_id'   => $log->product_id,
                        'variant_id'   => $log->variant_id,
                        'batch_code'   => $log->batch_code,
                    ])
                    ->first();

                if (! $stock) {
                    continue;
                }

                $before = $stock->quantity;
                $after  = $before + abs($log->quantity);

                $stock->update(['quantity' => $after]);

                StockTransaction::create([
                    'warehouse_id'    => $log->warehouse_id,
                    'product_id'      => $log->product_id,
                    'variant_id'      => $log->variant_id,
                    'batch_code'      => $log->batch_code,
                    'type'            => 'import',
                    'quantity'        => abs($log->quantity),
                    'before_quantity' => $before,
                    'after_quantity'  => $after,
                    'reference_type'  => 'order_cancel',
                    'reference_id'    => $order->id,
                ]);
            }
        });
    }
}
