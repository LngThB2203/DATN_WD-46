<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Discount extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'max_discount_amount',
        'min_order_value',
        'start_date',
        'expiry_date',
        'usage_limit',
        'used_count',
        'active',
        'type',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'min_order_value' => 'decimal:2',
        'start_date' => 'date',
        'expiry_date' => 'date',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'active' => 'boolean',
    ];

    /**
     * Check if discount is valid
     */
    public function isValid(): bool
    {
        if (!$this->active) {
            return false;
        }

        // So sánh theo ngày thay vì ngày + giờ để mã có hiệu lực trọn ngày
        $today = Carbon::today();

        // Check date range (start_date <= today <= expiry_date)
        if ($this->start_date && $today->lt($this->start_date)) {
            return false;
        }

        if ($this->expiry_date && $today->gt($this->expiry_date)) {
            return false;
        }

        // Check usage limit
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Check if discount can be applied to order value
     */
    public function canApplyToOrder(float $orderValue): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($this->min_order_value && $orderValue < $this->min_order_value) {
            return false;
        }

        return true;
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount(float $orderValue): float
    {
        if (!$this->canApplyToOrder($orderValue)) {
            return 0;
        }

        if ($this->discount_type === 'percent') {
            $discount = ($orderValue * $this->discount_value) / 100;

            // Áp dụng giới hạn giảm tối đa nếu có
            if ($this->max_discount_amount !== null) {
                $discount = min($discount, (float) $this->max_discount_amount);
            }

            // Không cho giảm vượt quá giá trị đơn hàng
            return min($discount, $orderValue);
        } else {
            // Fixed amount
            return min($this->discount_value, $orderValue);
        }
    }

    /**
     * Increment usage count
     */
    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }

    /**
     * Scope for active discounts
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function userVouchers()
    {
        return $this->hasMany(UserVoucher::class);
    }

    /**
     * Scope for valid discounts (not expired, within usage limit)
     */
    public function scopeValid($query)
    {
        $today = Carbon::today();

        return $query->where('active', true)
            ->where(function($q) use ($today) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', $today);
            })
            ->where(function($q) use ($today) {
                $q->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>=', $today);
            })
            ->where(function($q) {
                $q->whereNull('usage_limit')
                  ->orWhereRaw('used_count < usage_limit');
            });
    }
}
