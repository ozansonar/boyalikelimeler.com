<?php

namespace App\Providers;

use App\Services\AuthorService;
use App\Services\CommentService;
use App\Services\ContactService;
use App\Services\LiteraryWorkService;
use App\Services\MenuService;
use App\Services\QnaAnswerService;
use App\Services\QnaQuestionService;
use App\Services\SettingService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        if (str_starts_with((string) config('app.url'), 'https')) {
            URL::forceScheme('https');
        }

        View::composer('layouts.front', function ($view): void {
            $menuService = app(MenuService::class);
            $view->with('navbarMenu', $menuService->getByLocation('header'));
            $view->with('footerDiscoverMenu', $menuService->getByLocation('footer_discover'));
            $view->with('footerCompetitionsMenu', $menuService->getByLocation('footer_competitions'));
            $view->with('footerCorporateMenu', $menuService->getByLocation('footer_corporate'));

            $general = app(SettingService::class)->getGroup('general');
            $view->with('siteLogo', ! empty($general['logo']) ? upload_url($general['logo']) : null);
            $view->with('siteFavicon', ! empty($general['favicon']) ? upload_url($general['favicon']) : null);
        });

        View::composer('layouts.admin', function ($view): void {
            $general = app(SettingService::class)->getGroup('general');
            $view->with('siteFavicon', ! empty($general['favicon']) ? upload_url($general['favicon']) : null);
        });

        View::composer('auth.login', function ($view): void {
            $authorStats = app(AuthorService::class)->getStats();
            $visualStats = app(LiteraryWorkService::class)->getPublishedStatsByType('visual');

            $view->with('activeAuthorCount', $authorStats['author_count']);
            $view->with('totalWorkCount', $authorStats['total_works']);
            $view->with('painterCount', $visualStats['author_count']);
        });

        View::composer('partials.admin.sidebar', function ($view): void {
            $view->with('sidebarUser', auth()->user()?->loadMissing('role'));
            $view->with('pendingWorksCount', app(LiteraryWorkService::class)->getPendingCount());
            $view->with('pendingCommentsCount', app(CommentService::class)->getPendingCount());
            $view->with('unreadMessagesCount', app(ContactService::class)->getUnreadCount());
            $view->with('pendingQnaCount', app(QnaQuestionService::class)->getPendingCount() + app(QnaAnswerService::class)->getPendingCount());
        });
    }
}
