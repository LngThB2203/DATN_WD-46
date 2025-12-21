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
}
