<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\LiteraryCategoryService;
use App\Services\LiteraryWorkService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LiteraryWorkController extends Controller
{
    public function __construct(
        private readonly LiteraryWorkService $workService,
        private readonly LiteraryCategoryService $categoryService,
    ) {}

    public function index(Request $request): View
    {
        return view('front.literary-works.index', [
            'works'           => $this->workService->frontPaginate(12, [
                'search'   => $request->input('ara'),
                'category' => $request->input('kategori'),
                'sort'     => $request->input('sirala'),
            ]),
            'categories'      => $this->categoryService->activeList(),
            'stats'           => $this->workService->getPublishedStats(),
            'currentCategory' => $request->input('kategori'),
            'currentSort'     => $request->input('sirala', 'newest'),
        ]);
    }

    public function writtenWorks(Request $request): View
    {
        return view('front.literary-works.written', [
            'works'       => $this->workService->frontPaginate(12, [
                'search'    => $request->input('ara'),
                'sort'      => $request->input('sirala'),
                'work_type' => 'written',
            ]),
            'stats'       => $this->workService->getPublishedStatsByType('written'),
            'currentSort' => $request->input('sirala', 'newest'),
        ]);
    }

    public function visualWorks(Request $request): View
    {
        return view('front.literary-works.visual', [
            'works'       => $this->workService->frontPaginate(12, [
                'search'    => $request->input('ara'),
                'sort'      => $request->input('sirala'),
                'work_type' => 'visual',
            ]),
            'stats'       => $this->workService->getPublishedStatsByType('visual'),
            'currentSort' => $request->input('sirala', 'newest'),
        ]);
    }

    public function show(string $slug): View
    {
        $work = $this->workService->findPublishedBySlug($slug);

        if (! $work) {
            abort(404);
        }

        $this->workService->incrementViews($work);

        return view('front.literary-works.show', [
            'work'         => $work,
            'relatedWorks' => $this->workService->getRelatedWorks($work, 4),
            'categories'   => $this->categoryService->activeList(),
        ]);
    }
}
