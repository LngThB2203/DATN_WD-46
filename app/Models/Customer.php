<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;
protected $fillable = [
     'user_id',
    'address',
    'gender',
    'membership_level',
];
  public function user()
    {
        return $this->belongsTo(User::class);
    }
}


