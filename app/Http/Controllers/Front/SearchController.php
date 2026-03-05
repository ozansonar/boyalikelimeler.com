<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __construct(
        private readonly SearchService $searchService,
    ) {}

    public function index(Request $request): View
    {
        $query = trim((string) $request->input('q', ''));
        $results = null;

        if (mb_strlen($query) >= 2) {
            $results = $this->searchService->search($query);
        }

        return view('front.search.index', [
            'query'   => $query,
            'results' => $results,
        ]);
    }
}
