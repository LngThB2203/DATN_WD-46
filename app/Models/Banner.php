<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $table = 'banners';

   protected $fillable = ['image', 'link', 'start_date', 'end_date', 'created_by'];
protected $casts = [
    'start_date' => 'date',
    'end_date' => 'date',
];
public function index()
{
    // Lấy banner đang hoạt động (active)
    $heroBanners = Banner::active()->get();

    return view('client.home', compact('heroBanners'));
}

    // Mối quan hệ tới người tạo (user)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scope banner đang hoạt động
    public function scopeActive($query)
{
    $today = now()->toDateString();
    return $query
        ->where(function ($q) use ($today) {
            $q->whereNull('start_date')->orWhere('start_date', '<=', $today);
        })
        ->where(function ($q) use ($today) {
            $q->whereNull('end_date')->orWhere('end_date', '>=', $today);
        });
}

protected static function booted()
{
    static::deleting(function ($banner) {
        if ($banner->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($banner->image)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($banner->image);
        }
    });
}


}
