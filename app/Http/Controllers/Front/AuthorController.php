<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\AuthorService;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthorController extends Controller
{
    public function __construct(
        private readonly AuthorService $authorService,
        private readonly SettingService $settingService,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'golden_pen', 'sort', 'dir']);
        $pageSettings = $this->settingService->getGroup('authors_page');

        $featuredAuthors = $this->authorService->getFeaturedAuthors($pageSettings['featured_author_ids'] ?? null);
        $goldenPenMonths = $this->authorService->getGoldenPenMonths();

        return view('front.authors.index', [
            'authors'          => $this->authorService->paginate(12, $filters),
            'filters'          => $filters,
            'pageSettings'     => $pageSettings,
            'featuredAuthors'  => $featuredAuthors,
            'goldenPenMonths'  => $goldenPenMonths,
        ]);
    }

    public function goldenPenMonth(string $yearMonth): View
    {
        $pageSettings = $this->settingService->getGroup('authors_page');
        $monthData = $this->authorService->getGoldenPenAuthorsByMonth($yearMonth);

        if ($monthData === null) {
            abort(404);
        }

        $hasAuthors = $monthData['authors']->isNotEmpty();

        return view('front.authors.golden-pen-month', [
            'monthData'    => $monthData,
            'yearMonth'    => $yearMonth,
            'pageSettings' => $pageSettings,
            'hasAuthors'   => $hasAuthors,
        ]);
    }
}
