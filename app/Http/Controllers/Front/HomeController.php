<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\HomeService;
use Illuminate\View\View;

final class HomeController extends Controller
{
    public function __construct(
        private readonly HomeService $homeService,
    ) {}

    public function index(): View
    {
        $latestWorks = $this->homeService->getLatestWorks(6);
        $popularWorks = $this->homeService->getPopularWorks(4);
        $latestPosts = $this->homeService->getLatestPosts(6);

        return view('front.home', compact('latestWorks', 'popularWorks', 'latestPosts'));
    }
}
