<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\HomeService;
use App\Services\HomeSliderService;
use App\Services\SettingService;
use Illuminate\View\View;

final class HomeController extends Controller
{
    public function __construct(
        private readonly HomeService $homeService,
        private readonly HomeSliderService $homeSliderService,
        private readonly SettingService $settingService,
    ) {}

    public function index(): View
    {
        $latestWrittenWorks = $this->homeService->getLatestWrittenWorks(3);
        $latestVisualWorks = $this->homeService->getLatestVisualWorks(3);
        $popularWorks = $this->homeService->getPopularWorks(4);
        $latestPosts = $this->homeService->getLatestPosts(6);
        $homeSliders = $this->homeSliderService->getActiveSliders();
        $hero = $this->settingService->getGroup('homepage');

        return view('front.home', compact('latestWrittenWorks', 'latestVisualWorks', 'popularWorks', 'latestPosts', 'homeSliders', 'hero'));
    }
}
