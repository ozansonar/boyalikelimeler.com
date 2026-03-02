<?php

namespace App\Providers;

use App\Listeners\MailEventSubscriber;
use App\Services\MenuService;
use Illuminate\Support\Facades\Event;
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

        Event::subscribe(MailEventSubscriber::class);

        if (str_starts_with((string) config('app.url'), 'https')) {
            URL::forceScheme('https');
        }

        View::composer('layouts.front', function ($view): void {
            $menuService = app(MenuService::class);
            $view->with('navbarMenu', $menuService->getByLocation('header'));
            $view->with('footerDiscoverMenu', $menuService->getByLocation('footer_discover'));
            $view->with('footerCompetitionsMenu', $menuService->getByLocation('footer_competitions'));
            $view->with('footerCorporateMenu', $menuService->getByLocation('footer_corporate'));
        });
    }
}
