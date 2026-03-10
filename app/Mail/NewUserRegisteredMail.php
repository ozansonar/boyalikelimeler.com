<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewUserRegisteredMail extends BaseMailable
{
    public function __construct(
        public readonly User $newUser,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Yeni Kullanıcı Kaydı — Boyalı Kelimeler',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.auth.new-user-registered',
        );
    }

    protected function getTemplateVariables(): array
    {
        return [
            '{user_name}'     => $this->newUser->name,
            '{user_email}'    => $this->newUser->email,
            '{register_date}' => $this->newUser->created_at?->format('d.m.Y H:i') ?? '-',
            '{admin_url}'     => url(route('admin.dashboard', [], false)),
        ];
    }
}
