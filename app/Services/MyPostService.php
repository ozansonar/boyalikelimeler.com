<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final class MyPostService
{
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
