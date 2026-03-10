<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class VerifyEmailMail extends BaseMailable
{
    public function __construct(
        public readonly User $user,
        public readonly string $verificationUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'E-posta Adresinizi Doğrulayın — Boyalı Kelimeler',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.auth.verify-email',
        );
    }

    protected function getTemplateVariables(): array
    {
        return [
            '{user_name}'        => $this->user->name,
            '{verification_url}' => $this->verificationUrl,
        ];
    }
}
