<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\LiteraryWorkStatus;
use App\Enums\LiteraryWorkType;
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
    public function getSummaryStats(?string $workType = null): array
    {
        $cacheKey = 'author_statistics.summary' . ($workType ? ".{$workType}" : '');

        return Cache::remember($cacheKey, 300, function () use ($workType): array {
            $workTypeFilter = fn ($q) => $workType
                ? $q->where('status', LiteraryWorkStatus::Approved)->where('work_type', $workType)
                : $q->where('status', LiteraryWorkStatus::Approved);

            $totalAuthors = User::whereHas('literaryWorks', $workTypeFilter)->count();

            $topAuthor = $this->getTopAuthorByTotalViews($workType);
            $topPublisherThisMonth = $this->getTopPublisherThisMonth($workType);

            $worksQuery = LiteraryWork::where('status', LiteraryWorkStatus::Approved);
            if ($workType) {
                $worksQuery->where('work_type', $workType);
            }
            $aggregate = (clone $worksQuery)
                ->selectRaw('COUNT(*) as total_count, COALESCE(SUM(view_count), 0) as total_views')
                ->first();
            $totalViews = (int) $aggregate->total_views;
            $totalApprovedWorks = (int) $aggregate->total_count;

            $avgViews = $totalAuthors > 0 ? (int) round($totalViews / $totalAuthors) : 0;

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

    public function paginateAuthors(int $perPage, array $filters = [], ?int $precomputedTotal = null): LengthAwarePaginator
    {
        $workType = $filters['work_type'] ?? null;

        $workTypeFilter = fn ($q) => $workType
            ? $q->where('status', LiteraryWorkStatus::Approved)->where('work_type', $workType)
            : $q->where('status', LiteraryWorkStatus::Approved);

        $query = User::select('users.*')
            ->whereHas('literaryWorks', $workTypeFilter)
            ->withCount(['literaryWorks as approved_works_count' => $workTypeFilter])
            ->withSum(['literaryWorks as total_views' => $workTypeFilter], 'view_count');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['min_works'])) {
            $minWorks = (int) $filters['min_works'];
            $query->whereHas('literaryWorks', function ($q) use ($workType): void {
                $q->where('status', LiteraryWorkStatus::Approved);
                if ($workType) {
                    $q->where('work_type', $workType);
                }
            }, '>=', $minWorks);
        }

        if (!empty($filters['joined'])) {
            $joinedDate = match ($filters['joined']) {
                'this_month' => Carbon::now()->startOfMonth(),
                'last_3'     => Carbon::now()->subMonths(3)->startOfDay(),
                'last_6'     => Carbon::now()->subMonths(6)->startOfDay(),
                'last_12'    => Carbon::now()->subYear()->startOfDay(),
                default      => null,
            };
            if ($joinedDate) {
                $query->where('users.created_at', '>=', $joinedDate);
            }
        }

        if (!empty($filters['activity'])) {
            $activityDays = match ($filters['activity']) {
                'last_7'  => 7,
                'last_30' => 30,
                'last_90' => 90,
                default   => null,
            };
            if ($activityDays !== null) {
                $cutoff = Carbon::now()->subDays($activityDays)->toDateString();
                $lwClass2 = LiteraryWork::class;
                $approvedVal = LiteraryWorkStatus::Approved->value;
                $query->whereExists(function ($sub) use ($cutoff, $lwClass2, $approvedVal, $workType): void {
                    $sub->select(DB::raw(1))
                        ->from('daily_views')
                        ->join('literary_works', function ($join) use ($lwClass2): void {
                            $join->on('daily_views.viewable_id', '=', 'literary_works.id')
                                ->where('daily_views.viewable_type', '=', $lwClass2);
                        })
                        ->whereColumn('literary_works.user_id', 'users.id')
                        ->where('literary_works.status', $approvedVal)
                        ->whereNull('literary_works.deleted_at')
                        ->where('daily_views.view_date', '>=', $cutoff);
                    if ($workType) {
                        $sub->where('literary_works.work_type', $workType);
                    }
                });
            }
            if ($filters['activity'] === 'inactive') {
                $cutoff90 = Carbon::now()->subDays(90)->toDateString();
                $lwClass2 = LiteraryWork::class;
                $approvedVal = LiteraryWorkStatus::Approved->value;
                $query->whereNotExists(function ($sub) use ($cutoff90, $lwClass2, $approvedVal, $workType): void {
                    $sub->select(DB::raw(1))
                        ->from('daily_views')
                        ->join('literary_works', function ($join) use ($lwClass2): void {
                            $join->on('daily_views.viewable_id', '=', 'literary_works.id')
                                ->where('daily_views.viewable_type', '=', $lwClass2);
                        })
                        ->whereColumn('literary_works.user_id', 'users.id')
                        ->where('literary_works.status', $approvedVal)
                        ->whereNull('literary_works.deleted_at')
                        ->where('daily_views.view_date', '>=', $cutoff90);
                    if ($workType) {
                        $sub->where('literary_works.work_type', $workType);
                    }
                });
            }
        }

        $now = Carbon::now();
        $lwClass = LiteraryWork::class;
        $approvedStatus = LiteraryWorkStatus::Approved->value;

        $buildViewSubquery = function (int $days) use ($lwClass, $approvedStatus, $now, $workType) {
            $sub = DailyView::selectRaw('COALESCE(SUM(daily_views.view_count), 0)')
                ->join('literary_works', function ($join) use ($lwClass): void {
                    $join->on('daily_views.viewable_id', '=', 'literary_works.id')
                        ->where('daily_views.viewable_type', '=', $lwClass);
                })
                ->whereColumn('literary_works.user_id', 'users.id')
                ->where('literary_works.status', $approvedStatus)
                ->whereNull('literary_works.deleted_at')
                ->where('daily_views.view_date', '>=', $now->copy()->subDays($days)->toDateString());

            if ($workType) {
                $sub->where('literary_works.work_type', $workType);
            }

            return $sub;
        };

        $commentCountSubquery = \App\Models\Comment::selectRaw('COUNT(*)')
            ->where('commentable_type', $lwClass)
            ->whereIn('commentable_id', function ($q) use ($approvedStatus, $workType): void {
                $q->select('id')
                    ->from('literary_works')
                    ->whereColumn('literary_works.user_id', 'users.id')
                    ->where('literary_works.status', $approvedStatus)
                    ->whereNull('literary_works.deleted_at');
                if ($workType) {
                    $q->where('literary_works.work_type', $workType);
                }
            })
            ->where('is_approved', true);

        $totalCommentsSubquery = DB::table('comments')
            ->selectRaw('COUNT(*)')
            ->where('commentable_type', $lwClass)
            ->whereIn('commentable_id', function ($q) use ($approvedStatus, $workType): void {
                $q->select('id')
                    ->from('literary_works')
                    ->whereColumn('literary_works.user_id', 'users.id')
                    ->where('literary_works.status', $approvedStatus)
                    ->whereNull('literary_works.deleted_at');
                if ($workType) {
                    $q->where('literary_works.work_type', $workType);
                }
            })
            ->where('is_approved', true)
            ->whereNull('deleted_at');

        $totalFavoritesSubquery = DB::table('favorites')
            ->selectRaw('COUNT(*)')
            ->where('favoriteable_type', $lwClass)
            ->whereIn('favoriteable_id', function ($q) use ($approvedStatus, $workType): void {
                $q->select('id')
                    ->from('literary_works')
                    ->whereColumn('literary_works.user_id', 'users.id')
                    ->where('literary_works.status', $approvedStatus)
                    ->whereNull('literary_works.deleted_at');
                if ($workType) {
                    $q->where('literary_works.work_type', $workType);
                }
            });

        $avgRatingSubquery = DB::table('comments')
            ->selectRaw('ROUND(AVG(rating), 1)')
            ->where('commentable_type', $lwClass)
            ->whereIn('commentable_id', function ($q) use ($approvedStatus, $workType): void {
                $q->select('id')
                    ->from('literary_works')
                    ->whereColumn('literary_works.user_id', 'users.id')
                    ->where('literary_works.status', $approvedStatus)
                    ->whereNull('literary_works.deleted_at');
                if ($workType) {
                    $q->where('literary_works.work_type', $workType);
                }
            })
            ->where('is_approved', true)
            ->whereNull('deleted_at')
            ->whereNotNull('rating')
            ->where('rating', '>', 0);

        $query->addSelect([
            'views_last_7d'   => $buildViewSubquery(7),
            'views_last_30d'  => $buildViewSubquery(30),
            'views_last_90d'  => $buildViewSubquery(90),
            'total_comments'  => $totalCommentsSubquery,
            'total_favorites' => $totalFavoritesSubquery,
            'avg_rating'      => $avgRatingSubquery,
        ]);

        $sort = $filters['sort'] ?? 'total_views';
        $dir = $filters['dir'] ?? 'desc';
        $allowedSorts = ['name', 'approved_works_count', 'total_views', 'views_last_7d', 'views_last_30d', 'views_last_90d', 'total_comments', 'total_favorites', 'avg_rating', 'created_at'];
        $sort = in_array($sort, $allowedSorts, true) ? $sort : 'total_views';
        $dir = in_array($dir, ['asc', 'desc'], true) ? $dir : 'desc';

        $query->orderBy($sort, $dir);

        if ($precomputedTotal !== null) {
            $page = \Illuminate\Pagination\Paginator::resolveCurrentPage();
            $items = $query->forPage($page, $perPage)->get();

            return (new \Illuminate\Pagination\LengthAwarePaginator(
                $items,
                $precomputedTotal,
                $perPage,
                $page,
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()],
            ))->withQueryString();
        }

        return $query->paginate($perPage)->withQueryString();
    }

    // ─── Author Detail Data ───

    /**
     * @return array{author: User, work_stats: array, daily_views: array, top_works: Collection, monthly_comparison: array}
     */
    public function getAuthorDetail(User $author, ?string $workType = null): array
    {
        $approvedFilter = fn ($q) => $workType
            ? $q->where('status', LiteraryWorkStatus::Approved)->where('work_type', $workType)
            : $q->where('status', LiteraryWorkStatus::Approved);

        $pendingFilter = fn ($q) => $workType
            ? $q->where('status', LiteraryWorkStatus::Pending)->where('work_type', $workType)
            : $q->where('status', LiteraryWorkStatus::Pending);

        $totalFilter = fn ($q) => $workType
            ? $q->where('work_type', $workType)
            : $q;

        $author->loadCount([
            'literaryWorks as total_works_count' => $totalFilter,
            'literaryWorks as approved_works_count' => $approvedFilter,
            'literaryWorks as pending_works_count' => $pendingFilter,
        ]);

        $approvedWorksQuery = $author->literaryWorks()
            ->where('status', LiteraryWorkStatus::Approved);

        if ($workType) {
            $approvedWorksQuery->where('work_type', $workType);
        }

        $approvedWorks = $approvedWorksQuery
            ->withCount(['approvedComments', 'favorites'])
            ->get();

        $totalViews = (int) $approvedWorks->sum('view_count');
        $totalComments = (int) $approvedWorks->sum('approved_comments_count');
        $totalFavorites = (int) $approvedWorks->sum('favorites_count');
        $avgViewsPerWork = $approvedWorks->count() > 0 ? (int) round($totalViews / $approvedWorks->count()) : 0;

        $firstPublishedQuery = $author->literaryWorks()
            ->where('status', LiteraryWorkStatus::Approved)
            ->whereNotNull('published_at');

        if ($workType) {
            $firstPublishedQuery->where('work_type', $workType);
        }

        $firstPublished = $firstPublishedQuery->orderBy('published_at')->value('published_at');

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
        $allViewData = $this->fetchAllViewData($workIds);
        $dailyViews = $this->formatDailyViews($allViewData, 30);
        $weeklyViews = $this->formatDailyViews($allViewData, 7);
        $topWorks = $this->getAuthorTopWorks($author, 10, $workType);
        $categoryDistribution = $this->getCategoryDistribution($author, $workType);
        $workViewsChart = $this->getWorkViewsChartData($approvedWorks);
        $weeklyTrend = $this->buildWeeklyTrend($allViewData);
        $monthlyTrend = $this->buildMonthlyTrend($allViewData);
        $monthlyComparison = $this->buildMonthlyComparison($monthlyTrend);

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

    private function getTopAuthorByTotalViews(?string $workType = null): array
    {
        $query = DB::table('literary_works')
            ->join('users', 'literary_works.user_id', '=', 'users.id')
            ->where('literary_works.status', LiteraryWorkStatus::Approved->value)
            ->whereNull('literary_works.deleted_at')
            ->whereNull('users.deleted_at')
            ->select('users.name', DB::raw('SUM(literary_works.view_count) as total_views'))
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_views');

        if ($workType) {
            $query->where('literary_works.work_type', $workType);
        }

        $result = $query->first();

        return $result ? ['name' => $result->name, 'views' => (int) $result->total_views] : [];
    }

    private function getTopPublisherThisMonth(?string $workType = null): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();

        $query = DB::table('literary_works')
            ->join('users', 'literary_works.user_id', '=', 'users.id')
            ->where('literary_works.status', LiteraryWorkStatus::Approved->value)
            ->where('literary_works.published_at', '>=', $startOfMonth)
            ->whereNull('literary_works.deleted_at')
            ->whereNull('users.deleted_at')
            ->select('users.name', DB::raw('COUNT(*) as works_count'))
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('works_count');

        if ($workType) {
            $query->where('literary_works.work_type', $workType);
        }

        $result = $query->first();

        return $result ? ['name' => $result->name, 'count' => (int) $result->works_count] : [];
    }

    /**
     * @return array<string, int>
     */
    private function fetchAllViewData(Collection $workIds): array
    {
        if ($workIds->isEmpty()) {
            return [];
        }

        $startDate = Carbon::now()->subMonths(5)->startOfMonth()->toDateString();

        return DailyView::where('viewable_type', LiteraryWork::class)
            ->whereIn('viewable_id', $workIds)
            ->where('view_date', '>=', $startDate)
            ->selectRaw('view_date, SUM(view_count) as total')
            ->groupBy('view_date')
            ->orderBy('view_date')
            ->pluck('total', 'view_date')
            ->toArray();
    }

    /**
     * @param array<string, int> $data
     */
    private function formatDailyViews(array $data, int $days): array
    {
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

    private function getAuthorTopWorks(User $author, int $limit, ?string $workType = null): Collection
    {
        $query = $author->literaryWorks()
            ->with('category:id,name')
            ->where('status', LiteraryWorkStatus::Approved)
            ->withCount('approvedComments')
            ->withCount('favorites');

        if ($workType) {
            $query->where('work_type', $workType);
        }

        return $query->orderByDesc('view_count')
            ->limit($limit)
            ->get(['id', 'title', 'slug', 'view_count', 'literary_category_id', 'work_type', 'published_at']);
    }

    private function buildMonthlyComparison(array $monthlyTrend): array
    {
        $values = $monthlyTrend['values'] ?? [];
        $count = count($values);

        $thisMonth = $count >= 1 ? (int) $values[$count - 1] : 0;
        $lastMonth = $count >= 2 ? (int) $values[$count - 2] : 0;

        $changePercent = $lastMonth > 0
            ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1)
            : ($thisMonth > 0 ? 100.0 : 0.0);

        return [
            'this_month'     => $thisMonth,
            'last_month'     => $lastMonth,
            'change_percent' => $changePercent,
        ];
    }

    private function getCategoryDistribution(User $author, ?string $workType = null): Collection
    {
        $query = $author->literaryWorks()
            ->where('status', LiteraryWorkStatus::Approved);

        if ($workType) {
            $query->where('literary_works.work_type', $workType);
        }

        return $query
            ->join('literary_categories', 'literary_works.literary_category_id', '=', 'literary_categories.id')
            ->select('literary_categories.name', DB::raw('COUNT(*) as count'), DB::raw('SUM(literary_works.view_count) as total_views'))
            ->groupBy('literary_categories.id', 'literary_categories.name')
            ->orderByDesc('count')
            ->get();
    }

    /**
     * @param array<string, int> $viewData
     */
    private function buildWeeklyTrend(array $viewData): array
    {
        if (empty($viewData)) {
            return ['labels' => array_fill(0, 4, '-'), 'values' => array_fill(0, 4, 0)];
        }

        $labels = [];
        $values = [];
        for ($i = 3; $i >= 0; $i--) {
            $ws = Carbon::now()->subWeeks($i)->startOfWeek();
            $we = Carbon::now()->subWeeks($i)->endOfWeek();
            $labels[] = $ws->format('d M') . ' – ' . $we->format('d M');

            $weekTotal = 0;
            $cursor = $ws->copy();
            while ($cursor->lte($we)) {
                $weekTotal += (int) ($viewData[$cursor->toDateString()] ?? 0);
                $cursor->addDay();
            }
            $values[] = $weekTotal;
        }

        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * @param array<string, int> $viewData
     */
    private function buildMonthlyTrend(array $viewData): array
    {
        if (empty($viewData)) {
            return ['labels' => array_fill(0, 6, '-'), 'values' => array_fill(0, 6, 0)];
        }

        $labels = [];
        $values = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $ms = $month->copy()->startOfMonth();
            $me = $month->copy()->endOfMonth();
            $labels[] = $month->translatedFormat('M Y');

            $monthTotal = 0;
            $cursor = $ms->copy();
            while ($cursor->lte($me)) {
                $monthTotal += (int) ($viewData[$cursor->toDateString()] ?? 0);
                $cursor->addDay();
            }
            $values[] = $monthTotal;
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

        foreach (LiteraryWorkType::cases() as $type) {
            Cache::forget("author_statistics.summary.{$type->value}");
        }
    }
}
