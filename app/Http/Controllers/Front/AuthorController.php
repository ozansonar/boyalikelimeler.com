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

        $featuredAuthor = $this->authorService->getFeaturedAuthor($pageSettings['featured_author_id'] ?? null);
        $monthlyGoldenPen = $this->authorService->getMonthlyGoldenPenAuthors();

        return view('front.authors.index', [
            'authors'          => $this->authorService->paginate(12, $filters),
            'filters'          => $filters,
            'pageSettings'     => $pageSettings,
            'featuredAuthor'   => $featuredAuthor,
            'monthlyGoldenPen' => $monthlyGoldenPen,
        ]);
    }
}
