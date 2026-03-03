<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use App\Traits\GeneratesUniqueSlug;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

final class MyPostService
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
     * @return array{total: int, published: int, draft: int, archived: int}
     */
    public function getStats(User $user): array
    {
        $counts = $user->posts()
            ->selectRaw("status, COUNT(*) as cnt")
            ->groupBy('status')
            ->pluck('cnt', 'status');

        return [
            'total'     => (int) $counts->sum(),
            'published' => (int) ($counts[PostStatus::Published->value] ?? 0),
            'draft'     => (int) ($counts[PostStatus::Draft->value] ?? 0),
            'archived'  => (int) ($counts[PostStatus::Archived->value] ?? 0),
        ];
    }

    public function paginate(User $user, int $perPage, array $filters = []): LengthAwarePaginator
    {
        $query = $user->posts()->with('category');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('title', 'like', "%{$search}%");
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function createPost(User $user, array $data, ?UploadedFile $coverImage = null): Post
    {
        return DB::transaction(function () use ($user, $data, $coverImage): Post {
            $post = $user->posts()->create([
                'title'            => $data['title'],
                'slug'             => $this->generateUniqueSlug($data['title']),
                'body'             => $data['body'],
                'excerpt'          => $data['excerpt'] ?? null,
                'category_id'      => $data['category_id'],
                'status'           => PostStatus::Draft,
                'meta_title'       => $data['title'],
                'meta_description' => $data['excerpt'] ?? null,
                'cover_image'      => $coverImage ? $this->storeCoverImage($coverImage, $data['title']) : null,
            ]);

            return $post;
        });
    }

    public function updatePost(User $user, Post $post, array $data, ?UploadedFile $coverImage = null): ?Post
    {
        if ($post->user_id !== $user->id) {
            return null;
        }

        return DB::transaction(function () use ($post, $data, $coverImage): Post {
            $updateData = [
                'title'            => $data['title'],
                'slug'             => $this->generateUniqueSlug($data['title'], $post->id),
                'body'             => $data['body'],
                'excerpt'          => $data['excerpt'] ?? null,
                'category_id'      => $data['category_id'],
                'meta_title'       => $data['title'],
                'meta_description' => $data['excerpt'] ?? null,
            ];

            if ($coverImage) {
                $this->deleteOldCover($post->cover_image);
                $updateData['cover_image'] = $this->storeCoverImage($coverImage, $data['title']);
            }

            if (! empty($data['remove_cover']) && ! $coverImage) {
                $this->deleteOldCover($post->cover_image);
                $updateData['cover_image'] = null;
            }

            $post->update($updateData);

            return $post->fresh();
        });
    }

    public function getPostForEdit(User $user, Post $post): ?Post
    {
        if ($post->user_id !== $user->id) {
            return null;
        }

        return $post->load('category');
    }

    private function storeCoverImage(UploadedFile $file, ?string $title = null): string
    {
        return $this->uploadService->uploadImage($file, 'posts', $title);
    }

    private function deleteOldCover(?string $path): void
    {
        $this->uploadService->deleteImage($path);
    }

    public function deletePost(User $user, Post $post): bool
    {
        if ($post->user_id !== $user->id) {
            return false;
        }

        return DB::transaction(function () use ($post): bool {
            $post->delete();
            return true;
        });
    }
}
