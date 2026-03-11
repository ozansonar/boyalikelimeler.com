<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\QnaAnswer;
use App\Models\QnaCategory;
use App\Models\QnaQuestion;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class QnaCategoryService
{
    /**
     * @return Collection<int, QnaCategory>
     */
    public function getActiveCategories(): Collection
    {
        return Cache::remember('qna_categories.active', 600, function (): Collection {
            return QnaCategory::active()
                ->ordered()
                ->withCount([
                    'questions as approved_questions_count' => fn (Builder $q) => $q->where('status', 'approved'),
                ])
                ->get();
        });
    }

    public function getBySlug(string $slug): ?QnaCategory
    {
        return QnaCategory::where('slug', $slug)->first();
    }

    public function findById(int $id): ?QnaCategory
    {
        return QnaCategory::find($id);
    }

    /**
     * @param array<string, mixed> $filters
     */
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return QnaCategory::query()
            ->withCount([
                'questions as approved_questions_count' => fn (Builder $q) => $q->where('status', 'approved'),
            ])
            ->when($filters['search'] ?? null, function (Builder $q, string $search): void {
                $q->where('name', 'like', "%{$search}%");
            })
            ->ordered()
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function store(array $data): QnaCategory
    {
        return DB::transaction(function () use ($data): QnaCategory {
            $category = QnaCategory::create($data);
            $this->clearCache();

            return $category;
        });
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(QnaCategory $category, array $data): QnaCategory
    {
        return DB::transaction(function () use ($category, $data): QnaCategory {
            $category->update($data);
            $this->clearCache();

            return $category;
        });
    }

    public function destroy(QnaCategory $category): void
    {
        $category->delete();
        $this->clearCache();
    }

    /**
     * @return array<string, int>
     */
    public function getStats(): array
    {
        return Cache::remember('qna.stats', 300, function (): array {
            return [
                'categories' => QnaCategory::active()->count(),
                'questions'  => QnaQuestion::where('status', 'approved')->count(),
                'answers'    => QnaAnswer::where('status', 'approved')->count(),
            ];
        });
    }

    public function clearCache(): void
    {
        Cache::forget('qna_categories.active');
        Cache::forget('qna.stats');
    }
}
