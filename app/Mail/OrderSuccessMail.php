<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        // Load đầy đủ relations để hiển thị trong email
        $this->order = $order->load([
            'details.product.galleries',
            'details.variant.size',
            'details.variant.scent',
            'details.variant.concentration',
            'discount',
            'payment'
        ]);
    }

    public function build()
    {
        return $this->subject('Cảm ơn bạn đã đặt hàng tại 46 Perfume Shop')
                    ->view('emails.order-confirmation');
    }
}
