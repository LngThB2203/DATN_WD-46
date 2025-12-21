<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

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

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // Relation HasOne cho ảnh chính
    public function primaryImage()
    {
        return $this->galleries->where('is_primary', true)->first();
    }

    public function primaryImageModel()
    {
        return $this->hasOne(ProductGallery::class)->where('is_primary', true);
    }

    public function allImages()
    {
        return $this->galleries()->orderBy('is_primary', 'desc')->get();
    }

    public function warehouseProducts()
    {
        return $this->hasMany(WarehouseProduct::class, 'product_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function getStockQuantityAttribute()
    {
        return $this->warehouseProducts()->sum('quantity');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function getFormattedPriceAttribute()
    {
        return number_format((float) $this->price, 0, ',', '.') . ' VNĐ';
    }

    public function getFormattedSalePriceAttribute()
    {
        return $this->sale_price
            ? number_format((float) $this->sale_price, 0, ',', '.') . ' VNĐ'
            : null;
    }

    public function getDiscountPercentageAttribute()
    {
        if (! $this->sale_price || $this->sale_price >= $this->price) {
            return 0;
        }

        return round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    public function getAverageRatingAttribute()
    {
        return (float) ($this->reviews()->avg('rating') ?? 0);
    }

    public function getReviewsCountAttribute()
    {
        return (int) ($this->reviews()->count());
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
