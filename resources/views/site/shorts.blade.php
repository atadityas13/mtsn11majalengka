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
            <p class="max-w-sm text-sm text-white/60">Konten short belum tersedia. Silakan cek kembali nanti.</p>
            <a href="{{ route('home') }}" class="rounded-md bg-kemenag px-4 py-2 text-sm font-bold">Kembali ke beranda</a>
        </div>
    @else
        <div class="short-feed" data-short-feed>
            @foreach ($shorts as $index => $short)
                @php
                    $platform = $short->platform();
                @endphp
                <section
                    class="short-slide"
                    data-short-slide
                    data-platform="{{ $platform }}"
                    data-youtube-id="{{ $short->youtubeId() }}"
                    data-embed="{{ $short->embedUrl(autoplay: true, mute: true, shortsUi: true) }}"
                    data-embed-sound="{{ $short->embedUrl(autoplay: true, mute: false, shortsUi: true) }}"
                    data-external-url="{{ $short->video_url }}"
                >
                    <div class="short-media">
                        @if ($thumb = $short->thumbnailUrl())
                            <img src="{{ $thumb }}" alt="" class="short-poster" data-short-poster>
                        @else
                            <div class="short-poster short-poster-fallback" data-short-poster>
                                <span>{{ $short->platformLabel() }}</span>
                            </div>
                        @endif
                        <div class="short-player" data-short-player></div>

                        @if ($platform === 'tiktok')
                            <div class="short-scroll-layer" data-short-scroll-layer aria-hidden="true"></div>
                        @endif

                        {{-- Zona geser untuk Reels: tengah iframe bisa diklik putar, atas/bawah untuk scroll --}}
                        @if ($platform === 'instagram')
                            <div class="short-scroll-strip short-scroll-strip-top" aria-hidden="true"></div>
                            <div class="short-scroll-strip short-scroll-strip-bottom" aria-hidden="true"></div>
                        @endif

                        @if ($platform === 'youtube')
                            <button type="button" class="short-hit" data-short-hit aria-label="Pause atau putar"></button>
                            <div class="short-play" data-short-play aria-hidden="true">
                                <span>▶</span>
                            </div>
                        @endif
                    </div>

                    <div class="short-actions">
                        @if (in_array($platform, ['youtube', 'tiktok'], true))
                            <button type="button" class="short-action-btn is-muted" data-short-mute aria-label="Nyalakan suara">
                                <span class="short-speaker" aria-hidden="true">
                                    <svg class="speaker-on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5" fill="currentColor" stroke="none"></polygon>
                                        <path d="M15.5 8.5a5 5 0 0 1 0 7"></path>
                                        <path d="M18.5 5.5a9 9 0 0 1 0 13"></path>
                                    </svg>
                                    <svg class="speaker-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5" fill="currentColor" stroke="none"></polygon>
                                        <line x1="23" y1="9" x2="17" y2="15"></line>
                                        <line x1="17" y1="9" x2="23" y2="15"></line>
                                    </svg>
                                </span>
                            </button>
                        @endif

                        @if ($platform === 'instagram')
                            <a href="{{ $short->video_url }}" target="_blank" rel="noopener noreferrer" class="short-action-btn" aria-label="Buka di Instagram">
                                <span class="short-speaker" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                        <polyline points="15 3 21 3 21 9"></polyline>
                                        <line x1="10" y1="14" x2="21" y2="3"></line>
                                    </svg>
                                </span>
                            </a>
                        @endif

                        @if ($index < $shorts->count() - 1)
                            <button type="button" class="short-action-btn" data-short-next aria-label="Short berikutnya">
                                <span class="short-speaker" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </span>
                            </button>
                        @endif
                    </div>

                    <div class="short-overlay">
                        <div class="short-meta">
                            <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-gold">{{ $short->platformLabel() }} · {{ $site->school_name }}</p>
                            <h1 class="mt-1 font-display text-xl font-extrabold leading-snug">{{ $short->title }}</h1>
                            @if ($platform === 'instagram')
                                <p class="mt-2 text-xs text-white/70">Ketuk tengah untuk putar · geser di area atas/bawah untuk short berikutnya</p>
                            @elseif ($short->description)
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
