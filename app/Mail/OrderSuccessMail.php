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
         $this->order = $order->load([
        'details.product.galleries',
        'details.variant.size',
        'details.variant.scent',
        'details.variant.concentration',
    ]);
    }

  public function build()
{
    return $this->subject('Xác nhận đơn hàng')
        ->view('emails.order-confirmation')
        ->with([
            'order' => $this->order,
        ]);
}
}
