<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\WriterApplication;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class WriterApplicationReceivedMail extends BaseMailable
{
    public function __construct(
        public readonly WriterApplication $application,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Yazar Başvurunuz Alındı — Boyalı Kelimeler',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.writer-application.received',
        );
    }

    protected function getTemplateVariables(): array
    {
        return [
            '{user_name}'    => $this->application->user->name ?? '',
            '{submit_date}'  => $this->application->created_at?->format('d.m.Y H:i') ?? '-',
        ];
    }
}
