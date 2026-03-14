<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\WriterApplication;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class WriterApplicationApprovedMail extends BaseMailable
{
    public function __construct(
        public readonly WriterApplication $application,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Yazar Başvurunuz Onaylandı — Boyalı Kelimeler',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.writer-application.approved',
        );
    }

    protected function getTemplateVariables(): array
    {
        return [
            '{user_name}'    => $this->application->user->name ?? '',
            '{profile_url}'  => $this->application->user->username
                ? route('profile.show', $this->application->user->username)
                : url('/'),
        ];
    }
}
