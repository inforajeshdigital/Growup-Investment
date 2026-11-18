<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PasswordOtpMail extends Mailable
{
    use Queueable;

    public function __construct(public string $otp, public int $minutes) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Password reset OTP: '.$this->otp);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.otp');
    }
}
