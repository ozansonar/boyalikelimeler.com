<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class CategoryService
{
    public function all(): Collection
    {
        return Category::orderBy('sort_order')->orderBy('name')->get();
    }

    public function activeList(): Collection
    {
        return Cache::remember('categories.active', 600, function (): Collection {
            return Category::where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        });
    }

    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator
    {
        $query = Category::withCount('posts');

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

    public function create(array $data): Category
    {
        return DB::transaction(function () use ($data): Category {
            $data['slug'] = $this->generateUniqueSlug($data['name']);

            $category = Category::create($data);

            Cache::forget('categories.active');

            return $category;
        });
    }

    public function update(Category $category, array $data): Category
    {
        return DB::transaction(function () use ($category, $data): Category {
            if ($category->name !== $data['name']) {
                $data['slug'] = $this->generateUniqueSlug($data['name'], $category->id);
            }

            $category->update($data);

            Cache::forget('categories.active');

            return $category->fresh();
        });
    }

    public function delete(Category $category): void
    {
        DB::transaction(function () use ($category): void {
            $category->delete();
            Cache::forget('categories.active');
        });
    }

    private function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $counter = 1;

        while (Category::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $original . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
