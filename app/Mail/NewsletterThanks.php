<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterThanks extends Mailable
{
    use Queueable, SerializesModels;

    public function build()
    {
        return $this->subject('Cảm ơn bạn đã đăng ký nhận tin!')
                    ->view('emails.newsletter_thanks');
    }
}
