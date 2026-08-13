@php
    use Filament\Support\Enums\Width;

    $livewire ??= null;
    $renderHookScopes = $livewire?->getRenderHookScopes();
    $maxContentWidth ??= (filament()->getSimplePageMaxContentWidth() ?? Width::Large);

    if (is_string($maxContentWidth)) {
        $maxContentWidth = Width::tryFrom($maxContentWidth) ?? $maxContentWidth;
    }

    $site = \App\Models\SiteSetting::current();
    $logoUrl = filled($site->logo) ? asset('storage/'.$site->logo) : null;
@endphp

<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ __('filament-panels::layout.direction') ?? 'ltr' }}"
    class="{{ (filament()->hasDarkMode() && filament()->hasDarkModeForced()) ? 'dark' : '' }}"
>
    <head>
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::HEAD_START, scopes: $renderHookScopes) }}

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @if ($favicon = filament()->getFavicon())
            <link rel="icon" href="{{ $favicon }}">
        @endif

        <title>{{ filled($title = trim(strip_tags($livewire?->getTitle() ?? ''))) ? "{$title} - " : null }}{{ filament()->getBrandName() }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::STYLES_BEFORE, scopes: $renderHookScopes) }}

        @filamentStyles
        {{ filament()->getTheme()->getHtml() }}
        {{ filament()->getFontHtml() }}

        <style>
            :root {
                --sc-ink: #10241a;
                --sc-muted: #66786f;
                --sc-green: #0a7a3e;
                --sc-deep: #065c2e;
                --sc-dark: #043f1f;
                --sc-gold: #d4a017;
                --sc-line: rgba(16, 36, 26, 0.08);
            }

            * { box-sizing: border-box; }

            body.sicoma-login-body {
                margin: 0;
                min-height: 100vh;
                color: var(--sc-ink);
                font-family: "Plus Jakarta Sans", var(--font-family), ui-sans-serif, system-ui, sans-serif;
                background:
                    radial-gradient(1200px 600px at 12% -10%, rgba(10, 122, 62, 0.18), transparent 55%),
                    radial-gradient(900px 500px at 100% 0%, rgba(212, 160, 23, 0.14), transparent 45%),
                    radial-gradient(700px 420px at 50% 110%, rgba(4, 63, 31, 0.10), transparent 50%),
                    linear-gradient(180deg, #f7faf8 0%, #eef4f0 100%);
            }

            .sicoma-login-shell {
                position: relative;
                min-height: 100vh;
                display: grid;
                place-items: center;
                padding: 2rem 1.25rem;
                overflow: hidden;
            }

            .sicoma-login-shell::before,
            .sicoma-login-shell::after {
                content: "";
                position: absolute;
                border-radius: 999px;
                filter: blur(40px);
                pointer-events: none;
                z-index: 0;
            }

            .sicoma-login-shell::before {
                width: 18rem;
                height: 18rem;
                left: -4rem;
                bottom: 10%;
                background: rgba(10, 122, 62, 0.16);
            }

            .sicoma-login-shell::after {
                width: 14rem;
                height: 14rem;
                right: -3rem;
                top: 12%;
                background: rgba(212, 160, 23, 0.14);
            }

            .sicoma-login-frame {
                position: relative;
                z-index: 1;
                width: min(100%, 26.5rem);
            }

            .sicoma-login-top {
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
                margin-bottom: 1.35rem;
            }

            .sicoma-login-mark {
                width: 3.5rem;
                height: 3.5rem;
                border-radius: 1rem;
                display: grid;
                place-items: center;
                overflow: hidden;
                background: linear-gradient(145deg, #0c8b48, #043f1f);
                box-shadow:
                    0 10px 24px rgba(4, 63, 31, 0.22),
                    inset 0 1px 0 rgba(255,255,255,0.22);
            }

            .sicoma-login-mark img {
                width: 100%;
                height: 100%;
                object-fit: contain;
                background: #fff;
                padding: 0.35rem;
            }

            .sicoma-login-mark span {
                color: #fff;
                font-family: Outfit, sans-serif;
                font-weight: 800;
                font-size: 1.2rem;
                letter-spacing: -0.03em;
            }

            .sicoma-login-top h1 {
                margin: 0.95rem 0 0.2rem;
                font-family: Outfit, sans-serif;
                font-size: 1.65rem;
                font-weight: 800;
                letter-spacing: -0.03em;
                color: var(--sc-ink);
            }

            .sicoma-login-top p {
                margin: 0;
                color: var(--sc-muted);
                font-size: 0.9rem;
                font-weight: 500;
            }

            .sicoma-login-card {
                background: rgba(255, 255, 255, 0.86);
                backdrop-filter: blur(18px);
                -webkit-backdrop-filter: blur(18px);
                border: 1px solid rgba(255,255,255,0.75);
                border-radius: 1.35rem;
                box-shadow:
                    0 1px 0 rgba(255,255,255,0.8) inset,
                    0 24px 60px rgba(4, 63, 31, 0.12);
                padding: 1.55rem 1.45rem 1.35rem;
            }

            .sicoma-login-card .fi-simple-main {
                box-shadow: none !important;
                background: transparent !important;
                border: 0 !important;
                padding: 0 !important;
                max-width: none !important;
                width: 100% !important;
            }

            .sicoma-login-card .fi-simple-page {
                width: 100%;
            }

            .sicoma-login-card .fi-simple-page-content {
                gap: 1.15rem;
            }

            .sicoma-login-card .fi-simple-header {
                display: none !important;
            }

            .sicoma-login-card .fi-fo-field-wrp-label span,
            .sicoma-login-card label {
                font-size: 0.8rem !important;
                font-weight: 650 !important;
                color: #314740 !important;
            }

            .sicoma-login-card .fi-input-wrp {
                border-radius: 0.8rem !important;
                background: #fff !important;
                border-color: var(--sc-line) !important;
                box-shadow: none !important;
                min-height: 2.85rem;
            }

            .sicoma-login-card .fi-input-wrp:focus-within {
                border-color: rgba(10, 122, 62, 0.55) !important;
                box-shadow: 0 0 0 4px rgba(10, 122, 62, 0.12) !important;
            }

            .sicoma-login-card .fi-input {
                font-size: 0.95rem !important;
            }

            .sicoma-login-card .fi-btn {
                border-radius: 0.8rem !important;
                min-height: 2.85rem !important;
                font-weight: 700 !important;
            }

            .sicoma-login-card .fi-btn-primary,
            .sicoma-login-card button[type="submit"] {
                background: linear-gradient(180deg, #0c8f4b 0%, var(--sc-green) 100%) !important;
                color: #fff !important;
                box-shadow: 0 10px 22px rgba(10, 122, 62, 0.28) !important;
            }

            .sicoma-login-card .fi-btn-primary *,
            .sicoma-login-card button[type="submit"] * {
                color: #fff !important;
            }

            .sicoma-login-card .fi-btn-primary:hover,
            .sicoma-login-card button[type="submit"]:hover {
                filter: brightness(1.04);
            }

            .sicoma-login-foot {
                margin-top: 1.15rem;
                text-align: center;
                font-size: 0.75rem;
                color: #809289;
                letter-spacing: 0.01em;
            }
        </style>

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::STYLES_AFTER, scopes: $renderHookScopes) }}
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::HEAD_END, scopes: $renderHookScopes) }}
    </head>

    <body class="sicoma-login-body fi-body fi-panel-{{ filament()->getId() }}">
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::BODY_START, scopes: $renderHookScopes) }}

        <div class="sicoma-login-shell">
            <div class="sicoma-login-frame">
                <div class="sicoma-login-top">
                    <div class="sicoma-login-mark" aria-hidden="true">
                        @if ($logoUrl)
                            <img src="{{ $logoUrl }}" alt="">
                        @else
                            <span>11</span>
                        @endif
                    </div>
                    <h1>Si COMA</h1>
                    <p>{{ $site->school_name }}</p>
                </div>

                <div class="sicoma-login-card">
                    <main
                        @class([
                            'fi-simple-main',
                            ($maxContentWidth instanceof Width) ? "fi-width-{$maxContentWidth->value}" : $maxContentWidth,
                        ])
                    >
                        {{ $slot }}
                    </main>
                </div>

                <p class="sicoma-login-foot">Site Content Management</p>
            </div>
        </div>

        @filamentScripts(withCore: true)

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SCRIPTS_AFTER, scopes: $renderHookScopes) }}
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::BODY_END, scopes: $renderHookScopes) }}
    </body>
</html>
