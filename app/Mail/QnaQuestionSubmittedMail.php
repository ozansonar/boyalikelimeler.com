<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\QnaQuestion;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class QnaQuestionSubmittedMail extends BaseMailable
{
    public function __construct(
        public readonly QnaQuestion $question,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Yeni Soru Onay Bekliyor — Söz Meydanı',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.qna.question-submitted',
        );
    }

    protected function getTemplateVariables(): array
    {
        return [
            '{question_title}'  => $this->question->title,
            '{author_name}'     => $this->question->user?->name ?? '',
            '{category_name}'   => $this->question->category?->name ?? '',
            '{question_body}'   => $this->question->body,
            '{admin_url}'       => route('admin.qna.questions.index', ['status' => 'pending']),
        ];
    }
}
