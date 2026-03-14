<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Services\WriterApplicationService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService,
        private readonly WriterApplicationService $writerApplicationService,
    ) {}

    public function index(): View
    {
        return view('admin.dashboard', [
            'stats'            => $this->dashboardService->getStats(),
            'monthlyUsers'     => $this->dashboardService->getMonthlyUserRegistrations(),
            'monthlyWorks'     => $this->dashboardService->getMonthlyWorks(),
            'roleDistribution' => $this->dashboardService->getRoleDistribution(),
            'workStatus'       => $this->dashboardService->getWorkStatusDistribution(),
            'latestWorks'      => $this->dashboardService->getLatestWorks(),
            'latestComments'   => $this->dashboardService->getLatestComments(),
            'latestUsers'      => $this->dashboardService->getLatestUsers(),
            'topAuthors'       => $this->dashboardService->getTopAuthors(),
            'pendingWriterApplications' => $this->writerApplicationService->getPendingCount(),
        ]);
    }
}
