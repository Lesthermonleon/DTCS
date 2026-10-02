<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * OTP Notification Mail — DTCS HIMS login verification code.
 *
 * SECURITY: This mailable receives the PLAINTEXT OTP only for email delivery.
 * The plaintext code is never stored or logged — it is only used to render this email.
 * The stored record only contains the bcrypt hash in the otp_verifications table.
 */
class OtpNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param string $otpCode     Plaintext 6-digit OTP (used only for rendering — never stored)
     * @param int    $expiresMinutes  Minutes until OTP expires (for display)
     */
    public function __construct(
        private readonly string $otpCode,
        private readonly int    $expiresMinutes = 3,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'DTCS HIMS — Your Login Verification Code',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp-notification',
            with: [
                'otpCode'       => $this->otpCode,
                'expiresMinutes' => $this->expiresMinutes,
            ],
        );
    }
}
