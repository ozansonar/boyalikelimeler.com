<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\LiteraryCategory;
use App\Traits\GeneratesUniqueSlug;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class LiteraryCategoryService
{
    use GeneratesUniqueSlug;

    protected function slugModel(): string
    {
        return LiteraryCategory::class;
    }

    public function all(): Collection
    {
        return LiteraryCategory::orderBy('sort_order')->orderBy('name')->get();
    }

    public function activeList(): Collection
    {
        return Cache::remember('literary_categories.active', 600, function (): Collection {
            return LiteraryCategory::where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        });
    }

    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator
    {
        $query = LiteraryCategory::withCount('works');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        return $query->orderBy('sort_order')->orderBy('name')->paginate($perPage)->withQueryString();
    }

    public function create(array $data): LiteraryCategory
    {
        return DB::transaction(function () use ($data): LiteraryCategory {
            $data['slug'] = $this->generateUniqueSlug($data['name']);

            $category = LiteraryCategory::create($data);

            Cache::forget('literary_categories.active');

            return $category;
        });
    }

    public function update(LiteraryCategory $category, array $data): LiteraryCategory
    {
        return DB::transaction(function () use ($category, $data): LiteraryCategory {
            if ($category->name !== $data['name']) {
                $data['slug'] = $this->generateUniqueSlug($data['name'], $category->id);
            }

            $category->update($data);

            Cache::forget('literary_categories.active');

            return $category->fresh();
        });
    }

    public function delete(LiteraryCategory $category): void
    {
        DB::transaction(function () use ($category): void {
            $category->delete();
            Cache::forget('literary_categories.active');
        });
    }
}
