<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Short — {{ $site->school_name }}</title>
    <meta name="description" content="Short video {{ $site->school_name }}">
    <meta name="theme-color" content="#043f1f">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --brand: {{ $site->primary_color ?: '#0a7a3e' }};
            --accent: {{ $site->accent_color ?: '#d4a017' }};
        }
    </style>
</head>
<body class="short-page bg-black text-white antialiased">
    <header class="short-topbar">
        <a href="{{ route('home') }}" class="short-topbar-btn" aria-label="Kembali">←</a>
        <div class="min-w-0 text-center">
            <p class="truncate text-sm font-extrabold">Short MTsN 11</p>
            <p class="text-[10px] uppercase tracking-[0.16em] text-white/55">Geser ke atas</p>
        </div>
        <a href="{{ route('videos.index') }}" class="short-topbar-btn text-xs font-bold">Video</a>
    </header>

    @if ($shorts->isEmpty())
        <div class="flex min-h-dvh flex-col items-center justify-center gap-4 px-6 text-center">
            <p class="font-display text-2xl font-extrabold">Belum ada short</p>
            <p class="max-w-sm text-sm text-white/60">Tambahkan video berjenis Short dari panel admin (/admin → Video & Short). Sumber: YouTube Shorts, TikTok, atau Instagram Reels.</p>
            <a href="{{ route('home') }}" class="rounded-md bg-kemenag px-4 py-2 text-sm font-bold">Kembali ke beranda</a>
        </div>
    @else
        <div class="short-feed" data-short-feed>
            @foreach ($shorts as $index => $short)
                <section
                    class="short-slide"
                    data-short-slide
                    data-platform="{{ $short->platform() }}"
                    data-embed="{{ $short->embedUrl(autoplay: true, mute: true, shortsUi: true) }}"
                    data-embed-sound="{{ $short->embedUrl(autoplay: true, mute: false, shortsUi: true) }}"
                >
                    <div class="short-media">
                        @if ($thumb = $short->thumbnailUrl())
                            <img src="{{ $thumb }}" alt="" class="short-poster" data-short-poster>
                        @else
                            <div class="short-poster short-poster-fallback pattern-mesh flex items-center justify-center text-sm font-bold text-white/70">
                                {{ $short->platformLabel() }}
                            </div>
                        @endif
                        <div class="short-player" data-short-player></div>
                        <button type="button" class="short-play" data-short-play aria-label="Putar">
                            <span>▶</span>
                        </button>
                    </div>

                    <div class="short-actions">
                        <button type="button" class="short-action-btn is-muted" data-short-mute aria-label="Nyalakan suara">
                            <span data-mute-icon>OFF</span>
                            <small>Suara</small>
                        </button>
                    </div>

                    <div class="short-overlay">
                        <div class="short-meta">
                            <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-gold">{{ $short->platformLabel() }} · {{ $site->school_name }}</p>
                            <h1 class="mt-1 font-display text-xl font-extrabold leading-snug">{{ $short->title }}</h1>
                            @if ($short->description)
                                <p class="mt-2 line-clamp-3 text-sm text-white/75">{{ $short->description }}</p>
                            @endif
                        </div>
                        <div class="short-index">{{ $index + 1 }}/{{ $shorts->count() }}</div>
                    </div>
                </section>
            @endforeach
        </div>
    @endif
</body>
</html>
