<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\AuthorService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthorController extends Controller
{
    public function __construct(
        private readonly AuthorService $authorService,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'golden_pen', 'sort', 'dir']);

        return view('front.authors.index', [
            'authors' => $this->authorService->paginate(12, $filters),
            'stats'   => $this->authorService->getStats(),
            'filters' => $filters,
        ]);
    }
}
