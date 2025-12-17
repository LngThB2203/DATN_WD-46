<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DiscountCreatedNotification extends Notification
{
    use Queueable;

    protected $discountData;

    public function __construct($discount)
{
    $this->discountData = [
        'id' => $discount->id,
        'code' => $discount->code,
        'discount_value' => $discount->discount_value,
        'discount_type' => $discount->discount_type,
        // Ép thành string Y-m-d ngay từ đầu
        'expiry_date' => $discount->expiry_date->format('Y-m-d'),
    ];
}


    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
{
    $discountValue = $this->discountData['discount_value'];

    if ($this->discountData['discount_type'] == 'fixed') {
        $messageValue = number_format($discountValue, 0, ',', '.') . ' VNĐ';
    } else {
        $messageValue = $discountValue . '%';
    }

    // Chuyển chuỗi Y-m-d sang Carbon khi cần format hiển thị
    $expiry = \Carbon\Carbon::createFromFormat('Y-m-d', $this->discountData['expiry_date']);

    return [
        'title' => 'Voucher mới 🎁',
        'message' => 'Voucher "' . $this->discountData['code'] . '" giảm ' . $messageValue . ' vừa được phát hành. HSD: ' . $expiry->format('d/m/Y'),
        'discount_id' => $this->discountData['id'],
        
    ];
}

}
