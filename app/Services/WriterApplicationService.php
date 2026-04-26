<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\RoleSlug;
use App\Enums\WriterApplicationStatus;
use App\Mail\WriterApplicationApprovedMail;
use App\Mail\WriterApplicationReceivedMail;
use App\Mail\WriterApplicationRejectedMail;
use App\Mail\WriterApplicationSubmittedMail;
use App\Models\Role;
use App\Models\User;
use App\Models\WriterApplication;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

final class WriterApplicationService
{
    /**
     * Submit a new writer application.
     */
    public function store(User $user, string $motivation): WriterApplication
    {
        return DB::transaction(function () use ($user, $motivation): WriterApplication {
            $application = WriterApplication::create([
                'user_id'    => $user->id,
                'motivation' => $motivation,
                'status'     => WriterApplicationStatus::Pending,
            ]);

            $this->clearPendingCountCache();

            // Notify the applicant
            Mail::to($user->email)->send(new WriterApplicationReceivedMail($application));

            // Notify admins
            $this->notifyAdmins($application);

            return $application;
        });
    }

    /**
     * Approve a writer application and upgrade user role.
     */
    public function approve(WriterApplication $application, User $reviewer): WriterApplication
    {
        return DB::transaction(function () use ($application, $reviewer): WriterApplication {
            $application->update([
                'status'      => WriterApplicationStatus::Approved,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
            ]);

            // Upgrade user role to Yazar
            $yazarRole = Role::where('slug', RoleSlug::Yazar->value)->firstOrFail();
            $application->user->update(['role_id' => $yazarRole->id]);

            $this->clearPendingCountCache();

            Mail::to($application->user->email)->send(new WriterApplicationApprovedMail($application));

            return $application;
        });
    }

    /**
     * Reject a writer application.
     */
    public function reject(WriterApplication $application, User $reviewer, string $adminNote): WriterApplication
    {
        return DB::transaction(function () use ($application, $reviewer, $adminNote): WriterApplication {
            $application->update([
                'status'      => WriterApplicationStatus::Rejected,
                'admin_note'  => $adminNote,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
            ]);

            $this->clearPendingCountCache();

            Mail::to($application->user->email)->send(new WriterApplicationRejectedMail($application));

            return $application;
        });
    }

    /**
     * Get paginated applications with optional status filter.
     */
    public function getPaginated(?WriterApplicationStatus $status = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = WriterApplication::with(['user', 'reviewer'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderByDesc('created_at');

        if ($status !== null) {
            $query->where('status', $status);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get pending application count (cached).
     */
    public function getPendingCount(): int
    {
        return Cache::remember('writer_applications.pending_count', 300, function (): int {
            return WriterApplication::where('status', WriterApplicationStatus::Pending)->count();
        });
    }

    /**
     * Get status counts for tabs.
     *
     * @return array<string, int>
     */
    public function getStatusCounts(): array
    {
        return Cache::remember('writer_applications.status_counts', 300, function (): array {
            $counts = WriterApplication::selectRaw('status, COUNT(*) as cnt')
                ->groupBy('status')
                ->pluck('cnt', 'status');

            return [
                'pending'  => (int) ($counts[WriterApplicationStatus::Pending->value] ?? 0),
                'approved' => (int) ($counts[WriterApplicationStatus::Approved->value] ?? 0),
                'rejected' => (int) ($counts[WriterApplicationStatus::Rejected->value] ?? 0),
                'total'    => (int) $counts->sum(),
            ];
        });
    }

    /**
     * Check if user can apply (not a writer, no pending, 30-day cooldown after rejection).
     *
     * @return array{can_apply: bool, reason: string|null, last_application: WriterApplication|null}
     */
    public function canUserApply(User $user): array
    {
        if ($user->isYazar() || $user->isAdmin() || $user->isSuperAdmin()) {
            return ['can_apply' => false, 'reason' => 'already_writer', 'last_application' => null];
        }

        $lastApplication = WriterApplication::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->first();

        if ($lastApplication === null) {
            return ['can_apply' => true, 'reason' => null, 'last_application' => null];
        }

        if ($lastApplication->status === WriterApplicationStatus::Pending) {
            return ['can_apply' => false, 'reason' => 'pending', 'last_application' => $lastApplication];
        }

        if ($lastApplication->status === WriterApplicationStatus::Rejected) {
            $daysSinceRejection = (int) $lastApplication->reviewed_at?->diffInDays(now());

            if ($daysSinceRejection < 30) {
                return [
                    'can_apply'        => false,
                    'reason'           => 'cooldown',
                    'last_application' => $lastApplication,
                ];
            }

            return ['can_apply' => true, 'reason' => null, 'last_application' => $lastApplication];
        }

        return ['can_apply' => false, 'reason' => 'already_approved', 'last_application' => $lastApplication];
    }

    private function notifyAdmins(WriterApplication $application): void
    {
        $admins = User::whereIn('type', ['super_admin', 'admin'])->where('notify_admin_mails', true)->get();

        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new WriterApplicationSubmittedMail($application));
        }
    }

    private function clearPendingCountCache(): void
    {
        Cache::forget('writer_applications.pending_count');
        Cache::forget('writer_applications.status_counts');
    }
}
