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
        return $this->attributes['size_name'] ?? ($this->attributes['name'] ?? null);
    }

    // Mutator để map 'size_name' thành 'name'
    public function setSizeNameAttribute($value)
    {
        $this->attributes['size_name'] = $value;
    }

    public function getNameAttribute()
    {
        return $this->attributes['size_name'] ?? null;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['size_name'] = $value;
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'size_id');
    }
}
