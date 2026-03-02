<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\MailLog;
use App\Models\User;
use App\Services\MailLogService;
use Illuminate\Events\Dispatcher;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class MailEventSubscriber
{
    public function __construct(
        private readonly MailLogService $mailLogService,
    ) {}

    public function handleMessageSending(MessageSending $event): void
    {
        try {
            $message = $event->message;
            $to = $message->getTo();
            $toAddress = $this->getFirstAddress($to);
            $toName = $this->getFirstName($to);

            $subject = $message->getSubject() ?? '';
            $body = $this->extractBody($message);

            $mailableClass = $event->data['__laravel_notification'] ?? null;
            if ($mailableClass === null && isset($event->data['__laravel_notification_id'])) {
                $mailableClass = $event->data['__laravel_notification_class'] ?? null;
            }

            $userId = $this->findUserId($toAddress);

            $this->mailLogService->create([
                'user_id'        => $userId,
                'to_email'       => $toAddress,
                'to_name'        => $toName,
                'subject'        => $subject,
                'body'           => $body,
                'mailable_class' => $mailableClass,
                'status'         => 'pending',
            ]);
        } catch (\Throwable $e) {
            Log::error('Mail log creation failed: ' . $e->getMessage());
        }
    }

    public function handleMessageSent(MessageSent $event): void
    {
        try {
            $message = $event->message;
            $toAddress = $this->getFirstAddress($message->getTo());
            $subject = $message->getSubject() ?? '';

            $log = MailLog::where('to_email', $toAddress)
                ->where('subject', $subject)
                ->where('status', 'pending')
                ->latest()
                ->first();

            if ($log) {
                $this->mailLogService->markSent($log);
            }
        } catch (\Throwable $e) {
            Log::error('Mail log update (sent) failed: ' . $e->getMessage());
        }
    }

    /**
     * Extract body content from the email message.
     */
    private function extractBody(Email $message): string
    {
        $html = $message->getHtmlBody();
        if (is_string($html) && $html !== '') {
            return $html;
        }

        $text = $message->getTextBody();
        if (is_string($text) && $text !== '') {
            return $text;
        }

        return '';
    }

    /**
     * @param list<Address> $addresses
     */
    private function getFirstAddress(array $addresses): string
    {
        if (empty($addresses)) {
            return '';
        }

        $first = $addresses[0];

        return $first instanceof Address ? $first->getAddress() : (string) $first;
    }

    /**
     * @param list<Address> $addresses
     */
    private function getFirstName(array $addresses): ?string
    {
        if (empty($addresses)) {
            return null;
        }

        $first = $addresses[0];

        if ($first instanceof Address) {
            $name = $first->getName();

            return $name !== '' ? $name : null;
        }

        return null;
    }

    private function findUserId(string $email): ?int
    {
        if ($email === '') {
            return null;
        }

        $user = User::where('email', $email)->first(['id']);

        return $user?->id;
    }

    /**
     * @return array<string, string>
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            MessageSending::class => 'handleMessageSending',
            MessageSent::class    => 'handleMessageSent',
        ];
    }
}
