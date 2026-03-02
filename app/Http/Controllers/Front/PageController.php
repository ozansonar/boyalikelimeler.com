<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\PageService;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(
        private readonly PageService $pageService,
    ) {}

    public function show(string $slug): View
    {
        $page = $this->pageService->findActiveBySlug($slug);

        if (! $page) {
            abort(404);
        }

        return view('front.page.show', [
            'page' => $page,
        ]);
    }
}
