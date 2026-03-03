<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Traits\GeneratesUniqueSlug;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class PostService
{
    use GeneratesUniqueSlug;

    public function __construct(
        private readonly UploadService $uploadService,
    ) {}

    protected function slugModel(): string
    {
        return Post::class;
    }
    /**
     * @return array<string, int>
     */
    public function getAdminStats(): array
    {
        return Cache::remember('admin.posts.stats', 300, function (): array {
            return [
                'total'     => Post::count(),
                'published' => Post::where('status', PostStatus::Published)->count(),
                'draft'     => Post::where('status', PostStatus::Draft)->count(),
                'views'     => (int) Post::sum('view_count'),
            ];
        });
    }

    /**
     * @return array<string, int>
     */
    public function getStatusCounts(): array
    {
        return Cache::remember('admin.posts.status_counts', 300, function (): array {
            $counts = ['all' => Post::count()];

            foreach (PostStatus::cases() as $status) {
                $counts[$status->value] = Post::where('status', $status)->count();
            }

            return $counts;
        });
    }

    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator
    {
        $query = Post::with(['category', 'author']);

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search): void {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (! empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        $sortField = $filters['sort'] ?? 'created_at';
        $sortDir = $filters['dir'] ?? 'desc';
        $allowedSorts = ['title', 'created_at', 'view_count', 'published_at'];

        if (! in_array($sortField, $allowedSorts, true)) {
            $sortField = 'created_at';
        }

        return $query->orderBy($sortField, $sortDir)->paginate($perPage)->withQueryString();
    }

    public function create(array $data, ?UploadedFile $coverImage = null): Post
    {
        return DB::transaction(function () use ($data, $coverImage): Post {
            $data['slug'] = $this->generateUniqueSlug($data['title']);
            $data['user_id'] = auth()->id();

            if (($data['status'] ?? 'draft') === PostStatus::Published->value) {
                $data['published_at'] = $data['published_at'] ?? now();
            }

            if ($coverImage) {
                $data['cover_image'] = $this->uploadService->uploadImage($coverImage, 'posts', $data['title']);
            }

            $post = Post::create($data);

            $this->clearCache();

            return $post;
        });
    }

    public function update(Post $post, array $data, ?UploadedFile $coverImage = null): Post
    {
        return DB::transaction(function () use ($post, $data, $coverImage): Post {
            if ($post->title !== $data['title']) {
                $data['slug'] = $this->generateUniqueSlug($data['title'], $post->id);
            }

            if (
                ($data['status'] ?? $post->status->value) === PostStatus::Published->value
                && $post->published_at === null
            ) {
                $data['published_at'] = $data['published_at'] ?? now();
            }

            if ($coverImage) {
                $data['cover_image'] = $this->uploadService->replaceImage($coverImage, 'posts', $post->cover_image, $data['title']);
            }

            $post->update($data);

            $this->clearCache();

            return $post->fresh();
        });
    }

    public function delete(Post $post): void
    {
        DB::transaction(function () use ($post): void {
            $this->uploadService->deleteImage($post->cover_image);
            $post->delete();
            $this->clearCache();
        });
    }

    public function incrementViews(Post $post): void
    {
        $post->increment('view_count');
    }

    public function getPublishedPosts(int $perPage = 12, ?int $categoryId = null): LengthAwarePaginator
    {
        $query = Post::with(['category', 'author'])
            ->where('status', PostStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        return $query->orderByDesc('published_at')->paginate($perPage)->withQueryString();
    }

    public function findPublishedBySlug(string $slug): ?Post
    {
        return Post::with(['category', 'author'])
            ->where('slug', $slug)
            ->where('status', PostStatus::Published)
            ->first();
    }

    public function getFeaturedPosts(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return Post::with(['category', 'author'])
            ->where('status', PostStatus::Published)
            ->where('is_featured', true)
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    public function getPopularPosts(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('posts.popular.' . $limit, 600, function () use ($limit): \Illuminate\Database\Eloquent\Collection {
            return Post::with(['category'])
                ->where('status', PostStatus::Published)
                ->orderByDesc('view_count')
                ->limit($limit)
                ->get();
        });
    }

    public function getPublishedStats(): array
    {
        return Cache::remember('posts.published.stats', 600, function (): array {
            return [
                'total_posts'      => Post::where('status', PostStatus::Published)->count(),
                'total_categories' => \App\Models\Category::where('is_active', true)->count(),
                'total_views'      => (int) Post::where('status', PostStatus::Published)->sum('view_count'),
            ];
        });
    }

    private function clearCache(): void
    {
        Cache::forget('admin.posts.stats');
        Cache::forget('admin.posts.status_counts');
        Cache::forget('posts.popular.5');
        Cache::forget('posts.published.stats');
    }
}
