<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\LiteraryWorkStatus;
use App\Enums\LiteraryWorkType;
use App\Models\DailyView;
use App\Models\LiteraryWork;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class WorkStatisticsService
{
    // ─── Summary Stats (KPI cards) ───

    /**
     * @return array{total_works: int, total_views: int, today_views: int, avg_views_per_work: int, most_viewed_title: string, most_viewed_views: int}
     */
    public function getSummaryStats(?string $workType = null): array
    {
        $cacheKey = 'work_statistics.summary' . ($workType ? ".{$workType}" : '');

        return Cache::remember($cacheKey, 300, function () use ($workType): array {
            $baseQuery = LiteraryWork::where('status', LiteraryWorkStatus::Approved);

            if ($workType) {
                $baseQuery->where('work_type', $workType);
            }

            $totalWorks = (clone $baseQuery)->count();
            $totalViews = (int) (clone $baseQuery)->sum('view_count');
            $avgViews = $totalWorks > 0 ? (int) round($totalViews / $totalWorks) : 0;

            $todayViews = (int) DailyView::where('viewable_type', LiteraryWork::class)
                ->where('view_date', Carbon::today()->toDateString())
                ->when($workType, function ($q) use ($workType): void {
                    $q->whereIn('viewable_id', LiteraryWork::where('status', LiteraryWorkStatus::Approved)
                        ->where('work_type', $workType)
                        ->select('id'));
                }, function ($q): void {
                    $q->whereIn('viewable_id', LiteraryWork::where('status', LiteraryWorkStatus::Approved)
                        ->select('id'));
                })
                ->sum('view_count');

            $mostViewed = (clone $baseQuery)->orderByDesc('view_count')->first(['title', 'view_count']);

            return [
                'total_works'        => $totalWorks,
                'total_views'        => $totalViews,
                'today_views'        => $todayViews,
                'avg_views_per_work' => $avgViews,
                'most_viewed_title'  => $mostViewed?->title ?? '-',
                'most_viewed_views'  => (int) ($mostViewed?->view_count ?? 0),
            ];
        });
    }

    // ─── Paginated Works List ───

    public function paginateWorks(int $perPage, array $filters = []): LengthAwarePaginator
    {
        $query = LiteraryWork::with(['category:id,name', 'author:id,name,username,avatar'])
            ->where('status', LiteraryWorkStatus::Approved);

        if (!empty($filters['work_type'])) {
            $query->where('work_type', $filters['work_type']);
        }

        if (!empty($filters['category'])) {
            $query->where('literary_category_id', (int) $filters['category']);
        }

        if (!empty($filters['author'])) {
            $query->where('user_id', (int) $filters['author']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search): void {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('author', fn ($aq) => $aq->where('name', 'like', "%{$search}%"));
            });
        }

        if (!empty($filters['date_from'])) {
            $query->where('published_at', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }

        if (!empty($filters['date_to'])) {
            $query->where('published_at', '<=', Carbon::parse($filters['date_to'])->endOfDay());
        }

        $now = Carbon::now();
        $lwClass = LiteraryWork::class;

        $buildViewSubquery = function (int $days) use ($lwClass, $now) {
            return DailyView::selectRaw('COALESCE(SUM(daily_views.view_count), 0)')
                ->where('daily_views.viewable_type', $lwClass)
                ->whereColumn('daily_views.viewable_id', 'literary_works.id')
                ->where('daily_views.view_date', '>=', $now->copy()->subDays($days)->toDateString());
        };

        $query->withCount(['approvedComments', 'favorites'])
            ->addSelect([
                'views_last_7d'  => $buildViewSubquery(7),
                'views_last_30d' => $buildViewSubquery(30),
            ]);

        $sort = $filters['sort'] ?? 'view_count';
        $dir = $filters['dir'] ?? 'desc';
        $allowedSorts = [
            'title', 'view_count', 'views_last_7d', 'views_last_30d',
            'approved_comments_count', 'favorites_count', 'published_at',
        ];
        $sort = in_array($sort, $allowedSorts, true) ? $sort : 'view_count';
        $dir = in_array($dir, ['asc', 'desc'], true) ? $dir : 'desc';

        return $query->orderBy($sort, $dir)->paginate($perPage)->withQueryString();
    }

    // ─── Filter Data ───

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveCategories(): \Illuminate\Database\Eloquent\Collection
    {
        return \App\Models\LiteraryCategory::where('is_active', true)
            ->whereHas('approvedWorks')
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAuthorsWithWorks(): \Illuminate\Database\Eloquent\Collection
    {
        return \App\Models\User::whereHas('literaryWorks', fn ($q) => $q->where('status', LiteraryWorkStatus::Approved))
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    // ─── Cache Invalidation ───

    public function clearCache(): void
    {
        Cache::forget('work_statistics.summary');

        foreach (LiteraryWorkType::cases() as $type) {
            Cache::forget("work_statistics.summary.{$type->value}");
        }
    }
}
