<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WorkStatisticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkStatisticsController extends Controller
{
    public function __construct(
        private readonly WorkStatisticsService $workStatisticsService,
    ) {}

    public function index(Request $request): View
    {
        $perPage = in_array((int) $request->input('per_page'), [10, 25, 50, 100], true)
            ? (int) $request->input('per_page')
            : 10;

        $filters = $request->only([
            'search', 'sort', 'dir', 'work_type',
            'category', 'author', 'date_from', 'date_to',
        ]);

        $stats = $this->workStatisticsService->getSummaryStats($filters['work_type'] ?? null);

        $hasExtraFilters = !empty($filters['search']) || !empty($filters['category'])
            || !empty($filters['author']) || !empty($filters['date_from'])
            || !empty($filters['date_to']);

        $works = $this->workStatisticsService->paginateWorks(
            $perPage,
            $filters,
            !$hasExtraFilters ? $stats['total_works'] : null,
        );

        return view('admin.work-statistics.index', [
            'works'      => $works,
            'stats'      => $stats,
            'categories' => $this->workStatisticsService->getActiveCategories(),
            'authors'    => $this->workStatisticsService->getAuthorsWithWorks(),
            'filters'    => $filters,
            'perPage'    => $perPage,
        ]);
    }
}
