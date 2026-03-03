<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Page;
use App\Traits\GeneratesUniqueSlug;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class PageService
{
    use GeneratesUniqueSlug;

    public function __construct(
        private readonly UploadService $uploadService,
    ) {}

    protected function slugModel(): string
    {
        return Page::class;
    }
    /**
     * @return array<string, int>
     */
    public function getAdminStats(): array
    {
        return Cache::remember('admin.pages.stats', 300, function (): array {
            return [
                'total'    => Page::count(),
                'active'   => Page::where('is_active', true)->count(),
                'inactive' => Page::where('is_active', false)->count(),
            ];
        });
    }

    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator
    {
        $query = Page::with('author');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search): void {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        return $query->orderBy('sort_order')->orderByDesc('created_at')->paginate($perPage)->withQueryString();
    }

    public function create(array $data, ?UploadedFile $coverImage = null): Page
    {
        return DB::transaction(function () use ($data, $coverImage): Page {
            $data['slug'] = $this->generateUniqueSlug($data['title']);
            $data['user_id'] = auth()->id();

            if ($coverImage) {
                $data['cover_image'] = $this->uploadService->uploadImage($coverImage, 'pages', $data['title']);
            }

            $page = Page::create($data);

            $this->clearCache();

            return $page;
        });
    }

    public function update(Page $page, array $data, ?UploadedFile $coverImage = null): Page
    {
        return DB::transaction(function () use ($page, $data, $coverImage): Page {
            if ($page->title !== $data['title']) {
                $data['slug'] = $this->generateUniqueSlug($data['title'], $page->id);
            }

            if ($coverImage) {
                $data['cover_image'] = $this->uploadService->replaceImage($coverImage, 'pages', $page->cover_image, $data['title']);
            }

            $page->update($data);

            $this->clearCache();

            return $page->fresh();
        });
    }

    public function delete(Page $page): void
    {
        DB::transaction(function () use ($page): void {
            $this->uploadService->deleteImage($page->cover_image);
            $page->delete();
            $this->clearCache();
        });
    }

    public function findActiveBySlug(string $slug): ?Page
    {
        return Page::where('slug', $slug)
            ->where('is_active', true)
            ->first();
    }

    private function clearCache(): void
    {
        Cache::forget('admin.pages.stats');
    }
}
