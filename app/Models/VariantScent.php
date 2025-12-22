<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariantScent extends Model
{
    protected $table    = 'variants_scents';
    protected $fillable = ['name', 'scent_name'];
    public $timestamps  = true;

    // Accessor để map 'name' thành 'scent_name'
    public function getScentNameAttribute()
    {
        return $this->attributes['scent_name'] ?? ($this->attributes['name'] ?? null);
    }

    // Mutator để map 'scent_name' thành 'name'
    public function setScentNameAttribute($value)
    {
        $this->attributes['scent_name'] = $value;
    }

    public function getNameAttribute()
    {
        return $this->attributes['scent_name'] ?? null;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['scent_name'] = $value;
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'scent_id');
    }
}
