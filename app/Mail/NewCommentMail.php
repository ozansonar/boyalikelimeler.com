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
}
