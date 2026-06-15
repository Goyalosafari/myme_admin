<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public int $otp) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Admin Login OTP');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.admin_otp');
    }
}
