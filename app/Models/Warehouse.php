<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

<<<<<<< Updated upstream
    protected $table = 'warehouse'; // đúng với DB của bạn

    protected $fillable = [
        'warehouse_name',
        'address',
        'manager_id',
        'phone',
    ];

    /**
     * Liên kết tới người quản lý (User)
     */
=======
    protected $table = 'warehouse';

    protected $fillable = ['warehouse_name', 'address', 'manager_id', 'phone'];

>>>>>>> Stashed changes
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
<<<<<<< Updated upstream
=======

    public function products()
    {
        return $this->hasMany(WarehouseProduct::class);
    }
>>>>>>> Stashed changes
}
