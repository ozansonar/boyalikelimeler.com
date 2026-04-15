<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\PwaInstallPlatform;
use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Services\PwaInstallService;
use App\Services\WriterApplicationService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService,
        private readonly WriterApplicationService $writerApplicationService,
        private readonly PwaInstallService $pwaInstallService,
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
            'pwaStats'          => $this->pwaInstallService->getStats(),
            'pwaPlatforms'      => $this->pwaInstallService->getPlatformDistribution(),
            'pwaMonthly'        => $this->pwaInstallService->getMonthlyTrend(12),
            'pwaPlatformCases'  => PwaInstallPlatform::cases(),
        ]);
    }
}
