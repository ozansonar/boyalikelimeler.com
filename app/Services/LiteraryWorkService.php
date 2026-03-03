<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\LiteraryWorkStatus;
use App\Mail\LiteraryWorkApprovedMail;
use App\Mail\LiteraryWorkRejectedMail;
use App\Mail\LiteraryWorkRevisionRequestedMail;
use App\Mail\LiteraryWorkSubmittedMail;
use App\Mail\LiteraryWorkRevisedMail;
use App\Models\LiteraryCategory;
use App\Models\LiteraryRevision;
use App\Models\LiteraryWork;
use App\Models\User;
use App\Traits\GeneratesUniqueSlug;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

final class LiteraryWorkService
{
    use GeneratesUniqueSlug;

    private bool $lastMailSent = true;

    protected function slugModel(): string
    {
        return LiteraryWork::class;
    }

    public function wasMailSent(): bool
    {
        return $this->lastMailSent;
    }

    // ─── Admin: Stats ───

    /**
     * @return array{total: int, pending: int, approved: int, rejected: int, revision_requested: int}
     */
    public function getAdminStats(): array
    {
        return Cache::remember('literary_works.admin_stats', 300, function (): array {
            $counts = LiteraryWork::selectRaw("status, COUNT(*) as cnt")
                ->groupBy('status')
                ->pluck('cnt', 'status');

            return [
                'total'              => (int) $counts->sum(),
                'pending'            => (int) ($counts[LiteraryWorkStatus::Pending->value] ?? 0),
                'approved'           => (int) ($counts[LiteraryWorkStatus::Approved->value] ?? 0),
                'rejected'           => (int) ($counts[LiteraryWorkStatus::Rejected->value] ?? 0),
                'revision_requested' => (int) ($counts[LiteraryWorkStatus::RevisionRequested->value] ?? 0),
            ];
        });
    }

    public function getPendingCount(): int
    {
        return Cache::remember('literary_works.pending_count', 300, function (): int {
            return LiteraryWork::where('status', LiteraryWorkStatus::Pending)->count();
        });
    }

    // ─── Admin: Paginate ───

    public function adminPaginate(int $perPage, array $filters = []): LengthAwarePaginator
    {
        $query = LiteraryWork::with(['category', 'author']);

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search): void {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('author', fn ($aq) => $aq->where('name', 'like', "%{$search}%"));
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['category'])) {
            $query->where('literary_category_id', $filters['category']);
        }

        return $query->orderByDesc('created_at')->paginate($perPage)->withQueryString();
    }

    // ─── Admin: Show (detail) ───

    public function findForAdmin(int $id): ?LiteraryWork
    {
        return LiteraryWork::with(['category', 'author', 'revisions.admin'])->find($id);
    }

    // ─── Admin: Approve ───

    public function approve(LiteraryWork $work): bool
    {
        DB::transaction(function () use ($work): void {
            $work->update([
                'status'       => LiteraryWorkStatus::Approved,
                'published_at' => now(),
            ]);

            $this->clearCache();
        });

        return $this->sendMailSafely(
            fn () => Mail::to($work->author)->send(new LiteraryWorkApprovedMail($work)),
            'approve',
            $work,
        );
    }

    // ─── Admin: Reject ───

    public function reject(LiteraryWork $work): bool
    {
        DB::transaction(function () use ($work): void {
            $work->update([
                'status' => LiteraryWorkStatus::Rejected,
            ]);

            $this->clearCache();
        });

        return $this->sendMailSafely(
            fn () => Mail::to($work->author)->send(new LiteraryWorkRejectedMail($work)),
            'reject',
            $work,
        );
    }

    // ─── Admin: Request Revision ───

    public function requestRevision(LiteraryWork $work, User $admin, string $reason): bool
    {
        DB::transaction(function () use ($work, $admin, $reason): void {
            $work->update([
                'status' => LiteraryWorkStatus::RevisionRequested,
            ]);

            LiteraryRevision::create([
                'literary_work_id' => $work->id,
                'admin_id'         => $admin->id,
                'reason'           => $reason,
            ]);

            $this->clearCache();
        });

        $work->load('revisions.admin');

        return $this->sendMailSafely(
            fn () => Mail::to($work->author)->send(new LiteraryWorkRevisionRequestedMail($work, $reason)),
            'requestRevision',
            $work,
        );
    }

    // ─── Author: Stats ───

    /**
     * @return array{total: int, pending: int, approved: int, rejected: int, revision_requested: int}
     */
    public function getAuthorStats(User $user): array
    {
        $counts = $user->literaryWorks()
            ->selectRaw("status, COUNT(*) as cnt")
            ->groupBy('status')
            ->pluck('cnt', 'status');

        return [
            'total'              => (int) $counts->sum(),
            'pending'            => (int) ($counts[LiteraryWorkStatus::Pending->value] ?? 0),
            'approved'           => (int) ($counts[LiteraryWorkStatus::Approved->value] ?? 0),
            'rejected'           => (int) ($counts[LiteraryWorkStatus::Rejected->value] ?? 0),
            'revision_requested' => (int) ($counts[LiteraryWorkStatus::RevisionRequested->value] ?? 0),
        ];
    }

    // ─── Author: Paginate ───

    public function authorPaginate(User $user, int $perPage, array $filters = []): LengthAwarePaginator
    {
        $query = $user->literaryWorks()->with(['category', 'revisions.admin']);

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('title', 'like', "%{$search}%");
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderByDesc('created_at')->paginate($perPage)->withQueryString();
    }

    // ─── Author: Create ───

    public function createWork(User $user, array $data, ?UploadedFile $coverImage = null): LiteraryWork
    {
        $work = DB::transaction(function () use ($user, $data, $coverImage): LiteraryWork {
            return $user->literaryWorks()->create([
                'title'                => $data['title'],
                'slug'                 => $this->generateUniqueSlug($data['title']),
                'body'                 => $data['body'],
                'excerpt'              => $data['excerpt'] ?? null,
                'literary_category_id' => $data['literary_category_id'],
                'status'               => LiteraryWorkStatus::Pending,
                'meta_title'           => $data['title'],
                'meta_description'     => $data['excerpt'] ?? null,
                'cover_image'          => $coverImage ? $this->storeCoverImage($coverImage) : null,
            ]);
        });

        $this->clearCache();
        $this->lastMailSent = $this->notifyAdminsNewSubmission($work);

        return $work;
    }

    // ─── Author: Update (revise) ───

    public function updateWork(User $user, LiteraryWork $work, array $data, ?UploadedFile $coverImage = null): ?LiteraryWork
    {
        if ((int) $work->user_id !== (int) $user->id) {
            return null;
        }

        $wasRevisionRequested = $work->status === LiteraryWorkStatus::RevisionRequested;

        $updatedWork = DB::transaction(function () use ($work, $data, $coverImage, $wasRevisionRequested): LiteraryWork {
            $updateData = [
                'title'                => $data['title'],
                'slug'                 => $this->generateUniqueSlug($data['title'], $work->id),
                'body'                 => $data['body'],
                'excerpt'              => $data['excerpt'] ?? null,
                'literary_category_id' => $data['literary_category_id'],
                'status'               => LiteraryWorkStatus::Pending,
                'meta_title'           => $data['title'],
                'meta_description'     => $data['excerpt'] ?? null,
            ];

            if ($coverImage) {
                $this->deleteOldCover($work->cover_image);
                $updateData['cover_image'] = $this->storeCoverImage($coverImage);
            }

            if (! empty($data['remove_cover']) && ! $coverImage) {
                $this->deleteOldCover($work->cover_image);
                $updateData['cover_image'] = null;
            }

            $work->update($updateData);

            if ($wasRevisionRequested) {
                $work->revisions()
                    ->where('is_resolved', false)
                    ->update([
                        'is_resolved'  => true,
                        'resolved_at'  => now(),
                        'author_note'  => $data['author_note'] ?? null,
                    ]);
            }

            return $work->fresh();
        });

        $this->clearCache();

        if ($wasRevisionRequested) {
            $this->lastMailSent = $this->notifyAdminsRevised($updatedWork);
        }

        return $updatedWork;
    }

    // ─── Author: Get for Edit ───

    public function getWorkForEdit(User $user, LiteraryWork $work): ?LiteraryWork
    {
        if ((int) $work->user_id !== (int) $user->id) {
            return null;
        }

        return $work->load(['category', 'revisions.admin']);
    }

    // ─── Author: Delete ───

    public function deleteWork(User $user, LiteraryWork $work): bool
    {
        if ((int) $work->user_id !== (int) $user->id) {
            return false;
        }

        return DB::transaction(function () use ($work): bool {
            $work->delete();
            $this->clearCache();
            return true;
        });
    }

    // ─── File Helpers ───

    private function storeCoverImage(UploadedFile $file): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $directory = public_path('uploads/literary');

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $file->move($directory, $filename);

        return 'literary/' . $filename;
    }

    private function deleteOldCover(?string $path): void
    {
        if (! $path) {
            return;
        }

        $fullPath = public_path('uploads/' . $path);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }

    // ─── Notification Helpers ───

    private function notifyAdminsNewSubmission(LiteraryWork $work): bool
    {
        $admins = User::whereHas('role', fn ($q) => $q->whereIn('slug', ['admin', 'super_admin']))->get();
        $allSent = true;

        foreach ($admins as $admin) {
            $sent = $this->sendMailSafely(
                fn () => Mail::to($admin)->send(new LiteraryWorkSubmittedMail($work)),
                'notifyAdminsNewSubmission',
                $work,
            );
            if (! $sent) {
                $allSent = false;
            }
        }

        return $allSent;
    }

    private function notifyAdminsRevised(LiteraryWork $work): bool
    {
        $admins = User::whereHas('role', fn ($q) => $q->whereIn('slug', ['admin', 'super_admin']))->get();
        $allSent = true;

        foreach ($admins as $admin) {
            $sent = $this->sendMailSafely(
                fn () => Mail::to($admin)->send(new LiteraryWorkRevisedMail($work)),
                'notifyAdminsRevised',
                $work,
            );
            if (! $sent) {
                $allSent = false;
            }
        }

        return $allSent;
    }

    /**
     * Send mail inside try-catch. Returns true on success, false on failure.
     */
    private function sendMailSafely(\Closure $mailCallback, string $action, LiteraryWork $work): bool
    {
        try {
            $mailCallback();

            return true;
        } catch (\Throwable $e) {
            Log::error("Mail gönderilemedi [{$action}] — Eser #{$work->id} ({$work->title}): {$e->getMessage()}");

            return false;
        }
    }

    // ─── Front: Paginate published works ───

    public function frontPaginate(int $perPage, array $filters = []): LengthAwarePaginator
    {
        $query = LiteraryWork::with(['category', 'author'])
            ->where('status', LiteraryWorkStatus::Approved)
            ->whereNotNull('published_at');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search): void {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['category'])) {
            $categoryId = LiteraryCategory::where('slug', $filters['category'])->value('id');
            if ($categoryId) {
                $query->where('literary_category_id', $categoryId);
            }
        }

        $sort = $filters['sort'] ?? 'newest';
        $query = match ($sort) {
            'popular' => $query->orderByDesc('view_count'),
            default   => $query->orderByDesc('published_at'),
        };

        return $query->paginate($perPage)->withQueryString();
    }

    // ─── Front: Single work by slug ───

    public function findPublishedBySlug(string $slug): ?LiteraryWork
    {
        return LiteraryWork::with(['category', 'author'])
            ->where('slug', $slug)
            ->where('status', LiteraryWorkStatus::Approved)
            ->whereNotNull('published_at')
            ->first();
    }

    // ─── Front: Increment view count ───

    public function incrementViews(LiteraryWork $work): void
    {
        $work->increment('view_count');
    }

    // ─── Front: Related works (same category) ───

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, LiteraryWork>
     */
    public function getRelatedWorks(LiteraryWork $work, int $limit = 4): \Illuminate\Database\Eloquent\Collection
    {
        return LiteraryWork::with(['author'])
            ->where('literary_category_id', $work->literary_category_id)
            ->where('id', '!=', $work->id)
            ->where('status', LiteraryWorkStatus::Approved)
            ->whereNotNull('published_at')
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    // ─── Front: Stats (published) ───

    /**
     * @return array{work_count: int, author_count: int, total_views: int}
     */
    public function getPublishedStats(): array
    {
        return Cache::remember('literary_works.front_stats', 300, function (): array {
            return [
                'work_count'   => LiteraryWork::where('status', LiteraryWorkStatus::Approved)->count(),
                'author_count' => LiteraryWork::where('status', LiteraryWorkStatus::Approved)
                    ->distinct('user_id')->count('user_id'),
                'total_views'  => (int) LiteraryWork::where('status', LiteraryWorkStatus::Approved)->sum('view_count'),
            ];
        });
    }

    // ─── Cache ───

    private function clearCache(): void
    {
        Cache::forget('literary_works.admin_stats');
        Cache::forget('literary_works.pending_count');
        Cache::forget('literary_works.front_stats');
    }
}
