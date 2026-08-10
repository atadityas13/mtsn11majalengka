<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $site->school_name)</title>
    <meta name="description" content="@yield('description', $site->tagline)">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,500&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --brand: {{ $site->primary_color ?: '#1B5E3B' }};
            --accent: {{ $site->accent_color ?: '#C4A35A' }};
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <header class="absolute inset-x-0 top-0 z-40">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-6 px-4 py-5 md:px-6">
            <a href="{{ route('home') }}" class="group flex items-center gap-3 text-white">
                @if ($site->logo)
                    <img src="{{ asset('storage/'.$site->logo) }}" alt="{{ $site->school_name }}" class="h-11 w-11 rounded-full object-cover ring-2 ring-white/30">
                @else
                    <span class="flex h-11 w-11 items-center justify-center rounded-full bg-[var(--accent)] font-display text-lg font-bold text-madrasah-dark">M</span>
                @endif
                <span>
                    <span class="block font-display text-2xl leading-none tracking-wide">{{ $site->school_name }}</span>
                    <span class="mt-1 block text-[11px] uppercase tracking-[0.18em] text-white/75">Kementerian Agama RI</span>
                </span>
            </a>
            <nav class="hidden items-center gap-6 text-sm font-medium text-white/90 lg:flex">
                @forelse ($headerMenus as $item)
                    <a href="{{ $item->url }}" @if($item->open_in_new_tab) target="_blank" rel="noopener" @endif class="transition hover:text-[var(--accent)]">{{ $item->label }}</a>
                @empty
                    <a href="{{ route('home') }}" class="transition hover:text-[var(--accent)]">Beranda</a>
                    <a href="{{ route('posts.index') }}" class="transition hover:text-[var(--accent)]">Berita</a>
                    <a href="{{ route('announcements.index') }}" class="transition hover:text-[var(--accent)]">Pengumuman</a>
                    <a href="{{ route('gallery.index') }}" class="transition hover:text-[var(--accent)]">Galeri</a>
                    <a href="{{ route('layanan') }}" class="transition hover:text-[var(--accent)]">Layanan</a>
                    <a href="{{ route('contact') }}" class="transition hover:text-[var(--accent)]">Kontak</a>
                @endforelse
            </nav>
            @if ($site->ppdb_url)
                <a href="{{ $site->ppdb_url }}" target="_blank" rel="noopener" class="hidden rounded-full bg-[var(--accent)] px-4 py-2 text-sm font-semibold text-madrasah-dark shadow transition hover:brightness-105 md:inline-flex">PPDB</a>
            @endif
        </div>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="bg-madrasah-dark text-white">
        <div class="mx-auto grid max-w-6xl gap-10 px-4 py-14 md:grid-cols-[1.4fr_1fr_1fr] md:px-6">
            <div>
                <p class="font-display text-3xl">{{ $site->school_name }}</p>
                <p class="mt-3 max-w-md text-sm leading-relaxed text-white/70">{{ $site->footer_text ?: $site->tagline }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[var(--accent)]">Navigasi</p>
                <div class="mt-4 flex flex-col gap-2 text-sm text-white/80">
                    @forelse ($footerMenus as $item)
                        <a href="{{ $item->url }}" class="hover:text-white">{{ $item->label }}</a>
                    @empty
                        <a href="{{ route('posts.index') }}" class="hover:text-white">Berita</a>
                        <a href="{{ route('pages.show', 'profil') }}" class="hover:text-white">Profil</a>
                        <a href="{{ route('contact') }}" class="hover:text-white">Kontak</a>
                    @endforelse
                </div>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[var(--accent)]">Kontak</p>
                <div class="mt-4 space-y-2 text-sm text-white/80">
                    @if ($site->address)<p>{{ $site->address }}</p>@endif
                    @if ($site->phone)<p>{{ $site->phone }}</p>@endif
                    @if ($site->email)<p>{{ $site->email }}</p>@endif
                </div>
            </div>
        </div>
        <div class="border-t border-white/10 py-4 text-center text-xs text-white/50">
            &copy; {{ date('Y') }} {{ $site->school_name }}. Naungan Kementerian Agama RI.
        </div>
    </footer>
</body>
</html>
