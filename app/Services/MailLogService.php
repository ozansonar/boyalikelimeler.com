<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\MailLogStatus;
use App\Models\MailLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

final class MailLogService
{
    /**
     * @return array<string, int>
     */
    public function getAdminStats(): array
    {
        return Cache::remember('admin.mail_logs.stats', 300, function (): array {
            $counts = MailLog::selectRaw("status, COUNT(*) as cnt")
                ->groupBy('status')
                ->pluck('cnt', 'status');

            return [
                'total'   => (int) $counts->sum(),
                'sent'    => (int) ($counts[MailLogStatus::Sent->value] ?? 0),
                'failed'  => (int) ($counts[MailLogStatus::Failed->value] ?? 0),
                'pending' => (int) ($counts[MailLogStatus::Pending->value] ?? 0),
            ];
        });
    }

    /**
     * @return array<string, int>
     */
    public function getStatusCounts(): array
    {
        return $this->getAdminStats();
    }

    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator
    {
        $query = MailLog::with('user');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search): void {
                $q->where('to_email', 'like', "%{$search}%")
                  ->orWhere('to_name', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function find(int $id): ?MailLog
    {
        return MailLog::with('user')->find($id);
    }

    public function create(array $data): MailLog
    {
        $log = MailLog::create($data);
        $this->clearCache();

        return $log;
    }

    public function markSent(MailLog $log): void
    {
        $log->update([
            'status'  => MailLogStatus::Sent,
            'sent_at' => now(),
        ]);
        $this->clearCache();
    }

    public function markFailed(MailLog $log, string $error): void
    {
        $log->update([
            'status'        => MailLogStatus::Failed,
            'error_message' => $error,
        ]);
        $this->clearCache();
    }

    /**
     * Resend an email using the stored HTML body from the log.
     */
    public function resend(MailLog $log): MailLog
    {
        $newLog = $this->create([
            'user_id'        => $log->user_id,
            'to_email'       => $log->original_to_email ?: $log->to_email,
            'to_name'        => $log->to_name,
            'subject'        => $log->subject,
            'body'           => $log->body,
            'mailable_class' => $log->mailable_class,
            'status'         => MailLogStatus::Pending->value,
        ]);

        try {
            $settingService = app(SettingService::class);
            $smtp = $settingService->getGroup('smtp');
            $fromEmail = $smtp['from_email'] ?? config('mail.from.address');
            $fromName = $smtp['from_name'] ?? config('mail.from.name');

            Mail::html($log->body ?? '', function ($message) use ($newLog, $fromEmail, $fromName): void {
                $message->to($newLog->to_email, $newLog->to_name)
                    ->from($fromEmail, $fromName)
                    ->subject($newLog->subject ?? '(Konu yok)');
            });

            $this->markSent($newLog);
        } catch (\Throwable $e) {
            $this->markFailed($newLog, $e->getMessage());
        }

        return $newLog;
    }

    public function delete(MailLog $log): void
    {
        $log->delete();
        $this->clearCache();
    }

    private function clearCache(): void
    {
        Cache::forget('admin.mail_logs.stats');
    }
}
