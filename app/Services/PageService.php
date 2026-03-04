<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Page;
use App\Models\PageBox;
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

    public function create(array $data, ?UploadedFile $coverImage = null, array $boxes = [], array $boxImages = []): Page
    {
        return DB::transaction(function () use ($data, $coverImage, $boxes, $boxImages): Page {
            $data['slug'] = $this->generateUniqueSlug($data['title']);
            $data['user_id'] = auth()->id();

            if ($coverImage) {
                $data['cover_image'] = $this->uploadService->uploadImage($coverImage, 'pages', $data['title']);
            }

            $page = Page::create($data);

            $this->syncBoxes($page, $boxes, $boxImages);

            $this->clearCache();

            return $page;
        });
    }

    public function update(Page $page, array $data, ?UploadedFile $coverImage = null, array $boxes = [], array $boxImages = []): Page
    {
        return DB::transaction(function () use ($page, $data, $coverImage, $boxes, $boxImages): Page {
            if ($page->title !== $data['title']) {
                $data['slug'] = $this->generateUniqueSlug($data['title'], $page->id);
            }

            if ($coverImage) {
                $data['cover_image'] = $this->uploadService->replaceImage($coverImage, 'pages', $page->cover_image, $data['title']);
            }

            $page->update($data);

            $this->syncBoxes($page, $boxes, $boxImages);

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
        return Page::with('boxes')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();
    }

    /**
     * @param array<int, array<string, mixed>> $boxes
     * @param array<int, UploadedFile|null>     $boxImages
     */
    private function syncBoxes(Page $page, array $boxes, array $boxImages): void
    {
        $incomingIds = [];

        foreach ($boxes as $index => $boxData) {
            $existingId = ! empty($boxData['id']) ? (int) $boxData['id'] : null;

            $imageFile = $boxImages[$index] ?? null;
            $imagePath = $boxData['existing_image'] ?? null;

            if ($imageFile instanceof UploadedFile) {
                if ($existingId && $imagePath) {
                    $imagePath = $this->uploadService->replaceImage($imageFile, 'page-boxes', $imagePath, $boxData['title'] ?? 'box');
                } else {
                    $imagePath = $this->uploadService->uploadImage($imageFile, 'page-boxes', $boxData['title'] ?? 'box');
                }
            }

            $attributes = [
                'title'       => $boxData['title'],
                'description' => $boxData['description'] ?? null,
                'link'        => $boxData['link'] ?? null,
                'link_target' => $boxData['link_target'] ?? '_blank',
                'image'       => $imagePath,
                'col_desktop' => (int) ($boxData['col_desktop'] ?? 4),
                'col_tablet'  => (int) ($boxData['col_tablet'] ?? 6),
                'col_mobile'  => (int) ($boxData['col_mobile'] ?? 12),
                'sort_order'  => $index,
            ];

            if ($existingId) {
                $box = $page->boxes()->find($existingId);
                if ($box) {
                    $box->update($attributes);
                    $incomingIds[] = $box->id;
                }
            } else {
                $newBox = $page->boxes()->create($attributes);
                $incomingIds[] = $newBox->id;
            }
        }

        // Delete removed boxes
        $page->boxes()->whereNotIn('id', $incomingIds)->each(function (PageBox $box): void {
            $this->uploadService->deleteImage($box->image);
            $box->delete();
        });
    }

    private function clearCache(): void
    {
        Cache::forget('admin.pages.stats');
    }
}
