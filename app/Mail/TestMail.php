<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class TestMail extends Mailable
{
    use Queueable;

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Test email from '.config('app.name'));
    }

    public function content(): Content
    {
        return new Content(htmlString: '<p>Your SMTP settings are working. Password reset OTP emails will be sent from this account.</p>');
    }
}
