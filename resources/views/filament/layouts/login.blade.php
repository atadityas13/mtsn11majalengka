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
    class="fi"
    data-theme="light"
    style="color-scheme: light;"
>
    <head>
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::HEAD_START, scopes: $renderHookScopes) }}

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="color-scheme" content="light">

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
            html.dark, html.dark body { color-scheme: light !important; }

            :root {
                --sc-ink: #101f18;
                --sc-muted: #5f7268;
                --sc-green: #0a7a3e;
                --sc-deep: #065c2e;
                --sc-dark: #043f1f;
            }

            * { box-sizing: border-box; }

            body.sicoma-login-body {
                margin: 0;
                min-height: 100vh;
                color: var(--sc-ink);
                background: #fff;
                font-family: "Plus Jakarta Sans", ui-sans-serif, system-ui, sans-serif;
            }

            .sicoma-login-shell {
                min-height: 100vh;
                display: grid;
            }

            @media (min-width: 960px) {
                .sicoma-login-shell {
                    grid-template-columns: 1.05fr 1fr;
                }
            }

            .sicoma-login-aside {
                position: relative;
                display: none;
                overflow: hidden;
                color: #fff;
                background:
                    radial-gradient(ellipse 80% 60% at 20% 15%, rgba(212,160,23,.22), transparent 55%),
                    radial-gradient(ellipse 70% 50% at 90% 90%, rgba(255,255,255,.08), transparent 50%),
                    linear-gradient(160deg, #032816 0%, #065c2e 48%, #0a7a3e 100%);
                padding: 2.5rem;
            }

            @media (min-width: 960px) {
                .sicoma-login-aside { display: flex; flex-direction: column; justify-content: space-between; }
            }

            .sicoma-login-aside::before {
                content: "";
                position: absolute;
                inset: 0;
                background-image:
                    linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px);
                background-size: 48px 48px;
                mask-image: radial-gradient(ellipse at center, black 20%, transparent 75%);
                pointer-events: none;
            }

            .sicoma-login-aside > * { position: relative; z-index: 1; }

            .sicoma-aside-brand {
                display: flex;
                align-items: center;
                gap: 0.85rem;
            }

            .sicoma-aside-brand .mark {
                width: 2.75rem;
                height: 2.75rem;
                border-radius: 0.8rem;
                display: grid;
                place-items: center;
                overflow: hidden;
                background: rgba(255,255,255,.12);
                border: 1px solid rgba(255,255,255,.18);
            }

            .sicoma-aside-brand .mark img {
                width: 100%;
                height: 100%;
                object-fit: contain;
                background: #fff;
                padding: 0.25rem;
            }

            .sicoma-aside-brand .mark span {
                font-family: Outfit, sans-serif;
                font-weight: 800;
                font-size: 1rem;
            }

            .sicoma-aside-brand strong {
                display: block;
                font-family: Outfit, sans-serif;
                font-size: 1.25rem;
                font-weight: 800;
                letter-spacing: -0.02em;
            }

            .sicoma-aside-brand small {
                display: block;
                margin-top: 0.15rem;
                opacity: .72;
                font-size: 0.75rem;
                font-weight: 600;
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }

            .sicoma-aside-copy {
                max-width: 26rem;
            }

            .sicoma-aside-copy h2 {
                margin: 0 0 0.85rem;
                font-family: Outfit, sans-serif;
                font-size: clamp(1.8rem, 2.6vw, 2.45rem);
                line-height: 1.15;
                font-weight: 800;
                letter-spacing: -0.03em;
            }

            .sicoma-aside-copy p {
                margin: 0;
                color: rgba(255,255,255,.82);
                line-height: 1.65;
                font-size: 1rem;
            }

            .sicoma-aside-foot {
                font-size: 0.8rem;
                color: rgba(255,255,255,.55);
            }

            .sicoma-login-main {
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 2rem 1.25rem;
                background: #f6f8f7;
            }

            .sicoma-login-panel {
                width: min(100%, 24rem);
            }

            .sicoma-login-mobile-brand {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                margin-bottom: 1.5rem;
            }

            @media (min-width: 960px) {
                .sicoma-login-mobile-brand { display: none; }
            }

            .sicoma-login-mobile-brand .mark {
                width: 2.5rem;
                height: 2.5rem;
                border-radius: 0.7rem;
                display: grid;
                place-items: center;
                overflow: hidden;
                background: linear-gradient(145deg, #0c8b48, #043f1f);
                color: #fff;
                font-family: Outfit, sans-serif;
                font-weight: 800;
            }

            .sicoma-login-mobile-brand .mark img {
                width: 100%;
                height: 100%;
                object-fit: contain;
                background: #fff;
                padding: 0.2rem;
            }

            .sicoma-login-mobile-brand strong {
                display: block;
                font-family: Outfit, sans-serif;
                font-size: 1.15rem;
                font-weight: 800;
                color: var(--sc-ink);
            }

            .sicoma-login-mobile-brand small {
                color: var(--sc-muted);
                font-size: 0.8rem;
            }

            .sicoma-login-title {
                margin: 0 0 0.35rem;
                font-family: Outfit, sans-serif;
                font-size: 1.75rem;
                font-weight: 800;
                letter-spacing: -0.03em;
                color: var(--sc-ink);
            }

            .sicoma-login-desc {
                margin: 0 0 1.5rem;
                color: var(--sc-muted);
                font-size: 0.92rem;
                line-height: 1.5;
            }

            .sicoma-login-card {
                background: #fff;
                border: 1px solid rgba(16, 31, 24, 0.08);
                border-radius: 1.1rem;
                box-shadow: 0 18px 40px rgba(4, 63, 31, 0.08);
                padding: 1.4rem 1.3rem 1.25rem;
            }

            .sicoma-login-card,
            .sicoma-login-card * {
                color-scheme: light;
            }

            .sicoma-login-card .fi-simple-main {
                box-shadow: none !important;
                background: transparent !important;
                border: 0 !important;
                padding: 0 !important;
                max-width: none !important;
                width: 100% !important;
            }

            .sicoma-login-card .fi-simple-header {
                display: none !important;
            }

            .sicoma-login-card .fi-simple-page-content {
                gap: 1rem !important;
            }

            /* Force readable light-theme form text (overrides Filament dark prefs) */
            .sicoma-login-card .fi-fo-field-wrp-label,
            .sicoma-login-card .fi-fo-field-wrp-label *,
            .sicoma-login-card label,
            .sicoma-login-card .fi-checkbox-label,
            .sicoma-login-card .fi-checkbox-label * {
                color: #243830 !important;
                opacity: 1 !important;
            }

            .sicoma-login-card .fi-fo-field-wrp-label {
                font-size: 0.82rem !important;
                font-weight: 600 !important;
                margin-bottom: 0.35rem !important;
            }

            .sicoma-login-card .fi-input-wrp {
                background: #fff !important;
                border: 1px solid rgba(16, 31, 24, 0.14) !important;
                border-radius: 0.7rem !important;
                box-shadow: none !important;
                min-height: 2.9rem;
            }

            .sicoma-login-card .fi-input-wrp:focus-within {
                border-color: rgba(10, 122, 62, 0.65) !important;
                box-shadow: 0 0 0 3px rgba(10, 122, 62, 0.14) !important;
            }

            .sicoma-login-card .fi-input,
            .sicoma-login-card input {
                color: #10241a !important;
                background: transparent !important;
                font-size: 0.95rem !important;
                -webkit-text-fill-color: #10241a !important;
            }

            .sicoma-login-card .fi-input::placeholder,
            .sicoma-login-card input::placeholder {
                color: #9aa8a1 !important;
                -webkit-text-fill-color: #9aa8a1 !important;
                opacity: 1 !important;
            }

            .sicoma-login-card .fi-icon-btn,
            .sicoma-login-card .fi-icon-btn svg {
                color: #5f7268 !important;
            }

            .sicoma-login-card .fi-btn {
                border-radius: 0.7rem !important;
                min-height: 2.9rem !important;
                font-weight: 700 !important;
            }

            .sicoma-login-card .fi-btn-primary,
            .sicoma-login-card button[type="submit"] {
                background: #0a7a3e !important;
                color: #fff !important;
                box-shadow: 0 8px 18px rgba(10, 122, 62, 0.22) !important;
            }

            .sicoma-login-card .fi-btn-primary *,
            .sicoma-login-card button[type="submit"] * {
                color: #fff !important;
            }

            .sicoma-login-foot {
                margin-top: 1.25rem;
                text-align: center;
                font-size: 0.75rem;
                color: #809289;
            }
        </style>

        <script>
            try {
                localStorage.setItem('theme', 'light');
                document.documentElement.classList.remove('dark');
            } catch (e) {}
        </script>

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::STYLES_AFTER, scopes: $renderHookScopes) }}
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::HEAD_END, scopes: $renderHookScopes) }}
    </head>

    <body class="sicoma-login-body fi-body fi-panel-{{ filament()->getId() }}">
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::BODY_START, scopes: $renderHookScopes) }}

        <div class="sicoma-login-shell">
            <aside class="sicoma-login-aside" aria-hidden="true">
                <div class="sicoma-aside-brand">
                    <div class="mark">
                        @if ($logoUrl)
                            <img src="{{ $logoUrl }}" alt="">
                        @else
                            <span>11</span>
                        @endif
                    </div>
                    <div>
                        <strong>Si COMA</strong>
                        <small>Site Content Management</small>
                    </div>
                </div>

                <div class="sicoma-aside-copy">
                    <h2>{{ $site->school_name }}</h2>
                    <p>Dashboard redaksi untuk mengelola berita, media, dan informasi madrasah.</p>
                </div>

                <div class="sicoma-aside-foot">Kementerian Agama RI</div>
            </aside>

            <section class="sicoma-login-main">
                <div class="sicoma-login-panel">
                    <div class="sicoma-login-mobile-brand">
                        <div class="mark">
                            @if ($logoUrl)
                                <img src="{{ $logoUrl }}" alt="">
                            @else
                                <span>11</span>
                            @endif
                        </div>
                        <div>
                            <strong>Si COMA</strong>
                            <small>{{ $site->school_name }}</small>
                        </div>
                    </div>

                    <h1 class="sicoma-login-title">Masuk</h1>
                    <p class="sicoma-login-desc">Gunakan username dan kata sandi akun Anda.</p>

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

                    <p class="sicoma-login-foot">&copy; {{ date('Y') }} {{ $site->school_name }}</p>
                </div>
            </section>
        </div>

        @filamentScripts(withCore: true)

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SCRIPTS_AFTER, scopes: $renderHookScopes) }}
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::BODY_END, scopes: $renderHookScopes) }}
    </body>
</html>
