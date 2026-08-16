<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $site->school_name)</title>
    <meta name="description" content="@yield('description', $site->tagline)">
    <meta name="theme-color" content="{{ $site->primary_color ?: '#0a7a3e' }}">
    <link rel="manifest" href="{{ route('manifest') }}">
    @php
        $ogTitle = trim($__env->yieldContent('og_title', $__env->yieldContent('title', $site->school_name)));
        $ogDescription = trim($__env->yieldContent('og_description', $__env->yieldContent('description', $site->tagline)));
        $ogImage = trim($__env->yieldContent('og_image', $site->hero_image ? asset('storage/'.$site->hero_image) : ($site->logo ? asset('storage/'.$site->logo) : '')));
    @endphp
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="{{ $site->school_name }}">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDescription }}">
    @if ($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
    @endif
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $ogTitle }}">
    <meta name="twitter:description" content="{{ $ogDescription }}">
    @if ($ogImage)
        <meta name="twitter:image" content="{{ $ogImage }}">
    @endif
    @if ($site->favicon)
        <link rel="icon" href="{{ asset('storage/'.$site->favicon) }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --brand: {{ $site->primary_color ?: '#0a7a3e' }};
            --accent: {{ $site->accent_color ?: '#d4a017' }};
        }
    </style>
</head>
<body class="min-h-screen flex flex-col" x-data="{ open: false, searchOpen: false }">
    {{-- Main header: baris 1 identitas, baris 2 menu (supaya nama & nav muat) --}}
    <header class="site-header no-print sticky top-0 z-50 border-b border-kemenag/10 bg-white/95 backdrop-blur-md" data-site-header>
        <div class="site-container flex items-center justify-between gap-3 py-3">
            <a href="{{ route('home') }}" class="flex min-w-0 flex-1 items-center gap-2.5 sm:gap-3">
                @if ($site->kemenag_logo)
                    <img src="{{ asset('storage/'.$site->kemenag_logo) }}" alt="Kementerian Agama" class="hidden h-11 w-11 shrink-0 object-contain sm:block">
                @endif
                @if ($site->logo)
                    <img src="{{ asset('storage/'.$site->logo) }}" alt="{{ $site->school_name }}" class="h-12 w-12 shrink-0 object-contain">
                @else
                    <span class="relative flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-full bg-kemenag text-lg font-extrabold text-white">
                        <span class="absolute inset-0 ornament-arc opacity-70"></span>
                        <span class="relative">11</span>
                    </span>
                @endif
                <span class="min-w-0">
                    <span class="block font-display text-base font-extrabold leading-tight text-kemenag-deep sm:text-lg md:text-xl">{{ $site->school_name }}</span>
                    <span class="mt-0.5 block text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Madrasah Tsanawiyah Negeri</span>
                </span>
            </a>

            <div class="flex shrink-0 items-center gap-2">
                <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-md border border-kemenag/20 text-kemenag-deep" aria-label="Cari berita" @click="searchOpen = !searchOpen">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5" stroke-width="2"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-3.5-3.5"/></svg>
                </button>
                @if ($site->ppdb_url)
                    <a href="{{ $site->ppdb_url }}" target="_blank" rel="noopener" class="btn-primary hidden sm:inline-flex">PPDB</a>
                @endif
                <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-md border border-kemenag/20 text-kemenag-deep lg:hidden" aria-label="Buka menu" @click="open = !open" :aria-expanded="open.toString()">
                    <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5" stroke-width="2"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
                    <svg x-cloak x-show="open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5" stroke-width="2"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            </div>
        </div>

        <nav class="hidden border-t border-kemenag/10 bg-kemenag-soft/40 lg:block">
            <div class="site-container flex flex-wrap items-center gap-x-1 gap-y-1 py-2 text-[13px] font-semibold text-ink/80">
                @forelse ($headerMenus as $item)
                    @if ($item->children->isNotEmpty())
                        <div class="nav-item">
                            <button
                                type="button"
                                class="nav-link inline-flex items-center gap-1 rounded-md px-2.5 py-1.5 transition hover:bg-white hover:text-kemenag-deep"
                                aria-haspopup="true"
                                aria-expanded="false"
                            >
                                <span>{{ $item->label }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5 opacity-70" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.21 8.29a.75.75 0 0 1 .02-1.08z" clip-rule="evenodd"/></svg>
                            </button>
                            <div class="nav-dropdown" role="menu">
                                @foreach ($item->children as $child)
                                    <a
                                        href="{{ $child->resolvedUrl() }}"
                                        @if($child->open_in_new_tab) target="_blank" rel="noopener" @endif
                                        class="nav-dropdown-link"
                                        role="menuitem"
                                    >{{ $child->label }}</a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ $item->resolvedUrl() }}" @if($item->open_in_new_tab) target="_blank" rel="noopener" @endif class="rounded-md px-2.5 py-1.5 transition hover:bg-white hover:text-kemenag-deep">{{ $item->label }}</a>
                    @endif
                @empty
                    <a href="{{ route('home') }}" class="rounded-md px-2.5 py-1.5 hover:bg-white hover:text-kemenag-deep">Beranda</a>
                    <a href="{{ route('posts.index') }}" class="rounded-md px-2.5 py-1.5 hover:bg-white hover:text-kemenag-deep">Berita</a>
                    <a href="{{ route('announcements.index') }}" class="rounded-md px-2.5 py-1.5 hover:bg-white hover:text-kemenag-deep">Pengumuman</a>
                    <a href="{{ route('gallery.index') }}" class="rounded-md px-2.5 py-1.5 hover:bg-white hover:text-kemenag-deep">Galeri</a>
                    <a href="{{ route('layanan') }}" class="rounded-md px-2.5 py-1.5 hover:bg-white hover:text-kemenag-deep">Layanan</a>
                    <a href="{{ route('contact') }}" class="rounded-md px-2.5 py-1.5 hover:bg-white hover:text-kemenag-deep">Kontak</a>
                @endforelse
            </div>
        </nav>

        <div x-cloak x-show="searchOpen" x-transition class="border-t border-kemenag/10 bg-white">
            <form action="{{ route('posts.index') }}" method="get" class="site-container flex gap-2 py-3">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari berita..." class="w-full rounded-md border border-kemenag/20 px-4 py-2.5 text-sm outline-none focus:border-kemenag focus:ring-2 focus:ring-kemenag/20" autofocus>
                <button type="submit" class="btn-primary shrink-0">Cari</button>
            </form>
        </div>

        <div x-cloak x-show="open" x-transition class="border-t border-kemenag/10 bg-white lg:hidden">
            <nav class="site-container flex flex-col gap-1 py-3 text-sm font-semibold">
                @forelse ($headerMenus as $item)
                    @if ($item->children->isNotEmpty())
                        <div class="rounded-md" x-data="{ subOpen: false }">
                            <button
                                type="button"
                                class="flex w-full items-center justify-between rounded-md px-3 py-3 text-left hover:bg-kemenag-soft"
                                @click="subOpen = !subOpen"
                                :aria-expanded="subOpen.toString()"
                            >
                                <span>{{ $item->label }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 transition" :class="subOpen ? 'rotate-180' : ''" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.21 8.29a.75.75 0 0 1 .02-1.08z" clip-rule="evenodd"/></svg>
                            </button>
                            <div x-cloak x-show="subOpen" x-transition class="ml-3 space-y-1 border-l border-kemenag/15 pb-2 pl-3">
                                @foreach ($item->children as $child)
                                    <a
                                        href="{{ $child->resolvedUrl() }}"
                                        @if($child->open_in_new_tab) target="_blank" rel="noopener" @endif
                                        class="block rounded-md px-3 py-2.5 text-ink/80 hover:bg-kemenag-soft hover:text-kemenag-deep"
                                        @click="open = false"
                                    >{{ $child->label }}</a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ $item->resolvedUrl() }}" @if($item->open_in_new_tab) target="_blank" rel="noopener" @endif class="rounded-md px-3 py-3 hover:bg-kemenag-soft" @click="open = false">{{ $item->label }}</a>
                    @endif
                @empty
                    <a href="{{ route('home') }}" class="rounded-md px-3 py-3 hover:bg-kemenag-soft" @click="open = false">Beranda</a>
                    <a href="{{ route('posts.index') }}" class="rounded-md px-3 py-3 hover:bg-kemenag-soft" @click="open = false">Berita</a>
                    <a href="{{ route('announcements.index') }}" class="rounded-md px-3 py-3 hover:bg-kemenag-soft" @click="open = false">Pengumuman</a>
                    <a href="{{ route('layanan') }}" class="rounded-md px-3 py-3 hover:bg-kemenag-soft" @click="open = false">Layanan</a>
                    <a href="{{ route('contact') }}" class="rounded-md px-3 py-3 hover:bg-kemenag-soft" @click="open = false">Kontak</a>
                @endforelse
                @if ($site->ppdb_url)
                    <a href="{{ $site->ppdb_url }}" target="_blank" rel="noopener" class="btn-primary mt-2">PPDB Online</a>
                @endif
            </nav>
        </div>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="no-print mt-auto bg-kemenag-dark text-white">
        <div class="site-container grid gap-10 py-14 md:grid-cols-2 lg:grid-cols-[1.3fr_1fr_1fr_1fr_1fr]">
            <div>
                <div class="flex items-center gap-3">
                    @if ($site->kemenag_logo)
                        <img src="{{ asset('storage/'.$site->kemenag_logo) }}" alt="Kemenag" class="h-10 w-10 object-contain">
                    @endif
                    @if ($site->logo)
                        <img src="{{ asset('storage/'.$site->logo) }}" alt="" class="h-10 w-10 object-contain">
                    @endif
                </div>
                <p class="mt-4 font-display text-2xl font-extrabold">{{ $site->school_name }}</p>
                <p class="mt-3 max-w-md text-sm leading-relaxed text-white/70">{{ $site->footer_text ?: $site->tagline }}</p>
                <p class="mt-5 inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-gold">
                    Portal Madrasah · Kemenag RI
                </p>
            </div>
            @php
                $stats = $siteVisitStats ?? [
                    'today_visitors' => 0,
                    'today_page_views' => 0,
                    'total_visitors' => 0,
                    'total_page_views' => 0,
                ];
            @endphp
            <div class="visitor-stats" aria-label="Statistik pengunjung situs">
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-gold">Pengunjung</p>
                <div class="mt-4 space-y-2 text-sm text-white/80">
                    <p><span>Hari ini:</span> <strong>{{ number_format($stats['today_visitors']) }}</strong></p>
                    <p><span>Tayangan hari ini:</span> <strong>{{ number_format($stats['today_page_views']) }}</strong></p>
                    <p><span>Total pengunjung:</span> <strong>{{ number_format($stats['total_visitors']) }}</strong></p>
                    <p><span>Total tayangan:</span> <strong>{{ number_format($stats['total_page_views']) }}</strong></p>
                </div>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-gold">Navigasi</p>
                <div class="mt-4 flex flex-col gap-2 text-sm text-white/80">
                    @forelse ($footerMenus as $item)
                        <a href="{{ $item->resolvedUrl() }}" @if($item->open_in_new_tab) target="_blank" rel="noopener" @endif class="hover:text-white">{{ $item->label }}</a>
                    @empty
                        <a href="{{ route('posts.index') }}" class="hover:text-white">Berita</a>
                        <a href="{{ route('pages.show', 'profil') }}" class="hover:text-white">Profil</a>
                        <a href="{{ route('pages.show', 'akademik') }}" class="hover:text-white">Akademik</a>
                        <a href="{{ route('contact') }}" class="hover:text-white">Kontak</a>
                    @endforelse
                </div>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-gold">Kontak</p>
                <div class="mt-4 space-y-2 text-sm text-white/80">
                    @if ($site->address)<p>{{ $site->address }}</p>@endif
                    @if ($site->phone)<p>{{ $site->phone }}</p>@endif
                    @if ($site->email)<p>{{ $site->email }}</p>@endif
                    @if ($site->whatsappLink())
                        <a href="{{ $site->whatsappLink() }}" target="_blank" rel="noopener" class="inline-flex font-semibold text-gold hover:underline">Chat WhatsApp</a>
                    @endif
                </div>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-gold">Ikuti kami</p>
                <div class="mt-4 flex flex-wrap gap-3">
                    @if ($site->facebook_url)
                        <a href="{{ $site->facebook_url }}" target="_blank" rel="noopener" class="social-follow-btn" aria-label="Facebook">
                            <img src="{{ asset('images/social/facebook.png') }}" alt="Facebook" width="40" height="40" loading="lazy">
                        </a>
                    @endif
                    @if ($site->instagram_url)
                        <a href="{{ $site->instagram_url }}" target="_blank" rel="noopener" class="social-follow-btn" aria-label="Instagram">
                            <img src="{{ asset('images/social/instagram.png') }}" alt="Instagram" width="40" height="40" loading="lazy">
                        </a>
                    @endif
                    @if ($site->youtube_url)
                        <a href="{{ $site->youtube_url }}" target="_blank" rel="noopener" class="social-follow-btn" aria-label="YouTube">
                            <img src="{{ asset('images/social/youtube.png') }}" alt="YouTube" width="40" height="40" loading="lazy">
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @include('partials.app-credit', ['variant' => 'site', 'schoolName' => $site->school_name])
    </footer>

    @include('site.partials.mascot')

    @if ($wa = $site->whatsappLink())
        <a href="{{ $wa }}" target="_blank" rel="noopener" class="wa-float no-print" aria-label="Chat WhatsApp">
            <svg viewBox="0 0 24 24" class="h-7 w-7 fill-current" aria-hidden="true"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.87 9.87 0 0 0 12.04 2zm0 1.82c4.46 0 8.09 3.63 8.09 8.09 0 4.46-3.63 8.09-8.09 8.09-1.42 0-2.8-.37-4.01-1.07l-.29-.17-3.12.82.83-3.04-.19-.31a8.05 8.05 0 0 1-1.23-4.32c0-4.46 3.63-8.09 8.09-8.09zm4.52 10.44c-.25-.12-1.47-.72-1.7-.8-.23-.09-.39-.12-.56.12-.17.25-.64.8-.79.96-.14.17-.29.19-.54.06-.25-.12-1.05-.39-2-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.43.12-.14.17-.25.25-.41.08-.17.04-.31-.02-.43-.06-.12-.56-1.34-.76-1.84-.2-.48-.4-.41-.56-.42h-.48c-.17 0-.43.06-.66.31-.23.25-.86.85-.86 2.07 0 1.22.88 2.4 1 2.56.12.17 1.75 2.67 4.24 3.74 1.49.64 1.87.7 2.54.59.41-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.14-1.18-.06-.11-.23-.17-.48-.29z"/></svg>
        </a>
    @endif

    @if (filled($site->userway_account_id))
        <script>
            (function (d) {
                var s = d.createElement('script');
                s.setAttribute('data-account', @js($site->userway_account_id));
                s.setAttribute('data-position', '5');
                s.setAttribute('src', 'https://cdn.userway.org/widget.js');
                (d.body || d.head).appendChild(s);
            })(document);
        </script>
        <noscript>
            Pastikan Javascript aktif untuk <a href="https://userway.org" rel="noopener">aksesibilitas situs</a>.
        </noscript>
    @endif

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.getRegistrations().then((registrations) => {
                    registrations.forEach((registration) => registration.unregister());
                });
                if (window.caches) {
                    caches.keys().then((keys) => keys.forEach((key) => caches.delete(key)));
                }
            });
        }
    </script>
</body>
</html>
