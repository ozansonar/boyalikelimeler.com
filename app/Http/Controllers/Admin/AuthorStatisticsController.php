<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthorStatisticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthorStatisticsController extends Controller
{
    public function __construct(
        private readonly AuthorStatisticsService $statisticsService,
    ) {}

    public function index(Request $request): View
    {
        $perPage = in_array((int) $request->input('per_page'), [10, 25, 50, 100], true)
            ? (int) $request->input('per_page')
            : 10;

        $filters = $request->only(['search', 'sort', 'dir', 'work_type', 'min_works', 'joined', 'activity']);

        $stats = $this->statisticsService->getSummaryStats($filters['work_type'] ?? null);

        $hasExtraFilters = !empty($filters['search']) || !empty($filters['min_works'])
            || !empty($filters['joined']) || !empty($filters['activity']);

        $authors = $this->statisticsService->paginateAuthors(
            $perPage,
            $filters,
            !$hasExtraFilters ? $stats['total_authors'] : null,
        );

        return view('admin.author-statistics.index', [
            'authors'  => $authors,
            'stats'    => $stats,
            'filters'  => $filters,
            'perPage'  => $perPage,
        ]);
    }

    public function show(Request $request, User $user): View
    {
        $workType = $request->input('work_type');
        $data = $this->statisticsService->getAuthorDetail($user, $workType);
        $data['workType'] = $workType;

        $catColors = ['#14b8a6','#3b82f6','#f97316','#a855f7','#22c55e','#ef4444','#eab308','#ec4899'];

        $data['chartDataJson'] = json_encode([
            'weekly'         => $data['weeklyViews'],
            'monthly'        => $data['dailyViews'],
            'weeklyTrend'    => $data['weeklyTrend'],
            'monthlyTrend'   => $data['monthlyTrend'],
            'category'       => [
                'labels' => $data['categoryDistribution']->pluck('name')->values(),
                'values' => $data['categoryDistribution']->pluck('count')->values()->map(fn ($v) => (int) $v),
                'colors' => $catColors,
            ],
            'workComparison' => $data['workViewsChart'],
        ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        $data['catColors'] = $catColors;

        return view('admin.author-statistics.show', $data);
    }
}
