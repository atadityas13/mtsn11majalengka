<?php

namespace App\Providers;

use App\Models\MenuItem;
use App\Models\ServiceLink;
use App\Models\SiteSetting;
use App\Models\SiteVisit;
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
        // File upload Livewire default hanya 5 menit — menulis berita biasanya lebih lama,
        // sehingga livewire-tmp hilang dan create berita crash saat validasi ukuran.
        config([
            'livewire.temporary_file_upload.max_upload_time' => 180,
        ]);

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
            try {
                $view->with('siteVisitStats', SiteVisit::stats());
            } catch (\Throwable) {
                $view->with('siteVisitStats', [
                    'today_visitors' => 0,
                    'today_page_views' => 0,
                    'total_visitors' => 0,
                    'total_page_views' => 0,
                ]);
            }
        });
    }
}
