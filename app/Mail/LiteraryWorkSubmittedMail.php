<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\LiteraryWork;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class LiteraryWorkSubmittedMail extends BaseMailable
{
    public function __construct(
        public readonly LiteraryWork $work,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Yeni Edebiyat Eseri Gönderildi — Boyalı Kelimeler',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.literary.submitted',
        );
    }

    protected function getTemplateVariables(): array
    {
        return [
            '{author_name}'   => $this->work->author->name ?? '',
            '{work_title}'    => $this->work->title,
            '{category_name}' => $this->work->category->name ?? '',
            '{submit_date}'   => $this->work->created_at?->format('d.m.Y H:i') ?? '-',
            '{admin_url}'     => route('admin.literary-works.show', $this->work->id),
        ];
    }
}
