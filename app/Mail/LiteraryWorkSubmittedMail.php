<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\LiteraryWork;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class LiteraryWorkSubmittedMail extends BaseMailable
{
    public function __construct(
        public readonly LiteraryWork $work,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Yeni Edebiyat Eseri Gönderildi — Boyalı Kelimeler',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.literary.submitted',
        );
    }
}
