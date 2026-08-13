<?php

namespace App\Providers\Filament;

use App\Models\SiteSetting;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('Si COMA')
            ->brandLogo(function (): ?string {
                $logo = SiteSetting::current()->logo;

                if (blank($logo)) {
                    return null;
                }

                return Storage::disk('public')->url($logo);
            })
            ->brandLogoHeight('2.25rem')
            ->favicon(function (): ?string {
                $favicon = SiteSetting::current()->favicon;

                if (blank($favicon)) {
                    return null;
                }

                return Storage::disk('public')->url($favicon);
            })
            ->colors([
                'primary' => Color::hex('#0a7a3e'),
            ])
            ->navigationGroups([
                NavigationGroup::make('Konten')->collapsible(),
                NavigationGroup::make('Media')->collapsible(),
                NavigationGroup::make('Profil')->collapsible(),
                NavigationGroup::make('Interaksi')->collapsible(),
                NavigationGroup::make('Navigasi & Layanan')->collapsible(),
                NavigationGroup::make('Dokumen')->collapsible(),
                NavigationGroup::make('Sistem')->collapsible(),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => <<<'HTML'
                    <script>
                        if ('serviceWorker' in navigator) {
                            navigator.serviceWorker.getRegistrations().then((registrations) => {
                                registrations.forEach((registration) => registration.unregister());
                            });
                            if (window.caches) {
                                caches.keys().then((keys) => keys.forEach((key) => caches.delete(key)));
                            }
                        }
                    </script>
                    HTML
            )
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                fn (): string => <<<'HTML'
                    <div class="mb-6 text-center">
                        <p class="text-sm font-semibold tracking-wide text-gray-500 dark:text-gray-400">Site Content Management</p>
                        <p class="mt-1 text-base font-bold text-gray-950 dark:text-white">MTsN 11 Majalengka</p>
                    </div>
                    HTML
            );
    }
}
