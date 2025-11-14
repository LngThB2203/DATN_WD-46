<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariantConcentration extends Model
{
    protected $table    = 'variants_concentrations';
    protected $fillable = ['concentration_name'];
    public $timestamps  = true;

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'concentration_id');
    }
}
