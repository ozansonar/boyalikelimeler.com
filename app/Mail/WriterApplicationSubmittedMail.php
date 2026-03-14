<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\WriterApplication;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class WriterApplicationSubmittedMail extends BaseMailable
{
    public function __construct(
        public readonly WriterApplication $application,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Yeni Yazar Başvurusu — Boyalı Kelimeler',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.writer-application.submitted',
        );
    }

    protected function getTemplateVariables(): array
    {
        return [
            '{user_name}'    => $this->application->user->name ?? '',
            '{user_email}'   => $this->application->user->email ?? '',
            '{submit_date}'  => $this->application->created_at?->format('d.m.Y H:i') ?? '-',
            '{admin_url}'    => route('admin.writer-applications.show', $this->application->id),
        ];
    }
}
