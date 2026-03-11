<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Enums\AdvertisementPosition;
use App\Http\Controllers\Controller;
use App\Services\AdvertisementService;
use App\Services\HomeService;
use App\Services\HomeSliderService;
use App\Services\DailyQuestionService;
use App\Services\PollService;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

final class HomeController extends Controller
{
    public function __construct(
        private readonly HomeService $homeService,
        private readonly HomeSliderService $homeSliderService,
        private readonly SettingService $settingService,
        private readonly AdvertisementService $advertisementService,
        private readonly PollService $pollService,
        private readonly DailyQuestionService $dailyQuestionService,
    ) {}

    public function index(): View
    {
        $latestWrittenWorks = $this->homeService->getLatestWrittenWorks(3);
        $latestVisualWorks = $this->homeService->getLatestVisualWorks(3);
        $popularWorks = $this->homeService->getPopularWorks(4);
        $latestPosts = $this->homeService->getLatestPosts(6);
        $homeSliders = $this->homeSliderService->getActiveSliders();
        $hero = $this->settingService->getGroup('homepage');
        $sidebarAds = $this->advertisementService->getActiveByPosition(AdvertisementPosition::Sidebar);
        $tallAds = $this->advertisementService->getActiveByPosition(AdvertisementPosition::Tall);
        $weeklyMovies = $this->settingService->getWeeklyMovies();
        $youtubeVideos = $this->homeService->getYouTubeVideos();

        $activePoll = $this->pollService->getActivePoll();
        $pollHasVoted = false;
        $pollResults = null;
        if ($activePoll) {
            $pollHasVoted = $this->pollService->hasVoted($activePoll->id, request()->ip());
            if ($pollHasVoted) {
                $pollResults = $this->pollService->getResults($activePoll->id);
            }
        }

        $dailyQuestion = $this->dailyQuestionService->getActiveQuestion();
        $dailyQuestionAnswered = false;
        if ($dailyQuestion) {
            $cookieToken = request()->cookie('dq_token');
            $dailyQuestionAnswered = $this->dailyQuestionService->hasAnswered(
                $dailyQuestion->id,
                auth()->id(),
                request()->ip(),
                $cookieToken
            );
        }

        return view('front.home', compact('latestWrittenWorks', 'latestVisualWorks', 'popularWorks', 'latestPosts', 'homeSliders', 'hero', 'sidebarAds', 'tallAds', 'weeklyMovies', 'youtubeVideos', 'activePoll', 'pollHasVoted', 'pollResults', 'dailyQuestion', 'dailyQuestionAnswered'));
    }

    public function trackAdClick(int $advertisement): JsonResponse
    {
        $ad = $this->advertisementService->find($advertisement);
        $this->advertisementService->incrementClick($ad);

        return response()->json(['success' => true]);
    }

    public function trackAdView(int $advertisement): JsonResponse
    {
        $ad = $this->advertisementService->find($advertisement);
        $this->advertisementService->incrementView($ad);

        return response()->json(['success' => true]);
    }
}
