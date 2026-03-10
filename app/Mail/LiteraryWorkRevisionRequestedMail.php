<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\LiteraryWork;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class LiteraryWorkRevisionRequestedMail extends BaseMailable
{
    public function __construct(
        public readonly LiteraryWork $work,
        public readonly string $reason,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Eseriniz İçin Revize Talebi — Boyalı Kelimeler',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.literary.revision-requested',
        );
    }

    protected function getTemplateVariables(): array
    {
        return [
            '{author_name}' => $this->work->author->name ?? '',
            '{work_title}'  => $this->work->title,
            '{reason}'      => $this->reason,
            '{edit_url}'    => route('myposts.edit', $this->work),
        ];
    }
}
