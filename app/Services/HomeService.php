<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\LiteraryWorkStatus;
use App\Enums\LiteraryWorkType;
use App\Enums\PostStatus;
use App\Enums\QnaStatus;
use App\Models\Category;
use App\Models\LiteraryWork;
use App\Models\Post;
use App\Models\QnaQuestion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

final class HomeService
{
    public function __construct(
        private readonly YouTubeService $youTubeService,
    ) {}

    /**
     * Get YouTube channel videos.
     *
     * @return array<int, array{id: string, title: string, thumbnail: string, published_at: string, link: string}>
     */
    public function getYouTubeVideos(int $limit = 10): array
    {
        return $this->youTubeService->getChannelVideos($limit);
    }
    /**
     * Get latest approved written works.
     *
     * @return Collection<int, LiteraryWork>
     */
    public function getLatestWrittenWorks(int $limit = 3): Collection
    {
        return Cache::remember('home.latest_written_works', 300, fn (): Collection =>
            LiteraryWork::whereHas('author', fn ($q) => $q->whereNotNull('username'))
                ->where('status', LiteraryWorkStatus::Approved)
                ->where('work_type', LiteraryWorkType::Written)
                ->with(['category', 'author'])
                ->orderByDesc('published_at')
                ->limit($limit)
                ->get()
        );
    }

    /**
     * Get latest approved visual works.
     *
     * @return Collection<int, LiteraryWork>
     */
    public function getLatestVisualWorks(int $limit = 3): Collection
    {
        return Cache::remember('home.latest_visual_works', 300, fn (): Collection =>
            LiteraryWork::whereHas('author', fn ($q) => $q->whereNotNull('username'))
                ->where('status', LiteraryWorkStatus::Approved)
                ->where('work_type', LiteraryWorkType::Visual)
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
            LiteraryWork::whereHas('author', fn ($q) => $q->whereNotNull('username'))
                ->where('status', LiteraryWorkStatus::Approved)
                ->where('work_type', LiteraryWorkType::Written)
                ->with(['category', 'author'])
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
     * @return Collection<int, QnaQuestion>
     */
    public function getLatestQnaQuestions(int $limit = 3): Collection
    {
        return Cache::remember('home.latest_qna_questions', 300, fn (): Collection =>
            QnaQuestion::where('status', QnaStatus::Approved)
                ->with(['user', 'category'])
                ->withCount(['approvedAnswers as approved_answer_count'])
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get()
        );
    }

    /**
     * Get first N active categories ordered by sort_order.
     *
     * @return Collection<int, Category>
     */
    public function getContentCategories(int $limit = 4): Collection
    {
        return Cache::remember('home.content_categories', 600, fn (): Collection =>
            Category::where('is_active', true)
                ->orderBy('sort_order')
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
            'total_works' => LiteraryWork::whereHas('author', fn ($q) => $q->whereNotNull('username'))->where('status', LiteraryWorkStatus::Approved)->count(),
            'total_posts' => Post::where('status', PostStatus::Published)->count(),
        ]);
    }
}
