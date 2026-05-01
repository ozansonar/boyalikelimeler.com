<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\QnaStatus;
use App\Mail\QnaQuestionApprovedMail;
use App\Mail\QnaQuestionSubmittedMail;
use App\Models\QnaQuestion;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

final class QnaQuestionService
{
    /**
     * @param array<string, mixed> $filters
     */
    public function getByCategory(int $categoryId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return QnaQuestion::query()
            ->with(['user', 'category'])
            ->withCount(['approvedAnswers as approved_answer_count'])
            ->byCategory($categoryId)
            ->approved()
            ->when($filters['sort'] ?? 'newest', function (Builder $q, string $sort): void {
                match ($sort) {
                    'popular'    => $q->orderByDesc('answer_count'),
                    'unanswered' => $q->where('answer_count', 0)->orderByDesc('created_at'),
                    default      => $q->orderByDesc('created_at'),
                };
            })
            ->when($filters['search'] ?? null, function (Builder $q, string $search): void {
                $q->where(function (Builder $q2) use ($search): void {
                    $q2->where('title', 'like', "%{$search}%")
                       ->orWhere('body', 'like', "%{$search}%");
                });
            })
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getBySlug(string $slug): ?QnaQuestion
    {
        return QnaQuestion::with(['user', 'category', 'approvedAnswers.user'])
            ->where('slug', $slug)
            ->first();
    }

    public function findById(int $id): ?QnaQuestion
    {
        return QnaQuestion::with(['user', 'category', 'answers.user'])->find($id);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function store(array $data, User $user): QnaQuestion
    {
        return DB::transaction(function () use ($data, $user): QnaQuestion {
            $data['user_id'] = $user->id;
            $data['slug']    = $this->generateUniqueSlug($data['title']);
            $data['status']  = QnaStatus::Pending->value;

            $question = QnaQuestion::create($data);

            $this->clearCountCache();
            $this->notifyAdminsNewQuestion($question);

            return $question;
        });
    }

    public function approve(QnaQuestion $question): QnaQuestion
    {
        return DB::transaction(function () use ($question): QnaQuestion {
            $question->update(['status' => QnaStatus::Approved->value]);

            $this->clearCountCache();
            app(QnaCategoryService::class)->clearCache();
            $this->notifyQuestionOwnerApproved($question);

            return $question;
        });
    }

    public function reject(QnaQuestion $question): void
    {
        $question->update(['status' => QnaStatus::Rejected->value]);
        $this->clearCountCache();
        app(QnaCategoryService::class)->clearCache();
    }

    public function destroy(QnaQuestion $question): void
    {
        $question->delete();
        $this->clearCountCache();
        app(QnaCategoryService::class)->clearCache();
    }

    public function incrementViewCount(QnaQuestion $question): void
    {
        app(ViewTrackingService::class)->recordView($question);
    }

    /**
     * @return Collection<int, QnaQuestion>
     */
    public function getRelatedQuestions(QnaQuestion $question, int $limit = 4): Collection
    {
        return QnaQuestion::approved()
            ->where('qna_category_id', $question->qna_category_id)
            ->where('id', '!=', $question->id)
            ->withCount(['approvedAnswers as approved_answer_count'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * @return array<string, int>
     */
    public function getUserStats(int $userId): array
    {
        return [
            'questions' => QnaQuestion::where('user_id', $userId)->approved()->count(),
            'answers'   => \App\Models\QnaAnswer::where('user_id', $userId)->approved()->count(),
            'likes'     => QnaQuestion::where('user_id', $userId)->approved()->sum('like_count')
                         + \App\Models\QnaAnswer::where('user_id', $userId)->approved()->sum('like_count'),
        ];
    }

    public function getPendingCount(): int
    {
        return Cache::remember('qna_questions.pending_count', 300, function (): int {
            return QnaQuestion::pending()->count();
        });
    }

    /**
     * @param array<string, mixed> $filters
     */
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return QnaQuestion::query()
            ->with(['user', 'category'])
            ->when($filters['status'] ?? null, function (Builder $q, string $status): void {
                $q->where('status', $status);
            })
            ->when($filters['category'] ?? null, function (Builder $q, string $categoryId): void {
                $q->where('qna_category_id', $categoryId);
            })
            ->when($filters['search'] ?? null, function (Builder $q, string $search): void {
                $q->where(function (Builder $q2) use ($search): void {
                    $q2->where('title', 'like', "%{$search}%")
                       ->orWhere('body', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @return array<string, int>
     */
    public function getAdminStats(): array
    {
        return Cache::remember('qna.stats', 300, function (): array {
            $counts = QnaQuestion::selectRaw("status, COUNT(*) as cnt")
                ->groupBy('status')
                ->pluck('cnt', 'status');

            return [
                'total'    => (int) $counts->sum(),
                'pending'  => (int) ($counts[QnaStatus::Pending->value] ?? 0),
                'approved' => (int) ($counts[QnaStatus::Approved->value] ?? 0),
                'rejected' => (int) ($counts[QnaStatus::Rejected->value] ?? 0),
            ];
        });
    }

    public function clearCountCache(): void
    {
        Cache::forget('qna_questions.pending_count');
        Cache::forget('qna.pending_total');
        Cache::forget('qna.stats');
    }

    private function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (QnaQuestion::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function notifyAdminsNewQuestion(QnaQuestion $question): void
    {
        $question->loadMissing('user', 'category');
        $admins = User::whereHas('role', fn (Builder $q) => $q->whereIn('slug', ['admin', 'super-admin']))->get();

        foreach ($admins as $admin) {
            $this->sendMailSafely(
                fn () => Mail::to($admin->email, $admin->name)->send(new QnaQuestionSubmittedMail($question)),
                'notifyAdminsNewQuestion',
                $question->id,
            );
        }
    }

    private function notifyQuestionOwnerApproved(QnaQuestion $question): void
    {
        $question->loadMissing('user', 'category');
        $owner = $question->user;

        if (!$owner) {
            return;
        }

        $this->sendMailSafely(
            fn () => Mail::to($owner->email, $owner->name)->send(new QnaQuestionApprovedMail($question)),
            'notifyQuestionOwnerApproved',
            $question->id,
        );
    }

    private function sendMailSafely(\Closure $mailCallback, string $action, int $questionId): bool
    {
        try {
            $mailCallback();

            return true;
        } catch (\Throwable $e) {
            Log::error("Mail gönderilemedi [{$action}] — Soru #{$questionId}: {$e->getMessage()}");

            return false;
        }
    }
}
