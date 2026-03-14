<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Enums\LiteraryWorkType;
use App\Http\Controllers\Controller;
use App\Services\LiteraryCategoryService;
use App\Services\LiteraryWorkService;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LiteraryWorkController extends Controller
{
    public function __construct(
        private readonly LiteraryWorkService $workService,
        private readonly LiteraryCategoryService $categoryService,
        private readonly ProfileService $profileService,
    ) {}

    public function index(Request $request): View
    {
        return view('front.literary-works.index', [
            'works'           => $this->workService->frontPaginate(12, [
                'search'    => $request->input('ara'),
                'category'  => $request->input('kategori'),
                'sort'      => $request->input('sirala'),
                'work_type' => $request->input('tur'),
            ]),
            'categories'      => $this->categoryService->activeList(),
            'stats'           => $this->workService->getPublishedStats(),
            'currentCategory' => $request->input('kategori'),
            'currentSort'     => $request->input('sirala', 'newest'),
            'currentWorkType' => $request->input('tur'),
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

        $authorStats = $this->profileService->getWriterStats($work->author);
        $hasWritten  = ($authorStats['work_type_counts'][LiteraryWorkType::Written->value] ?? 0) > 0;
        $hasVisual   = ($authorStats['work_type_counts'][LiteraryWorkType::Visual->value] ?? 0) > 0;

        if ($hasWritten && $hasVisual) {
            $authorRoleLabel = 'Yazar ve Ressam';
            $authorRoleIcon  = 'fa-solid fa-feather-pointed';
        } elseif ($hasVisual) {
            $authorRoleLabel = 'Ressam';
            $authorRoleIcon  = 'fa-solid fa-palette';
        } else {
            $authorRoleLabel = 'Yazar';
            $authorRoleIcon  = 'fa-solid fa-user-pen';
        }

        return view('front.literary-works.show', [
            'work'             => $work,
            'relatedWorks'     => $this->workService->getRelatedWorks($work, 4),
            'categories'       => $this->categoryService->activeList(),
            'authorRoleLabel'  => $authorRoleLabel,
            'authorRoleIcon'   => $authorRoleIcon,
        ]);
    }
}
