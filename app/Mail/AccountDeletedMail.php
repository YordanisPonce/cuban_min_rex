<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountDeletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $userName,
        public string $title,
        public string $msg,
    ) {
    }

    public function build()
    {
        return $this
            ->subject($this->title)
            ->markdown('emails.account-deleted');
    }
}
