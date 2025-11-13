<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariantScent extends Model
{
    protected $table    = 'variants_scents';
    protected $fillable = ['scent_name'];
    public $timestamps  = true;

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'scent_id');
    }
}
