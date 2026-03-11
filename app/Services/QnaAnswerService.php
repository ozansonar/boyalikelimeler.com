<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\QnaStatus;
use App\Mail\QnaAnswerApprovedMail;
use App\Mail\QnaAnswerSubmittedMail;
use App\Models\QnaAnswer;
use App\Models\QnaQuestion;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

final class QnaAnswerService
{
    /**
     * @param array<string, mixed> $data
     */
    public function store(array $data, QnaQuestion $question, User $user): QnaAnswer
    {
        return DB::transaction(function () use ($data, $question, $user): QnaAnswer {
            $data['qna_question_id'] = $question->id;
            $data['user_id']         = $user->id;
            $data['status']          = QnaStatus::Pending->value;

            $answer = QnaAnswer::create($data);

            $this->clearCountCache();
            $this->notifyAdminsNewAnswer($answer);

            return $answer;
        });
    }

    public function approve(QnaAnswer $answer): QnaAnswer
    {
        return DB::transaction(function () use ($answer): QnaAnswer {
            $answer->update(['status' => QnaStatus::Approved->value]);

            $question = $answer->question;
            if ($question) {
                $question->update([
                    'answer_count' => $question->approvedAnswers()->count(),
                ]);
            }

            $this->clearCountCache();
            app(QnaCategoryService::class)->clearCache();
            $this->notifyAnswerOwnerApproved($answer);

            return $answer;
        });
    }

    public function reject(QnaAnswer $answer): void
    {
        DB::transaction(function () use ($answer): void {
            $answer->update(['status' => QnaStatus::Rejected->value]);

            $question = $answer->question;
            if ($question) {
                $question->update([
                    'answer_count' => $question->approvedAnswers()->count(),
                ]);
            }

            $this->clearCountCache();
            app(QnaCategoryService::class)->clearCache();
        });
    }

    public function destroy(QnaAnswer $answer): void
    {
        DB::transaction(function () use ($answer): void {
            $question = $answer->question;
            $answer->delete();

            if ($question) {
                $question->update([
                    'answer_count' => $question->approvedAnswers()->count(),
                ]);
            }

            $this->clearCountCache();
            app(QnaCategoryService::class)->clearCache();
        });
    }

    /**
     * @param array<string, mixed> $filters
     */
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return QnaAnswer::query()
            ->with(['user', 'question.category'])
            ->when($filters['status'] ?? null, function (Builder $q, string $status): void {
                $q->where('status', $status);
            })
            ->when($filters['search'] ?? null, function (Builder $q, string $search): void {
                $q->where('body', 'like', "%{$search}%");
            })
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getPendingCount(): int
    {
        return Cache::remember('qna_answers.pending_count', 300, function (): int {
            return QnaAnswer::pending()->count();
        });
    }

    /**
     * @return array<string, int>
     */
    public function getAdminStats(): array
    {
        return [
            'total'    => QnaAnswer::count(),
            'pending'  => QnaAnswer::where('status', 'pending')->count(),
            'approved' => QnaAnswer::where('status', 'approved')->count(),
            'rejected' => QnaAnswer::where('status', 'rejected')->count(),
        ];
    }

    public function clearCountCache(): void
    {
        Cache::forget('qna_answers.pending_count');
        Cache::forget('qna.pending_total');
        Cache::forget('qna.stats');
    }

    private function notifyAdminsNewAnswer(QnaAnswer $answer): void
    {
        $answer->loadMissing('user', 'question.category');
        $admins = User::whereHas('role', fn (Builder $q) => $q->whereIn('slug', ['admin', 'super-admin']))->get();

        foreach ($admins as $admin) {
            $this->sendMailSafely(
                fn () => Mail::to($admin->email, $admin->name)->send(new QnaAnswerSubmittedMail($answer)),
                'notifyAdminsNewAnswer',
                $answer->id,
            );
        }
    }

    private function notifyAnswerOwnerApproved(QnaAnswer $answer): void
    {
        $answer->loadMissing('user', 'question.category');
        $owner = $answer->user;

        if (!$owner) {
            return;
        }

        $this->sendMailSafely(
            fn () => Mail::to($owner->email, $owner->name)->send(new QnaAnswerApprovedMail($answer)),
            'notifyAnswerOwnerApproved',
            $answer->id,
        );
    }

    private function sendMailSafely(\Closure $mailCallback, string $action, int $answerId): bool
    {
        try {
            $mailCallback();

            return true;
        } catch (\Throwable $e) {
            Log::error("Mail gönderilemedi [{$action}] — Cevap #{$answerId}: {$e->getMessage()}");

            return false;
        }
    }
}
