<?php

namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Brand extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'brands';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'name',
        'origin',
        'description',
        'image'
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