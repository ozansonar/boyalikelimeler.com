<?php

declare(strict_types=1);

namespace App\Mail;

use App\Enums\MailLogStatus;
use App\Models\MailLog;
use App\Models\User;
use App\Services\MailLogService;
use App\Services\MailTemplateService;
use App\Services\SettingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\SentMessage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransportFactory;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;
use Symfony\Component\Mime\Email;

abstract class BaseMailable extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    /** @var int[] */
    public array $backoff = [10, 30, 60];

    /** Mail log ID created at queue dispatch time (serialized with the job). */
    public ?int $pendingMailLogId = null;

    private bool $isDebugRedirect = false;
    private string $originalToEmail = '';

    /** @var array<string, string|null> */
    private array $smtpSettings = [];

    /**
     * Override queue to create a pending mail log at dispatch time (before worker runs).
     *
     * This ensures the "Bekliyor" count appears immediately when a mail is queued.
     *
     * @param \Illuminate\Contracts\Queue\Queue $queue
     */
    public function queue($queue): mixed
    {
        $this->createQueuedPendingLog();

        return parent::queue($queue);
    }

    /**
     * Override send to apply SMTP config, debug redirect, CID logo + update existing log.
     */
    public function send($mailer): ?SentMessage
    {
        $this->resolveSubjectFromEnvelope();
        $this->applyTemplateSubjectOverride();
        $this->loadSmtpSettings();
        $mailer = $this->buildConfiguredMailer($mailer);
        $this->applyDebugMode();
        $this->embedMailLogo();

        $capturedBody = '';

        $this->withSymfonyMessage(function (Email $message) use (&$capturedBody): void {
            $html = $message->getHtmlBody();
            $capturedBody = is_string($html) && $html !== ''
                ? $html
                : (string) ($message->getTextBody() ?? '');
        });

        $log = $this->findOrCreatePendingLog();

        try {
            $result = parent::send($mailer);

            if ($log) {
                $this->updateLogOnSend($log, $capturedBody);
                app(MailLogService::class)->markSent($log);
            }

            return $result;
        } catch (\Throwable $e) {
            if ($log) {
                $this->updateLogOnSend($log, $capturedBody);
                app(MailLogService::class)->markFailed($log, $e->getMessage());
            }

            throw $e;
        }
    }

    /**
     * Load SMTP settings from database once.
     */
    private function loadSmtpSettings(): void
    {
        try {
            $this->smtpSettings = app(SettingService::class)->getGroup('smtp');
        } catch (\Throwable $e) {
            Log::warning('SMTP config from DB failed, using .env defaults: ' . $e->getMessage());
            $this->smtpSettings = [];
        }
    }

    /**
     * Build a fresh mailer instance with DB SMTP settings.
     *
     * Uses Symfony EsmtpTransportFactory + Dsn (same as Laravel MailManager)
     * with verify_peer disabled for shared hosting compatibility.
     */
    private function buildConfiguredMailer(object $mailer): object
    {
        $smtp = $this->smtpSettings;

        if (empty($smtp['host'])) {
            return $mailer;
        }

        try {
            $port = (int) ($smtp['port'] ?? 587);
            $scheme = ($port === 465) ? 'smtps' : 'smtp';

            $factory = new EsmtpTransportFactory();
            $transport = $factory->create(new Dsn(
                $scheme,
                $smtp['host'],
                $smtp['username'] ?? null,
                $smtp['password'] ?? null,
                $port,
                ['verify_peer' => 0],
            ));

            $stream = $transport->getStream();
            if ($stream instanceof SocketStream) {
                $stream->setTimeout(30);
            }

            $fromName    = $smtp['from_name'] ?? config('mail.from.name');
            $fromAddress = $smtp['from_email'] ?? config('mail.from.address');

            $this->from($fromAddress, $fromName);

            return new Mailer(
                'db_smtp',
                app('view'),
                $transport,
                app('events'),
            );
        } catch (\Throwable $e) {
            Log::warning('Custom mailer build failed, using default: ' . $e->getMessage());

            return $mailer;
        }
    }

    /**
     * Redirect all recipients to debug emails when developer mode is active.
     */
    private function applyDebugMode(): void
    {
        try {
            $smtp = $this->smtpSettings;

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
            $logoPath = $this->smtpSettings['mail_logo'] ?? '';

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
     * Create a pending log at queue dispatch time (runs synchronously in the request).
     */
    private function createQueuedPendingLog(): void
    {
        try {
            $this->resolveSubjectFromEnvelope();
            $this->applyTemplateSubjectOverride();

            $toEmail = $this->getFirstToAddress();
            $toName = $this->getFirstToName();

            $log = app(MailLogService::class)->create([
                'user_id'        => $this->findUserId($toEmail),
                'to_email'       => $toEmail,
                'to_name'        => $toName,
                'subject'        => $this->subject ?? '',
                'body'           => '',
                'mailable_class' => static::class,
                'status'         => MailLogStatus::Pending->value,
            ]);

            $this->pendingMailLogId = $log->id;
        } catch (\Throwable $e) {
            Log::error('Queued mail log creation failed: ' . $e->getMessage());
        }
    }

    /**
     * Find existing log (created at queue time) or create a new one as fallback.
     */
    private function findOrCreatePendingLog(): ?MailLog
    {
        try {
            if ($this->pendingMailLogId !== null) {
                return MailLog::find($this->pendingMailLogId);
            }

            // Fallback: create log now (for mails sent synchronously without queue)
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
                'status'            => MailLogStatus::Pending->value,
            ]);
        } catch (\Throwable $e) {
            Log::error('Mail log creation failed: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Update log with final send details (body, debug redirect info, subject).
     */
    private function updateLogOnSend(MailLog $log, string $capturedBody): void
    {
        $data = [];

        if ($capturedBody !== '') {
            $data['body'] = $capturedBody;
        }

        // Update subject (may have changed due to debug mode prefix)
        if ($this->subject !== null && $this->subject !== '') {
            $data['subject'] = $this->subject;
        }

        // Update debug redirect info (only known at send time)
        if ($this->isDebugRedirect) {
            $data['to_email'] = $this->getFirstToAddress();
            $data['original_to_email'] = $this->originalToEmail;
            $data['is_debug_redirect'] = true;
        }

        if ($data !== []) {
            $log->updateQuietly($data);
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

    /**
     * Resolve subject from envelope() before send, so logging and debug mode can access it.
     */
    private function resolveSubjectFromEnvelope(): void
    {
        if ($this->subject !== null && $this->subject !== '') {
            return;
        }

        try {
            if (method_exists($this, 'envelope')) {
                $envelope = $this->envelope();
                if ($envelope->subject !== null && $envelope->subject !== '') {
                    $this->subject = $envelope->subject;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Subject resolve from envelope failed: ' . $e->getMessage());
        }
    }

    /**
     * Override subject from DB mail template settings if available.
     */
    private function applyTemplateSubjectOverride(): void
    {
        try {
            $override = app(MailTemplateService::class)->getSubjectByClass(static::class);

            if ($override !== null && $override !== '') {
                $this->subject = $override;
            }
        } catch (\Throwable $e) {
            Log::warning('Template subject override failed: ' . $e->getMessage());
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
