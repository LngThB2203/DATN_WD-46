<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'image',
        'price',
        'sale_price',
        'slug',
        'description',
        'category_id',
        'brand',
        'status',
    ];

    protected $casts = [
        'price'      => 'decimal:2',
        'sale_price' => 'decimal:2',
        'status'     => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function galleries(): HasMany
    {
        return $this->hasMany(ProductGallery::class);
    }

    public function primaryImage()
    {
        return $this->galleries->where('is_primary', true)->first();
    }

    public function allImages()
    {
        return $this->galleries()->orderBy('is_primary', 'desc')->get();
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 0, ',', '.') . ' VNĐ';
    }

    public function getFormattedSalePriceAttribute()
    {
        return $this->sale_price ? number_format($this->sale_price, 0, ',', '.') . ' VNĐ' : null;
    }

    public function getDiscountPercentageAttribute()
    {
        if (! $this->sale_price || $this->sale_price >= $this->price) {
            return 0;
        }

        return round((($this->price - $this->sale_price) / $this->price) * 100);
    }
}
