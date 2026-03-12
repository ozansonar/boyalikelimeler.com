<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\PainterService;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PainterController extends Controller
{
    public function __construct(
        private readonly PainterService $painterService,
        private readonly SettingService $settingService,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'golden_brush', 'sort', 'dir']);
        $pageSettings = $this->settingService->getGroup('painters_page');

        $featuredPainters = $this->painterService->getFeaturedPainters($pageSettings['featured_painter_ids'] ?? null);
        $goldenBrushMonths = $this->painterService->getGoldenBrushMonths();

        return view('front.painters.index', [
            'painters'           => $this->painterService->paginate(12, $filters),
            'filters'            => $filters,
            'pageSettings'       => $pageSettings,
            'featuredPainters'   => $featuredPainters,
            'goldenBrushMonths'  => $goldenBrushMonths,
        ]);
    }

    public function goldenBrushIndex(): View
    {
        $pageSettings = $this->settingService->getGroup('painters_page');

        return view('front.painters.golden-brush-index', [
            'months'       => $this->painterService->getGoldenBrushMonthsPaginated(12),
            'pageSettings' => $pageSettings,
        ]);
    }

    public function goldenBrushMonth(string $yearMonth): View
    {
        $pageSettings = $this->settingService->getGroup('painters_page');
        $monthData = $this->painterService->getGoldenBrushPaintersByMonth($yearMonth);

        if ($monthData === null) {
            abort(404);
        }

        $hasPainters = $monthData['painters']->isNotEmpty();

        return view('front.painters.golden-brush-month', [
            'monthData'    => $monthData,
            'yearMonth'    => $yearMonth,
            'pageSettings' => $pageSettings,
            'hasPainters'  => $hasPainters,
        ]);
    }
}
