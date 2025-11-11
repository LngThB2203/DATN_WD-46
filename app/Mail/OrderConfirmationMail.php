<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function build(): self
    {
        return $this->subject('Xác nhận đơn hàng #' . str_pad((string) $this->order->id, 6, '0', STR_PAD_LEFT))
            ->view('emails.order-confirmation')
            ->with([
                'order' => $this->order,
            ]);
    }
}

