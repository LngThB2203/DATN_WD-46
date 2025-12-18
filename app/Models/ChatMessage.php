<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    protected $fillable = [
        'user_id', 
        'guest_token', 
        'sender', 
        'message', 
        'payload',
    ];

    protected $casts = [
    'payload' => 'json',
    ];

  
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
