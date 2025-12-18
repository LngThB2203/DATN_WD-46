<?php

namespace App\Listeners;

use App\Models\ChatMessage;
use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cookie;

class MergeGuestChatAfterLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(login $event): void
    {
        $guestToken = request()->cookie('chat_token');

        if ($guestToken) {
            ChatMessage::where('guest_token', $guestToken)
                ->update([
                    'user_id' => $event->user->id,
                    'guest_token' => null
                ]);

            cookie()->queue(cookie()->forget('chat_token'));
        }
    }
}
