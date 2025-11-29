<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariantSize extends Model
{
    protected $table    = 'variants_sizes';
    protected $fillable = ['size_name'];
    public $timestamps  = true;

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'size_id');
    }
}
