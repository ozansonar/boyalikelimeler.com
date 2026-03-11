<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\DailyQuestion;
use App\Models\DailyQuestionAnswer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class DailyQuestionService
{
    private const CACHE_TTL = 300;

    /**
     * Get all questions for admin list.
     */
    public function getAll(): Collection
    {
        return DailyQuestion::withCount('answers')
            ->with('creator:id,name')
            ->orderByDesc('published_at')
            ->get();
    }

    /**
     * Find a question by ID.
     */
    public function find(int $id): DailyQuestion
    {
        return DailyQuestion::with('creator:id,name')
            ->withCount('answers')
            ->findOrFail($id);
    }

    /**
     * Create a new question.
     *
     * @param array<string, mixed> $data
     */
    public function store(array $data): DailyQuestion
    {
        $question = DB::transaction(function () use ($data): DailyQuestion {
            return DailyQuestion::create($data);
        });

        $this->clearCache();

        return $question;
    }

    /**
     * Update a question.
     *
     * @param array<string, mixed> $data
     */
    public function update(DailyQuestion $question, array $data): DailyQuestion
    {
        DB::transaction(function () use ($question, $data): void {
            $question->update($data);
        });

        $this->clearCache();

        return $question;
    }

    /**
     * Delete a question.
     */
    public function destroy(DailyQuestion $question): void
    {
        $question->delete();
        $this->clearCache();
    }

    /**
     * Get today's active question (cached).
     */
    public function getActiveQuestion(): ?DailyQuestion
    {
        return Cache::remember('daily_question.active', self::CACHE_TTL, function (): ?DailyQuestion {
            return DailyQuestion::where('status', 'published')
                ->where('published_at', '<=', now()->toDateString())
                ->orderByDesc('published_at')
                ->first();
        });
    }

    /**
     * Check if a user/visitor has already answered the active question.
     */
    public function hasAnswered(int $questionId, ?int $userId, string $ipAddress, ?string $cookieToken): bool
    {
        $query = DailyQuestionAnswer::where('daily_question_id', $questionId);

        if ($userId !== null) {
            return $query->where('user_id', $userId)->exists();
        }

        $query->where('ip_address', $ipAddress);

        if ($cookieToken !== null) {
            $query->orWhere(function ($q) use ($questionId, $cookieToken): void {
                $q->where('daily_question_id', $questionId)
                    ->where('cookie_token', $cookieToken);
            });
        }

        return $query->exists();
    }

    /**
     * Store an answer.
     *
     * @param array<string, mixed> $data
     */
    public function storeAnswer(array $data): DailyQuestionAnswer
    {
        return DB::transaction(function () use ($data): DailyQuestionAnswer {
            $answer = DailyQuestionAnswer::create($data);

            $this->clearAnswerCountCache($data['daily_question_id']);

            return $answer;
        });
    }

    /**
     * Get answers for a question (admin).
     */
    public function getAnswers(int $questionId): Collection
    {
        return DailyQuestionAnswer::where('daily_question_id', $questionId)
            ->with('user:id,name')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Delete an answer.
     */
    public function destroyAnswer(DailyQuestionAnswer $answer): void
    {
        $questionId = $answer->daily_question_id;
        $answer->delete();
        $this->clearAnswerCountCache($questionId);
    }

    /**
     * Get admin stats.
     *
     * @return array{total: int, published: int, draft: int, archived: int, total_answers: int}
     */
    public function getAdminStats(): array
    {
        return [
            'total'         => DailyQuestion::count(),
            'published'     => DailyQuestion::where('status', 'published')->count(),
            'draft'         => DailyQuestion::where('status', 'draft')->count(),
            'archived'      => DailyQuestion::where('status', 'archived')->count(),
            'total_answers' => DailyQuestionAnswer::count(),
        ];
    }

    /**
     * Clear active question cache.
     */
    private function clearCache(): void
    {
        Cache::forget('daily_question.active');
    }

    /**
     * Clear answer count cache for a question.
     */
    private function clearAnswerCountCache(int $questionId): void
    {
        Cache::forget("daily_question.answer_count.{$questionId}");
    }
}
