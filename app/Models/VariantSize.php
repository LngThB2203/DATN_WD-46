<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariantSize extends Model
{
    protected $table    = 'variants_sizes';
    protected $fillable = ['name', 'size_name'];
    public $timestamps  = true;

    // Accessor để map 'name' thành 'size_name'
    public function getSizeNameAttribute()
    {
        return $this->attributes['name'] ?? null;
    }

    // Mutator để map 'size_name' thành 'name'
    public function setSizeNameAttribute($value)
    {
        $this->attributes['name'] = $value;
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'size_id');
    }
}
