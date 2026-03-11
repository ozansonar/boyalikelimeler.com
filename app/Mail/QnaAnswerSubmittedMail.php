<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\QnaAnswer;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class QnaAnswerSubmittedMail extends BaseMailable
{
    public function __construct(
        public readonly QnaAnswer $answer,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Yeni Cevap Onay Bekliyor — Söz Meydanı',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.qna.answer-submitted',
        );
    }

    protected function getTemplateVariables(): array
    {
        return [
            '{question_title}' => $this->answer->question?->title ?? '',
            '{author_name}'    => $this->answer->user?->name ?? '',
            '{answer_body}'    => $this->answer->body,
            '{admin_url}'      => route('admin.qna.answers.index', ['status' => 'pending']),
        ];
    }
}
