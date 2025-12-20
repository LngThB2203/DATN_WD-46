<?php

namespace App\Helpers;

use App\Models\Order;
use App\Helpers\OrderStatusHelper;

class WarehouseLockHelper
{
    public static function isWarehouseLocked(int $warehouseId): bool
    {
        return Order::where('warehouse_id', $warehouseId)
            ->whereIn('order_status', [
                OrderStatusHelper::PREPARING,
                OrderStatusHelper::AWAITING_PICKUP,
            ])
            ->exists();
    }
}
