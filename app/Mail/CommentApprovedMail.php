<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Comment;
use App\Models\LiteraryWork;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class CommentApprovedMail extends BaseMailable
{
    public function __construct(
        public readonly Comment $comment,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'İçeriğinize Yorum Yapıldı — Boyalı Kelimeler',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.comment.approved',
        );
    }

    protected function getTemplateVariables(): array
    {
        $contentUrl = $this->comment->commentable_type === LiteraryWork::class
            ? route('literary-works.show', $this->comment->commentable?->slug ?? '')
            : ($this->comment->commentable?->url() ?? '#');

        return [
            '{author_name}'    => $this->comment->commentable?->author?->name ?? '',
            '{content_title}'  => $this->comment->contentTitle(),
            '{commenter_name}' => $this->comment->fullName(),
            '{comment_body}'   => $this->comment->body,
            '{rating}'         => str_repeat('★', $this->comment->rating) . str_repeat('☆', 5 - $this->comment->rating) . ' (' . $this->comment->rating . '/5)',
            '{content_url}'    => $contentUrl,
        ];
    }
}
