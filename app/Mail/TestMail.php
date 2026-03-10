<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class TestMail extends BaseMailable
{
    public function __construct(
        private readonly string $mailSubject,
        private readonly string $mailBody,
    ) {
        $this->connection = 'sync';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mailSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.test',
            with: [
                'body' => $this->mailBody,
            ],
        );
    }
}
