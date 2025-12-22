<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'size_id',
        'scent_id',
        'concentration_id',
        'sku',
        'image',
        'price_adjustment',
        'gender',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function size()
    {
        return $this->belongsTo(VariantSize::class, 'size_id');
    }

    public function scent()
    {
        return $this->belongsTo(VariantScent::class, 'scent_id');
    }

    public function concentration()
    {
        return $this->belongsTo(VariantConcentration::class, 'concentration_id');
    }

    public function warehouseStock()
    {
        return $this->hasMany(WarehouseProduct::class, 'variant_id', 'id');
    }

    public function getTotalStockAttribute()
    {
        return $this->warehouseStock->sum('quantity');
    }
}
