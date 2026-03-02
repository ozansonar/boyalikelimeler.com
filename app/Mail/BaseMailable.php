<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\MailLog;
use App\Models\User;
use App\Services\MailLogService;
use App\Services\SettingService;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\SentMessage;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mime\Email;

abstract class BaseMailable extends Mailable
{
    /**
     * Override send to apply SMTP config + automatic logging.
     */
    public function send($mailer): ?SentMessage
    {
        $this->applySmtpConfig();

        $capturedBody = '';

        $this->withSymfonyMessage(function (Email $message) use (&$capturedBody): void {
            $html = $message->getHtmlBody();
            $capturedBody = is_string($html) && $html !== ''
                ? $html
                : (string) ($message->getTextBody() ?? '');
        });

        $log = $this->createPendingLog();

        try {
            $result = parent::send($mailer);

            if ($log) {
                $this->updateLogBody($log, $capturedBody);
                app(MailLogService::class)->markSent($log);
            }

            return $result;
        } catch (\Throwable $e) {
            if ($log) {
                $this->updateLogBody($log, $capturedBody);
                app(MailLogService::class)->markFailed($log, $e->getMessage());
            }

            throw $e;
        }
    }

    /**
     * Apply SMTP settings from database.
     */
    protected function applySmtpConfig(): void
    {
        try {
            $smtp = app(SettingService::class)->getGroup('smtp');

            if (empty($smtp['host'])) {
                return;
            }

            config([
                'mail.mailers.smtp.host'       => $smtp['host'],
                'mail.mailers.smtp.port'       => (int) ($smtp['port'] ?? 587),
                'mail.mailers.smtp.username'   => $smtp['username'] ?? '',
                'mail.mailers.smtp.password'   => $smtp['password'] ?? '',
                'mail.mailers.smtp.encryption' => ($smtp['encryption'] ?? 'tls') === 'none'
                    ? null
                    : ($smtp['encryption'] ?? 'tls'),
                'mail.from.name'               => $smtp['from_name'] ?? config('mail.from.name'),
                'mail.from.address'             => $smtp['from_email'] ?? config('mail.from.address'),
            ]);
        } catch (\Throwable $e) {
            Log::warning('SMTP config from DB failed, using .env defaults: ' . $e->getMessage());
        }
    }

    /**
     * Create a pending mail log entry.
     */
    private function createPendingLog(): ?MailLog
    {
        try {
            $toEmail = '';
            $toName = null;

            if (!empty($this->to)) {
                $first = $this->to[0];
                $toEmail = $first['address'] ?? '';
                $toName = !empty($first['name']) ? $first['name'] : null;
            }

            return app(MailLogService::class)->create([
                'user_id'        => $this->findUserId($toEmail),
                'to_email'       => $toEmail,
                'to_name'        => $toName,
                'subject'        => $this->subject ?? '',
                'body'           => '',
                'mailable_class' => static::class,
                'status'         => 'pending',
            ]);
        } catch (\Throwable $e) {
            Log::error('Mail log creation failed: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Update log body after message is rendered.
     */
    private function updateLogBody(MailLog $log, string $body): void
    {
        if ($body !== '') {
            $log->updateQuietly(['body' => $body]);
        }
    }

    private function findUserId(string $email): ?int
    {
        if ($email === '') {
            return null;
        }

        return User::where('email', $email)->value('id');
    }
}
