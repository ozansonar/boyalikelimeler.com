<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ResetPasswordMail extends BaseMailable
{
    public function __construct(
        public readonly User $user,
        public readonly string $resetUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Şifre Sıfırlama Talebi — Boyalı Kelimeler',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.auth.reset-password',
        );
    }
}
