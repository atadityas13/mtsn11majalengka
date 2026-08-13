<?php

namespace App\Providers;

use App\Models\MenuItem;
use App\Models\ServiceLink;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer(['layouts.site', 'site.*'], function ($view): void {
            $view->with('site', SiteSetting::current());
            $view->with('headerMenus', MenuItem::visible()->header()->orderBy('sort_order')->get());
            $view->with('footerMenus', MenuItem::visible()->where('location', 'footer')->orderBy('sort_order')->get());
            $view->with('serviceLinks', ServiceLink::visible()->ordered()->get());
        });
    }
}
