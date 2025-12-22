<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseProduct extends Model
{
    use HasFactory;

    protected $table = 'warehouse_products';

    protected $fillable = [
        'warehouse_id',
        'product_id',
        'variant_id',
        'quantity',
        'min_stock_threshold',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function batches()
    {
        return $this->hasMany(WarehouseBatch::class, 'product_id', 'product_id')
            ->whereColumn('warehouse_batches.variant_id', 'warehouse_products.variant_id')
            ->whereColumn('warehouse_batches.warehouse_id', 'warehouse_products.warehouse_id');
    }

    /**
     * Kiểm tra xem tồn kho có thấp không
     * @param int $threshold Ngưỡng tồn kho thấp (mặc định 10)
     * @return bool
     */
    public function isLowStock(?int $threshold = null): bool
    {
        $threshold = $threshold ?? $this->min_stock_threshold ?? 10;
        return $this->quantity <= $threshold;
    }
}
