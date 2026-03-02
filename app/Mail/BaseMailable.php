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
    private bool $isDebugRedirect = false;
    private string $originalToEmail = '';

    /**
     * Override send to apply SMTP config, debug redirect, CID logo + automatic logging.
     */
    public function send($mailer): ?SentMessage
    {
        $this->applySmtpConfig();
        $this->applyDebugMode();
        $this->embedMailLogo();

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
                'mail.from.address'            => $smtp['from_email'] ?? config('mail.from.address'),
            ]);
        } catch (\Throwable $e) {
            Log::warning('SMTP config from DB failed, using .env defaults: ' . $e->getMessage());
        }
    }

    /**
     * Redirect all recipients to debug emails when developer mode is active.
     */
    private function applyDebugMode(): void
    {
        try {
            $smtp = app(SettingService::class)->getGroup('smtp');

            if (($smtp['send_mode'] ?? 'normal') !== 'developer') {
                return;
            }

            $debugEmails = $smtp['debug_emails'] ?? '';
            if ($debugEmails === '') {
                return;
            }

            $debugAddresses = array_filter(
                array_map('trim', explode(',', $debugEmails)),
                fn (string $email): bool => filter_var($email, FILTER_VALIDATE_EMAIL) !== false
            );

            if ($debugAddresses === []) {
                return;
            }

            $this->originalToEmail = $this->getFirstToAddress();
            $this->isDebugRedirect = true;

            $this->to = [];
            foreach ($debugAddresses as $address) {
                $this->to($address);
            }

            $originalSubject = $this->subject ?? '(no subject)';
            $this->subject = '[DEBUG → ' . $this->originalToEmail . '] ' . $originalSubject;
        } catch (\Throwable $e) {
            Log::warning('Debug mode redirect failed: ' . $e->getMessage());
        }
    }

    /**
     * Embed mail logo as CID attachment so it displays in all email clients.
     */
    private function embedMailLogo(): void
    {
        try {
            $smtp = app(SettingService::class)->getGroup('smtp');
            $logoPath = $smtp['mail_logo'] ?? '';

            if ($logoPath === '') {
                return;
            }

            $fullPath = public_path('uploads/' . $logoPath);

            if (!file_exists($fullPath)) {
                return;
            }

            $this->withSymfonyMessage(function (Email $message) use ($fullPath): void {
                $message->embedFromPath($fullPath, 'mail-logo');
            });
        } catch (\Throwable $e) {
            Log::warning('Mail logo embed failed: ' . $e->getMessage());
        }
    }

    /**
     * Create a pending mail log entry.
     */
    private function createPendingLog(): ?MailLog
    {
        try {
            $toEmail = $this->getFirstToAddress();
            $toName = $this->getFirstToName();

            return app(MailLogService::class)->create([
                'user_id'           => $this->findUserId($this->isDebugRedirect ? $this->originalToEmail : $toEmail),
                'to_email'          => $toEmail,
                'to_name'           => $toName,
                'original_to_email' => $this->isDebugRedirect ? $this->originalToEmail : null,
                'subject'           => $this->subject ?? '',
                'body'              => '',
                'mailable_class'    => static::class,
                'is_debug_redirect' => $this->isDebugRedirect,
                'status'            => 'pending',
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

    private function getFirstToAddress(): string
    {
        if (empty($this->to)) {
            return '';
        }

        return $this->to[0]['address'] ?? '';
    }

    private function getFirstToName(): ?string
    {
        if (empty($this->to)) {
            return null;
        }

        $name = $this->to[0]['name'] ?? '';

        return $name !== '' ? $name : null;
    }

    private function findUserId(string $email): ?int
    {
        if ($email === '') {
            return null;
        }

        return User::where('email', $email)->value('id');
    }
}
