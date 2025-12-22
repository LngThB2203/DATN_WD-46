<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_id',
        'discount_id',
        'payment_id',
        'order_status',
        'total_price',
        'shipping_address',
        'shipping_cost',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_province',
        'shipping_district',
        'shipping_ward',
        'shipping_address_line',
        'customer_note',
        'subtotal',
        'discount_total',
        'grand_total',
        'payment_method',
        'cancellation_reason',
        'cancelled_at',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
