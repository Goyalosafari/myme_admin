<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $otp,
        public string $intro = 'Use the code below to verify your account.',
        public int $expiryMinutes = 10,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your MYME verification code',
            // Customer-facing mail should read as the brand, not the internal
            // "Myme Admin" name used for the admin-panel login OTP.
            from: new Address(config('mail.from.address'), 'MYME'),
            replyTo: [new Address(config('mail.from.address'), 'MYME')],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.customer_otp',
            text: 'emails.customer_otp_plain',
        );
    }
}
