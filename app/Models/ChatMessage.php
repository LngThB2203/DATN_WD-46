<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = [
        'user_id', 'sender', 'message', 'payload',
    ];

    protected $casts = [
        'payload' => 'json',
    ];
}
