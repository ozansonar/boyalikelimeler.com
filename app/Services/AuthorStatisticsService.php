<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\LiteraryWorkStatus;
use App\Models\DailyView;
use App\Models\LiteraryWork;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class AuthorStatisticsService
{
    // ─── Summary Stats (top cards) ───

    /**
     * @return array{total_authors: int, top_author_name: string, top_author_views: int, top_publisher_name: string, top_publisher_count: int, avg_views_per_author: float}
     */
    public function getSummaryStats(): array
    {
        return Cache::remember('author_statistics.summary', 300, function (): array {
            $totalAuthors = User::whereHas('literaryWorks', fn ($q) => $q->where('status', LiteraryWorkStatus::Approved))
                ->count();

            $topAuthor = $this->getTopAuthorByTotalViews();
            $topPublisherThisMonth = $this->getTopPublisherThisMonth();

            $totalViews = (int) LiteraryWork::where('status', LiteraryWorkStatus::Approved)->sum('view_count');
            $avgViews = $totalAuthors > 0 ? (int) round($totalViews / $totalAuthors) : 0;

            $totalApprovedWorks = LiteraryWork::where('status', LiteraryWorkStatus::Approved)->count();

            return [
                'total_authors'        => $totalAuthors,
                'total_views'          => $totalViews,
                'total_works'          => $totalApprovedWorks,
                'top_author_name'      => $topAuthor['name'] ?? '-',
                'top_author_views'     => $topAuthor['views'] ?? 0,
                'top_publisher_name'   => $topPublisherThisMonth['name'] ?? '-',
                'top_publisher_count'  => $topPublisherThisMonth['count'] ?? 0,
                'avg_views_per_author' => $avgViews,
            ];
        });
    }

    // ─── Paginated Author List ───

    public function paginateAuthors(int $perPage, array $filters = []): LengthAwarePaginator
    {
        $query = User::select('users.*')
            ->whereHas('literaryWorks', fn ($q) => $q->where('status', LiteraryWorkStatus::Approved))
            ->withCount(['literaryWorks as approved_works_count' => fn ($q) => $q->where('status', LiteraryWorkStatus::Approved)])
            ->withSum(['literaryWorks as total_views' => fn ($q) => $q->where('status', LiteraryWorkStatus::Approved)], 'view_count');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $now = Carbon::now();
        $lwClass = LiteraryWork::class;
        $approvedStatus = LiteraryWorkStatus::Approved->value;

        $query->addSelect([
            'views_last_7d' => DailyView::selectRaw('COALESCE(SUM(daily_views.view_count), 0)')
                ->join('literary_works', function ($join) use ($lwClass): void {
                    $join->on('daily_views.viewable_id', '=', 'literary_works.id')
                        ->where('daily_views.viewable_type', '=', $lwClass);
                })
                ->whereColumn('literary_works.user_id', 'users.id')
                ->where('literary_works.status', $approvedStatus)
                ->whereNull('literary_works.deleted_at')
                ->where('daily_views.view_date', '>=', $now->copy()->subDays(7)->toDateString()),
            'views_last_30d' => DailyView::selectRaw('COALESCE(SUM(daily_views.view_count), 0)')
                ->join('literary_works', function ($join) use ($lwClass): void {
                    $join->on('daily_views.viewable_id', '=', 'literary_works.id')
                        ->where('daily_views.viewable_type', '=', $lwClass);
                })
                ->whereColumn('literary_works.user_id', 'users.id')
                ->where('literary_works.status', $approvedStatus)
                ->whereNull('literary_works.deleted_at')
                ->where('daily_views.view_date', '>=', $now->copy()->subDays(30)->toDateString()),
            'views_last_90d' => DailyView::selectRaw('COALESCE(SUM(daily_views.view_count), 0)')
                ->join('literary_works', function ($join) use ($lwClass): void {
                    $join->on('daily_views.viewable_id', '=', 'literary_works.id')
                        ->where('daily_views.viewable_type', '=', $lwClass);
                })
                ->whereColumn('literary_works.user_id', 'users.id')
                ->where('literary_works.status', $approvedStatus)
                ->whereNull('literary_works.deleted_at')
                ->where('daily_views.view_date', '>=', $now->copy()->subDays(90)->toDateString()),
        ]);

        $sort = $filters['sort'] ?? 'total_views';
        $dir = $filters['dir'] ?? 'desc';
        $allowedSorts = ['name', 'approved_works_count', 'total_views', 'views_last_7d', 'views_last_30d', 'views_last_90d', 'created_at'];
        $sort = in_array($sort, $allowedSorts, true) ? $sort : 'total_views';
        $dir = in_array($dir, ['asc', 'desc'], true) ? $dir : 'desc';

        return $query->orderBy($sort, $dir)->paginate($perPage)->withQueryString();
    }

    // ─── Author Detail Data ───

    /**
     * @return array{author: User, work_stats: array, daily_views: array, top_works: Collection, monthly_comparison: array}
     */
    public function getAuthorDetail(User $author): array
    {
        $author->loadCount([
            'literaryWorks as total_works_count',
            'literaryWorks as approved_works_count' => fn ($q) => $q->where('status', LiteraryWorkStatus::Approved),
            'literaryWorks as pending_works_count' => fn ($q) => $q->where('status', LiteraryWorkStatus::Pending),
        ]);

        $approvedWorks = $author->literaryWorks()
            ->where('status', LiteraryWorkStatus::Approved)
            ->withCount(['approvedComments', 'favorites'])
            ->get();

        $totalViews = (int) $approvedWorks->sum('view_count');
        $totalComments = (int) $approvedWorks->sum('approved_comments_count');
        $totalFavorites = (int) $approvedWorks->sum('favorites_count');
        $avgViewsPerWork = $approvedWorks->count() > 0 ? (int) round($totalViews / $approvedWorks->count()) : 0;

        $firstPublished = $author->literaryWorks()
            ->where('status', LiteraryWorkStatus::Approved)
            ->whereNotNull('published_at')
            ->orderBy('published_at')
            ->value('published_at');

        $workStats = [
            'total_works'       => $author->total_works_count,
            'approved_works'    => $author->approved_works_count,
            'pending_works'     => $author->pending_works_count,
            'total_views'       => $totalViews,
            'total_comments'    => $totalComments,
            'total_favorites'   => $totalFavorites,
            'avg_views_per_work' => $avgViewsPerWork,
            'first_published'   => $firstPublished ? Carbon::parse($firstPublished) : null,
        ];

        $workIds = $approvedWorks->pluck('id');
        $dailyViews = $this->getAuthorDailyViews($author, 30);
        $weeklyViews = $this->getAuthorDailyViews($author, 7);
        $topWorks = $this->getAuthorTopWorks($author, 10);
        $monthlyComparison = $this->getMonthlyComparison($author);
        $categoryDistribution = $this->getCategoryDistribution($author);
        $workViewsChart = $this->getWorkViewsChartData($approvedWorks);
        $weeklyTrend = $this->getWeeklyTrend($workIds);
        $monthlyTrend = $this->getMonthlyTrend($workIds);

        return [
            'author'                => $author,
            'workStats'             => $workStats,
            'dailyViews'            => $dailyViews,
            'weeklyViews'           => $weeklyViews,
            'topWorks'              => $topWorks,
            'monthlyComparison'     => $monthlyComparison,
            'categoryDistribution'  => $categoryDistribution,
            'workViewsChart'        => $workViewsChart,
            'weeklyTrend'           => $weeklyTrend,
            'monthlyTrend'          => $monthlyTrend,
        ];
    }

    // ─── Private Helpers ───

    private function getTopAuthorByTotalViews(): array
    {
        $result = DB::table('literary_works')
            ->join('users', 'literary_works.user_id', '=', 'users.id')
            ->where('literary_works.status', LiteraryWorkStatus::Approved->value)
            ->whereNull('literary_works.deleted_at')
            ->whereNull('users.deleted_at')
            ->select('users.name', DB::raw('SUM(literary_works.view_count) as total_views'))
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_views')
            ->first();

        return $result ? ['name' => $result->name, 'views' => (int) $result->total_views] : [];
    }

    private function getTopPublisherThisMonth(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();

        $result = DB::table('literary_works')
            ->join('users', 'literary_works.user_id', '=', 'users.id')
            ->where('literary_works.status', LiteraryWorkStatus::Approved->value)
            ->where('literary_works.published_at', '>=', $startOfMonth)
            ->whereNull('literary_works.deleted_at')
            ->whereNull('users.deleted_at')
            ->select('users.name', DB::raw('COUNT(*) as works_count'))
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('works_count')
            ->first();

        return $result ? ['name' => $result->name, 'count' => (int) $result->works_count] : [];
    }

    private function getAuthorDailyViews(User $author, int $days): array
    {
        $startDate = Carbon::now()->subDays($days - 1)->toDateString();

        $workIds = $author->literaryWorks()
            ->where('status', LiteraryWorkStatus::Approved)
            ->pluck('id');

        if ($workIds->isEmpty()) {
            return ['labels' => [], 'values' => []];
        }

        $data = DailyView::where('viewable_type', LiteraryWork::class)
            ->whereIn('viewable_id', $workIds)
            ->where('view_date', '>=', $startDate)
            ->selectRaw('view_date, SUM(view_count) as total')
            ->groupBy('view_date')
            ->orderBy('view_date')
            ->pluck('total', 'view_date')
            ->toArray();

        $labels = [];
        $values = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $key = $date->toDateString();
            $labels[] = $date->format('d M');
            $values[] = (int) ($data[$key] ?? 0);
        }

        return ['labels' => $labels, 'values' => $values];
    }

    private function getAuthorTopWorks(User $author, int $limit): Collection
    {
        return $author->literaryWorks()
            ->with('category:id,name')
            ->where('status', LiteraryWorkStatus::Approved)
            ->withCount('approvedComments')
            ->withCount('favorites')
            ->orderByDesc('view_count')
            ->limit($limit)
            ->get(['id', 'title', 'slug', 'view_count', 'literary_category_id', 'work_type', 'published_at']);
    }

    private function getMonthlyComparison(User $author): array
    {
        $workIds = $author->literaryWorks()
            ->where('status', LiteraryWorkStatus::Approved)
            ->pluck('id');

        if ($workIds->isEmpty()) {
            return ['this_month' => 0, 'last_month' => 0, 'change_percent' => 0];
        }

        $thisMonthStart = Carbon::now()->startOfMonth()->toDateString();
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth()->toDateString();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth()->toDateString();

        $thisMonth = (int) DailyView::where('viewable_type', LiteraryWork::class)
            ->whereIn('viewable_id', $workIds)
            ->where('view_date', '>=', $thisMonthStart)
            ->sum('view_count');

        $lastMonth = (int) DailyView::where('viewable_type', LiteraryWork::class)
            ->whereIn('viewable_id', $workIds)
            ->whereBetween('view_date', [$lastMonthStart, $lastMonthEnd])
            ->sum('view_count');

        $changePercent = $lastMonth > 0
            ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1)
            : ($thisMonth > 0 ? 100.0 : 0.0);

        return [
            'this_month'     => $thisMonth,
            'last_month'     => $lastMonth,
            'change_percent' => $changePercent,
        ];
    }

    private function getCategoryDistribution(User $author): Collection
    {
        return $author->literaryWorks()
            ->where('status', LiteraryWorkStatus::Approved)
            ->join('literary_categories', 'literary_works.literary_category_id', '=', 'literary_categories.id')
            ->select('literary_categories.name', DB::raw('COUNT(*) as count'), DB::raw('SUM(literary_works.view_count) as total_views'))
            ->groupBy('literary_categories.id', 'literary_categories.name')
            ->orderByDesc('count')
            ->get();
    }

    private function getWeeklyTrend(Collection $workIds): array
    {
        $labels = [];
        $values = [];

        if ($workIds->isEmpty()) {
            return ['labels' => array_fill(0, 4, '-'), 'values' => array_fill(0, 4, 0)];
        }

        for ($i = 3; $i >= 0; $i--) {
            $weekStart = Carbon::now()->subWeeks($i)->startOfWeek()->toDateString();
            $weekEnd = Carbon::now()->subWeeks($i)->endOfWeek()->toDateString();

            $total = (int) DailyView::where('viewable_type', LiteraryWork::class)
                ->whereIn('viewable_id', $workIds)
                ->whereBetween('view_date', [$weekStart, $weekEnd])
                ->sum('view_count');

            $labels[] = Carbon::parse($weekStart)->format('d M') . ' – ' . Carbon::parse($weekEnd)->format('d M');
            $values[] = $total;
        }

        return ['labels' => $labels, 'values' => $values];
    }

    private function getMonthlyTrend(Collection $workIds): array
    {
        $labels = [];
        $values = [];

        if ($workIds->isEmpty()) {
            return ['labels' => array_fill(0, 6, '-'), 'values' => array_fill(0, 6, 0)];
        }

        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth()->toDateString();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth()->toDateString();

            $total = (int) DailyView::where('viewable_type', LiteraryWork::class)
                ->whereIn('viewable_id', $workIds)
                ->whereBetween('view_date', [$monthStart, $monthEnd])
                ->sum('view_count');

            $labels[] = Carbon::now()->subMonths($i)->translatedFormat('M Y');
            $values[] = $total;
        }

        return ['labels' => $labels, 'values' => $values];
    }

    private function getWorkViewsChartData(Collection $approvedWorks): array
    {
        $sorted = $approvedWorks->sortByDesc('view_count')->take(10);

        $labels = [];
        $views = [];
        $comments = [];
        $favorites = [];

        foreach ($sorted as $work) {
            $labels[] = \Illuminate\Support\Str::limit($work->title, 30);
            $views[] = (int) $work->view_count;
            $comments[] = (int) $work->approved_comments_count;
            $favorites[] = (int) $work->favorites_count;
        }

        return [
            'labels'    => $labels,
            'views'     => $views,
            'comments'  => $comments,
            'favorites' => $favorites,
        ];
    }

    // ─── Cache Invalidation ───

    public function clearCache(): void
    {
        Cache::forget('author_statistics.summary');
    }
}
