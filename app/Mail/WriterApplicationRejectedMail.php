<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\WriterApplication;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class WriterApplicationRejectedMail extends BaseMailable
{
    public function __construct(
        public readonly WriterApplication $application,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Yazar Başvurunuz Hakkında — Boyalı Kelimeler',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.writer-application.rejected',
        );
    }

    protected function getTemplateVariables(): array
    {
        return [
            '{user_name}'   => $this->application->user->name ?? '',
            '{admin_note}'  => $this->application->admin_note ?? '',
        ];
    }
}
