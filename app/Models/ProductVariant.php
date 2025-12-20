<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'size_id',
        'scent_id',
        'concentration_id',
        'sku',
        'image',
        'stock',
        'price_adjustment',
        'gender',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
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

    public function getStockAttribute()
    {
        return $this->warehouseStock->sum('quantity');
    }
    
}
