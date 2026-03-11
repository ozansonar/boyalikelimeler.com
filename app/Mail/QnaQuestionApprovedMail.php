<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\QnaQuestion;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class QnaQuestionApprovedMail extends BaseMailable
{
    public function __construct(
        public readonly QnaQuestion $question,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Sorunuz Onaylandı — Söz Meydanı',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.qna.question-approved',
        );
    }

    protected function getTemplateVariables(): array
    {
        return [
            '{question_title}' => $this->question->title,
            '{author_name}'    => $this->question->user?->name ?? '',
            '{category_name}'  => $this->question->category?->name ?? '',
            '{question_url}'   => route('qna.show', [
                'categorySlug' => $this->question->category?->slug ?? '',
                'questionSlug' => $this->question->slug,
            ]),
        ];
    }
}
