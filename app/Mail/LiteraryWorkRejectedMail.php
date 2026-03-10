<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\LiteraryWork;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class LiteraryWorkRejectedMail extends BaseMailable
{
    public function __construct(
        public readonly LiteraryWork $work,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Eseriniz Hakkında Bilgilendirme — Boyalı Kelimeler',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.literary.rejected',
        );
    }

    protected function getTemplateVariables(): array
    {
        return [
            '{author_name}' => $this->work->author->name ?? '',
            '{work_title}'  => $this->work->title,
            '{works_url}'   => route('myposts.index'),
        ];
    }
}
