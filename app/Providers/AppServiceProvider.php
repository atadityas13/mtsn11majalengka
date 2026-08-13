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
            $view->with(
                'headerMenus',
                MenuItem::query()
                    ->visible()
                    ->header()
                    ->roots()
                    ->with(['children' => fn ($query) => $query->visible()->orderBy('sort_order')->orderBy('label')])
                    ->orderBy('sort_order')
                    ->orderBy('label')
                    ->get()
            );
            $view->with(
                'footerMenus',
                MenuItem::query()
                    ->visible()
                    ->where('location', 'footer')
                    ->roots()
                    ->orderBy('sort_order')
                    ->orderBy('label')
                    ->get()
            );
            $view->with('serviceLinks', ServiceLink::visible()->ordered()->get());
        });
    }
}
