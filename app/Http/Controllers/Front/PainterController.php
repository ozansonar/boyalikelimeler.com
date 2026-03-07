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
        $filters = $request->only(['search', 'sort', 'dir']);
        $pageSettings = $this->settingService->getGroup('painters_page');

        $featuredPainters = $this->painterService->getFeaturedPainters($pageSettings['featured_painter_ids'] ?? null);

        return view('front.painters.index', [
            'painters'          => $this->painterService->paginate(12, $filters),
            'filters'           => $filters,
            'pageSettings'      => $pageSettings,
            'featuredPainters'  => $featuredPainters,
        ]);
    }
}
