<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DiscountCreatedNotification extends Notification
{
    use Queueable;

    protected $discount;

    public function __construct($discount)
    {
        $this->discount = $discount;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Voucher mới 🎁',
            'message' => 'Voucher "' . $this->discount->code . '" giảm '
                . $this->discount->value . '% vừa được phát hành',
            'discount_id' => $this->discount->id,
        ];
    }
}
