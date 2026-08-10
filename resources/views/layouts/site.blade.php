<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $site->school_name)</title>
    <meta name="description" content="@yield('description', $site->tagline)">
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
<body class="min-h-screen flex flex-col" x-data="{ open: false }">
    {{-- Top utility bar ala portal Kemenag --}}
    <div class="bg-kemenag-dark text-[11px] text-white/85">
        <div class="site-container flex flex-wrap items-center justify-between gap-2 py-2">
            <p class="font-semibold tracking-wide">Naungan Kementerian Agama Republik Indonesia</p>
            <div class="flex flex-wrap items-center gap-4 font-medium">
                @if ($site->npsn)<span>NPSN {{ $site->npsn }}</span>@endif
                @if ($site->accreditation_value)
                    <span class="rounded bg-gold/20 px-2 py-0.5 text-gold">{{ $site->accreditation_label ?: 'Akreditasi' }} {{ $site->accreditation_value }}</span>
                @endif
                @if ($site->phone)<span class="hidden sm:inline">{{ $site->phone }}</span>@endif
            </div>
        </div>
    </div>

    {{-- Main header --}}
    <header class="sticky top-0 z-50 border-b border-kemenag/10 bg-white/95 shadow-sm backdrop-blur-md">
        <div class="site-container flex items-center justify-between gap-4 py-3">
            <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3">
                @if ($site->logo)
                    <img src="{{ asset('storage/'.$site->logo) }}" alt="{{ $site->school_name }}" class="h-12 w-12 shrink-0 object-contain">
                @else
                    <span class="relative flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-full bg-kemenag text-lg font-extrabold text-white">
                        <span class="absolute inset-0 ornament-arc opacity-70"></span>
                        <span class="relative">11</span>
                    </span>
                @endif
                <span class="min-w-0">
                    <span class="block truncate font-display text-lg font-extrabold leading-tight text-kemenag-deep md:text-xl">{{ $site->school_name }}</span>
                    <span class="mt-0.5 block text-[10px] font-semibold uppercase tracking-[0.16em] text-muted">Madrasah Tsanawiyah Negeri</span>
                </span>
            </a>

            <nav class="hidden items-center gap-1 text-sm font-semibold text-ink/80 xl:flex">
                @forelse ($headerMenus as $item)
                    <a href="{{ $item->url }}" @if($item->open_in_new_tab) target="_blank" rel="noopener" @endif class="rounded-md px-3 py-2 transition hover:bg-kemenag-soft hover:text-kemenag-deep">{{ $item->label }}</a>
                @empty
                    <a href="{{ route('home') }}" class="rounded-md px-3 py-2 hover:bg-kemenag-soft hover:text-kemenag-deep">Beranda</a>
                    <a href="{{ route('posts.index') }}" class="rounded-md px-3 py-2 hover:bg-kemenag-soft hover:text-kemenag-deep">Berita</a>
                    <a href="{{ route('announcements.index') }}" class="rounded-md px-3 py-2 hover:bg-kemenag-soft hover:text-kemenag-deep">Pengumuman</a>
                    <a href="{{ route('gallery.index') }}" class="rounded-md px-3 py-2 hover:bg-kemenag-soft hover:text-kemenag-deep">Galeri</a>
                    <a href="{{ route('layanan') }}" class="rounded-md px-3 py-2 hover:bg-kemenag-soft hover:text-kemenag-deep">Layanan</a>
                    <a href="{{ route('contact') }}" class="rounded-md px-3 py-2 hover:bg-kemenag-soft hover:text-kemenag-deep">Kontak</a>
                @endforelse
            </nav>

            <div class="flex items-center gap-2">
                @if ($site->ppdb_url)
                    <a href="{{ $site->ppdb_url }}" target="_blank" rel="noopener" class="btn-primary hidden sm:inline-flex">PPDB</a>
                @endif
                <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-md border border-kemenag/20 text-kemenag-deep xl:hidden" aria-label="Buka menu" @click="open = !open" :aria-expanded="open.toString()">
                    <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5" stroke-width="2"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
                    <svg x-cloak x-show="open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5" stroke-width="2"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            </div>
        </div>

        <div x-cloak x-show="open" x-transition class="border-t border-kemenag/10 bg-white xl:hidden">
            <nav class="site-container flex flex-col gap-1 py-3 text-sm font-semibold">
                @forelse ($headerMenus as $item)
                    <a href="{{ $item->url }}" @if($item->open_in_new_tab) target="_blank" rel="noopener" @endif class="rounded-md px-3 py-3 hover:bg-kemenag-soft" @click="open = false">{{ $item->label }}</a>
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

    <footer class="mt-auto bg-kemenag-dark text-white">
        <div class="site-container grid gap-10 py-14 md:grid-cols-[1.5fr_1fr_1fr]">
            <div>
                <p class="font-display text-2xl font-extrabold">{{ $site->school_name }}</p>
                <p class="mt-3 max-w-md text-sm leading-relaxed text-white/70">{{ $site->footer_text ?: $site->tagline }}</p>
                <p class="mt-5 inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-gold">
                    Portal Madrasah · Kemenag RI
                </p>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-gold">Navigasi</p>
                <div class="mt-4 flex flex-col gap-2 text-sm text-white/80">
                    @forelse ($footerMenus as $item)
                        <a href="{{ $item->url }}" class="hover:text-white">{{ $item->label }}</a>
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
                </div>
            </div>
        </div>
        <div class="border-t border-white/10 py-4 text-center text-xs text-white/45">
            &copy; {{ date('Y') }} {{ $site->school_name }}. Seluruh hak dilindungi · Naungan Kementerian Agama RI.
        </div>
    </footer>
</body>
</html>
