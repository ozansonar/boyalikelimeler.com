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

        $filters = $request->only(['search', 'sort', 'dir']);

        return view('admin.author-statistics.index', [
            'authors'  => $this->statisticsService->paginateAuthors($perPage, $filters),
            'stats'    => $this->statisticsService->getSummaryStats(),
            'filters'  => $filters,
            'perPage'  => $perPage,
        ]);
    }

    public function show(User $user): View
    {
        $data = $this->statisticsService->getAuthorDetail($user);

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
