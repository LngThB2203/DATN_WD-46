<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

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
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}
