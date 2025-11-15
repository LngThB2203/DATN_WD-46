<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Discount extends Model
{
    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'min_order_value',
        'start_date',
        'expiry_date',
        'usage_limit',
        'used_count',
        'active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
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

        $now = Carbon::now();

        // Check date range
        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }

        if ($this->expiry_date && $now->gt($this->expiry_date)) {
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
            // Don't allow discount to exceed order value
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

    /**
     * Scope for valid discounts (not expired, within usage limit)
     */
    public function scopeValid($query)
    {
        $now = Carbon::now();
        return $query->where('active', true)
            ->where(function($q) use ($now) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function($q) use ($now) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', $now);
            })
            ->where(function($q) {
                $q->whereNull('usage_limit')
                  ->orWhereRaw('used_count < usage_limit');
            });
    }
}
