<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\LiteraryWorkStatus;
use App\Enums\PostStatus;
use App\Models\LiteraryWork;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

final class HomeService
{
    /**
     * Get latest approved literary works.
     *
     * @return Collection<int, LiteraryWork>
     */
    public function getLatestWorks(int $limit = 6): Collection
    {
        return Cache::remember('home.latest_works', 300, fn (): Collection =>
            LiteraryWork::where('status', LiteraryWorkStatus::Approved)
                ->with(['category', 'author'])
                ->orderByDesc('published_at')
                ->limit($limit)
                ->get()
        );
    }

    /**
     * Get most popular literary works by view count.
     *
     * @return Collection<int, LiteraryWork>
     */
    public function getPopularWorks(int $limit = 4): Collection
    {
        return Cache::remember('home.popular_works', 300, fn (): Collection =>
            LiteraryWork::where('status', LiteraryWorkStatus::Approved)
                ->with(['category'])
                ->orderByDesc('view_count')
                ->limit($limit)
                ->get()
        );
    }

    /**
     * Get latest published blog posts.
     *
     * @return Collection<int, Post>
     */
    public function getLatestPosts(int $limit = 6): Collection
    {
        return Cache::remember('home.latest_posts', 300, fn (): Collection =>
            Post::where('status', PostStatus::Published)
                ->with(['category', 'author'])
                ->orderByDesc('published_at')
                ->limit($limit)
                ->get()
        );
    }

    /**
     * Get homepage statistics.
     */
    public function getStats(): array
    {
        return Cache::remember('home.stats', 600, fn (): array => [
            'total_works' => LiteraryWork::where('status', LiteraryWorkStatus::Approved)->count(),
            'total_posts' => Post::where('status', PostStatus::Published)->count(),
        ]);
    }
}
