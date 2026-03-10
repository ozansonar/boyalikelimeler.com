<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Comment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewCommentMail extends BaseMailable
{
    public function __construct(
        public readonly Comment $comment,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Yeni Yorum Bekliyor — Boyalı Kelimeler',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.comment.new-comment',
        );
    }

    protected function getTemplateVariables(): array
    {
        return [
            '{content_title}'   => $this->comment->contentTitle(),
            '{commenter_name}'  => $this->comment->fullName(),
            '{commenter_email}' => $this->comment->commenterEmail() ?? '',
            '{comment_body}'    => $this->comment->body,
            '{rating}'          => str_repeat('★', $this->comment->rating) . str_repeat('☆', 5 - $this->comment->rating) . ' (' . $this->comment->rating . '/5)',
            '{admin_url}'       => route('admin.comments.index', ['status' => 'pending']),
        ];
    }
}
