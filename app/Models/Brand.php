<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $table = 'brands';

    protected $fillable = [
        'name',
        'origin',
        'description',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id', 'id');
    }


    public static function getAllBrands()
    {
        return self::orderByDesc('id')->get();
    }


    public static function getBrandById($id)
    {
        return self::findOrFail($id);
    }


    public static function insertBrand($data)
    {
        return self::create($data);
    }

    
    public static function updateBrand($id, $data)
    {
        $brand = self::findOrFail($id);
        $brand->update($data);
        return $brand;
    }

    

    
    public static function deleteBrand($id)
    {
        $brand = self::findOrFail($id);

        // Nếu còn sản phẩm thì không cho xóa
        if ($brand->products()->exists()) {
            return false;
        }

        return $brand->delete();
    }
}
