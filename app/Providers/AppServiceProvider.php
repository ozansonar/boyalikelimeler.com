<?php

namespace App\Providers;

use App\Services\CommentService;
use App\Services\ContactService;
use App\Services\LiteraryWorkService;
use App\Services\MenuService;
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

        View::composer('partials.admin.sidebar', function ($view): void {
            $view->with('sidebarUser', auth()->user()?->loadMissing('role'));
            $view->with('pendingWorksCount', app(LiteraryWorkService::class)->getPendingCount());
            $view->with('pendingCommentsCount', app(CommentService::class)->getPendingCount());
            $view->with('unreadMessagesCount', app(ContactService::class)->getUnreadCount());
        });
    }
}
