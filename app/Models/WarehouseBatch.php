<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseBatch extends Model
{
    use HasFactory;

    protected $table = 'warehouse_batches';

    protected $fillable = [
        'warehouse_id',
        'product_id',
        'variant_id',
        'batch_code',
        'expired_at',
        'import_price',
        'quantity',
    ];

    protected $dates = [
        'expired_at',
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
}
