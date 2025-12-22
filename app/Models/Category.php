<?php
namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_name',
        'slug',
        'description',
        'parent_id',
        'image',
        'status',
    ];

    /**
     * Get the parent category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the child categories.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get the products for the category.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Scope a query to only include root categories.
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope a query to only include child categories.
     */
    public function scopeChildren($query)
    {
        return $query->whereNotNull('parent_id');
    }

    /**
     * Handle slug creation and updating.
     */
    protected static function booted()
    {
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->category_name);
            }

            $original = $category->slug;
            $count    = 1;
            while (Category::where('slug', $category->slug)->exists()) {
                $category->slug = "{$original}-{$count}";
                $count++;
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('category_name')) {
                $category->slug = Str::slug($category->category_name);

                $original = $category->slug;
                $count    = 1;
                while (
                    Category::where('slug', $category->slug)
                    ->where('id', '!=', $category->id)
                    ->exists()
                ) {
                    $category->slug = "{$original}-{$count}";
                    $count++;
                }
            }
        });
    }
}
