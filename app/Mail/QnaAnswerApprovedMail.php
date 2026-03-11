<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\QnaAnswer;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class QnaAnswerApprovedMail extends BaseMailable
{
    public function __construct(
        public readonly QnaAnswer $answer,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cevabınız Onaylandı — Söz Meydanı',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.qna.answer-approved',
        );
    }

    protected function getTemplateVariables(): array
    {
        return [
            '{question_title}' => $this->answer->question?->title ?? '',
            '{author_name}'    => $this->answer->user?->name ?? '',
            '{question_url}'   => route('qna.show', [
                'categorySlug' => $this->answer->question?->category?->slug ?? '',
                'questionSlug' => $this->answer->question?->slug ?? '',
            ]),
        ];
    }
}
