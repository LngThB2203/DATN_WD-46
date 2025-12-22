<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariantConcentration extends Model
{
    protected $table    = 'variants_concentrations';
    protected $fillable = ['name', 'concentration_name'];
    public $timestamps  = true;

    // Accessor để map 'name' thành 'concentration_name'
    public function getConcentrationNameAttribute()
    {
        return $this->attributes['name'] ?? null;
    }

    // Mutator để map 'concentration_name' thành 'name'
    public function setConcentrationNameAttribute($value)
    {
        $this->attributes['name'] = $value;
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'concentration_id');
    }
}
