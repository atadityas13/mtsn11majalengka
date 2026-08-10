@extends('layouts.site')

@section('title', $site->school_name.' — '.$site->tagline)

@section('content')
{{-- Hero institusional: brand first + grafis Kemenag-like --}}
<section class="relative overflow-hidden text-white">
    <div class="absolute inset-0 pattern-mesh"></div>
    <div class="absolute -right-24 -top-24 h-80 w-80 rounded-full ornament-arc opacity-40 animate-float"></div>
    <div class="absolute -bottom-32 -left-16 h-96 w-96 rounded-full ornament-arc opacity-30 animate-float-delay"></div>
    @if ($site->hero_image)
        <img src="{{ asset('storage/'.$site->hero_image) }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-25 mix-blend-luminosity">
    @endif
    <div class="absolute inset-0 bg-gradient-to-r from-kemenag-dark/95 via-kemenag-deep/80 to-transparent"></div>

    <div class="site-container relative grid min-h-[78vh] items-center gap-10 py-16 md:grid-cols-[1.15fr_0.85fr] md:py-20">
        <div>
            <p class="animate-rise section-label !text-gold">
                <span class="text-gold">Portal Resmi Madrasah</span>
            </p>
            <h1 class="animate-rise-delay mt-5 max-w-3xl font-display text-5xl font-extrabold leading-[1.02] text-balance md:text-6xl lg:text-7xl">
                {{ $site->hero_title ?: $site->school_name }}
            </h1>
            <p class="animate-rise-delay-2 mt-5 max-w-xl text-base leading-relaxed text-white/80 md:text-lg">
                {{ $site->hero_subtitle ?: $site->tagline }}
            </p>
            <div class="animate-rise-delay-2 mt-8 flex flex-wrap gap-3">
                @if ($site->hero_cta_url)
                    <a href="{{ $site->hero_cta_url }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center rounded-md bg-gold px-5 py-3 text-sm font-extrabold text-kemenag-dark transition hover:-translate-y-0.5 hover:brightness-110 hover:shadow-lg">
                        {{ $site->hero_cta_label ?: 'Info PPDB' }}
                    </a>
                @endif
                <a href="{{ route('posts.index') }}" class="btn-ghost">Berita Terbaru</a>
            </div>
        </div>

        <div class="animate-rise-delay relative hidden md:block">
            <div class="relative overflow-hidden rounded-2xl border border-white/15 bg-white/10 p-1 shadow-2xl backdrop-blur transition duration-500 hover:-translate-y-1 hover:shadow-[0_25px_60px_rgba(0,0,0,0.35)]">
                <div class="aspect-[4/5] overflow-hidden rounded-xl bg-kemenag-dark/40">
                    @if ($site->hero_image)
                        <img src="{{ asset('storage/'.$site->hero_image) }}" alt="{{ $site->school_name }}" class="h-full w-full object-cover transition duration-700 hover:scale-105">
                    @else
                        <div class="flex h-full flex-col justify-between bg-[linear-gradient(160deg,#0a7a3e,#043f1f)] p-8">
                            <div class="h-24 w-24 rounded-full ornament-arc"></div>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.2em] text-gold">MTsN 11</p>
                                <p class="mt-2 font-display text-3xl font-extrabold leading-tight">Majalengka</p>
                                <p class="mt-3 text-sm text-white/70">Cingambul · Jawa Barat</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="absolute -bottom-5 -left-5 animate-float rounded-xl bg-white px-4 py-3 text-kemenag-deep shadow-xl">
                <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-muted">Status</p>
                <p class="font-display text-2xl font-extrabold">Akreditasi {{ $site->accreditation_value ?: 'A' }}</p>
            </div>
        </div>
    </div>
</section>

{{-- Featured news --}}
<section class="site-container py-14 md:py-16">
    <div class="mb-8 flex items-end justify-between gap-4" x-reveal>
        <div>
            <p class="section-label">Berita</p>
            <h2 class="mt-2 font-display text-3xl font-extrabold text-kemenag-deep md:text-4xl">Berita Terbaru</h2>
        </div>
        <a href="{{ route('posts.index') }}" class="text-sm font-bold text-kemenag hover:underline">Lihat semua →</a>
    </div>

    @php $featured = $posts->first(); $rest = $posts->skip(1)->take(4); @endphp

    @if ($featured)
        <div class="grid gap-6 lg:grid-cols-[1.4fr_1fr]">
            <a href="{{ route('posts.show', $featured->slug) }}" class="group relative overflow-hidden rounded-2xl bg-kemenag-dark text-white shadow-lg hover-lift" x-reveal="reveal-scale">
                <div class="aspect-[16/10] md:aspect-[16/9]">
                    @if ($featured->cover_image)
                        <img src="{{ asset('storage/'.$featured->cover_image) }}" alt="{{ $featured->title }}" class="img-zoom h-full w-full object-cover opacity-70">
                    @else
                        <div class="h-full w-full pattern-mesh"></div>
                    @endif
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-kemenag-dark via-kemenag-dark/40 to-transparent"></div>
                <div class="absolute inset-x-0 bottom-0 p-6 md:p-8">
                    <p class="news-meta !text-gold">{{ optional($featured->published_at)->translatedFormat('d F Y') }}</p>
                    <h3 class="mt-2 font-display text-2xl font-extrabold leading-snug transition group-hover:translate-x-1 md:text-3xl">{{ $featured->title }}</h3>
                    <p class="mt-3 line-clamp-2 max-w-2xl text-sm text-white/75">{{ $featured->excerpt }}</p>
                </div>
            </a>

            <div class="stagger flex flex-col divide-y divide-kemenag/10 overflow-hidden rounded-2xl border border-kemenag/10 bg-white shadow-sm" x-reveal>
                @forelse ($rest as $post)
                    <a href="{{ route('posts.show', $post->slug) }}" class="reveal group flex gap-4 p-4 transition hover:bg-kemenag-soft/60 hover:pl-5">
                        <div class="h-20 w-28 shrink-0 overflow-hidden rounded-lg bg-kemenag-soft">
                            @if ($post->cover_image)
                                <img src="{{ asset('storage/'.$post->cover_image) }}" alt="" class="img-zoom h-full w-full object-cover">
                            @else
                                <div class="flex h-full items-center justify-center bg-kemenag text-xs font-bold text-white">MTsN</div>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="news-meta">{{ optional($post->published_at)->translatedFormat('d M Y') }}</p>
                            <h3 class="mt-1 line-clamp-2 font-display text-lg font-bold leading-snug text-kemenag-deep group-hover:text-kemenag">{{ $post->title }}</h3>
                        </div>
                    </a>
                @empty
                    <p class="p-6 text-sm text-muted">Tambahkan berita lain dari panel admin.</p>
                @endforelse
            </div>
        </div>
    @else
        <p class="rounded-2xl border border-dashed border-kemenag/20 bg-white p-8 text-muted" x-reveal>Belum ada berita. Tambahkan dari panel admin.</p>
    @endif
</section>

{{-- Layanan cepat --}}
<section class="bg-white py-12">
    <div class="site-container">
        <div class="mb-8 flex items-end justify-between" x-reveal>
            <div>
                <p class="section-label">Layanan</p>
                <h2 class="mt-2 font-display text-3xl font-extrabold text-kemenag-deep">Akses Layanan</h2>
            </div>
            <a href="{{ route('layanan') }}" class="text-sm font-bold text-kemenag hover:underline">Semua layanan →</a>
        </div>
        <div class="stagger grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['PPDB Online', $site->ppdb_url, 'Pendaftaran peserta didik baru', '01'],
                ['Rapor Digital', $site->rdm_url, 'Portal RDM madrasah', '02'],
                ['Kemenag RI', $site->kemenag_url, 'Portal Kementerian Agama', '03'],
                ['Kontak', route('contact'), 'Alamat & saluran komunikasi', '04'],
            ] as [$label, $url, $desc, $num])
                @if ($url)
                    <a href="{{ $url }}" @if(\Illuminate\Support\Str::startsWith($url, 'http')) target="_blank" rel="noopener" @endif class="reveal hover-lift group relative overflow-hidden rounded-2xl border border-kemenag/10 bg-surface p-5" x-reveal>
                        <span class="font-display text-4xl font-extrabold text-kemenag/15 transition duration-300 group-hover:scale-110 group-hover:text-kemenag/30">{{ $num }}</span>
                        <p class="mt-3 font-display text-xl font-extrabold text-kemenag-deep">{{ $label }}</p>
                        <p class="mt-1 text-sm text-muted">{{ $desc }}</p>
                    </a>
                @endif
            @endforeach
        </div>
    </div>
</section>

{{-- Pengumuman + Agenda --}}
<section class="site-container grid gap-6 py-14 md:grid-cols-2 md:py-16">
    <div class="rounded-2xl border border-kemenag/10 bg-white p-6 shadow-sm md:p-8" x-reveal="reveal-left">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="section-label">Pengumuman</p>
                <h2 class="mt-2 font-display text-2xl font-extrabold text-kemenag-deep">Informasi penting</h2>
            </div>
            <a href="{{ route('announcements.index') }}" class="text-sm font-bold text-kemenag hover:underline">Semua</a>
        </div>
        <div class="mt-6 space-y-4">
            @forelse ($announcements as $item)
                <article class="border-b border-kemenag/10 pb-4 last:border-0 transition hover:translate-x-1">
                    <div class="flex flex-wrap items-center gap-2">
                        @if ($item->is_pinned)
                            <span class="rounded bg-gold/15 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-kemenag-deep">Pinned</span>
                        @endif
                        <span class="news-meta">{{ optional($item->published_on)->translatedFormat('d M Y') }}</span>
                    </div>
                    <h3 class="mt-2 font-display text-xl font-bold text-kemenag-deep">{{ $item->title }}</h3>
                    <p class="mt-1 text-sm text-muted">{{ \Illuminate\Support\Str::limit(strip_tags($item->body), 120) }}</p>
                </article>
            @empty
                <p class="text-sm text-muted">Belum ada pengumuman.</p>
            @endforelse
        </div>
    </div>

    <div class="rounded-2xl border border-kemenag/10 bg-kemenag-deep p-6 text-white shadow-sm md:p-8" x-reveal>
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-gold">Agenda</p>
                <h2 class="mt-2 font-display text-2xl font-extrabold">Kegiatan mendatang</h2>
            </div>
            <a href="{{ route('agendas.index') }}" class="text-sm font-bold text-gold hover:underline">Semua</a>
        </div>
        <div class="mt-6 space-y-4">
            @forelse ($agendas as $item)
                <article class="rounded-xl bg-white/10 p-4 backdrop-blur transition hover:-translate-y-0.5 hover:bg-white/15">
                    <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-gold">{{ $item->starts_at->translatedFormat('d F Y · H:i') }}</p>
                    <h3 class="mt-2 font-display text-xl font-bold">{{ $item->title }}</h3>
                    @if ($item->location)
                        <p class="mt-1 text-sm text-white/70">{{ $item->location }}</p>
                    @endif
                </article>
            @empty
                <p class="text-sm text-white/70">Belum ada agenda terjadwal.</p>
            @endforelse
        </div>
    </div>
</section>

@if ($gallery->isNotEmpty())
<section class="bg-white py-14 md:py-16">
    <div class="site-container">
        <div class="mb-8 flex items-end justify-between gap-4" x-reveal>
            <div>
                <p class="section-label">Galeri</p>
                <h2 class="mt-2 font-display text-3xl font-extrabold text-kemenag-deep">Dokumentasi</h2>
            </div>
            <a href="{{ route('gallery.index') }}" class="text-sm font-bold text-kemenag hover:underline">Lihat galeri →</a>
        </div>
        <div class="stagger grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($gallery as $item)
                <figure class="reveal group overflow-hidden rounded-2xl" x-reveal>
                    <div class="aspect-[4/3] overflow-hidden">
                        <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->title }}" class="img-zoom h-full w-full object-cover">
                    </div>
                    <figcaption class="mt-3 px-1 font-display text-lg font-bold text-kemenag-deep transition group-hover:text-kemenag">{{ $item->title }}</figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif

@if ($site->headmaster_name)
<section class="relative overflow-hidden py-14 md:py-16">
    <div class="absolute inset-0 pattern-mesh opacity-95"></div>
    <div class="site-container relative grid items-center gap-8 md:grid-cols-[0.75fr_1.25fr]">
        <div class="aspect-[4/5] overflow-hidden rounded-2xl border border-white/20 bg-white/10 shadow-2xl" x-reveal="reveal-left">
            @if ($site->headmaster_photo)
                <img src="{{ asset('storage/'.$site->headmaster_photo) }}" alt="{{ $site->headmaster_name }}" class="h-full w-full object-cover transition duration-700 hover:scale-105">
            @else
                <div class="flex h-full items-center justify-center font-display text-5xl font-extrabold text-white/35">KM</div>
            @endif
        </div>
        <div class="text-white" x-reveal>
            <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-gold">Sambutan Kepala Madrasah</p>
            <blockquote class="mt-4 font-display text-3xl font-bold leading-snug text-balance md:text-4xl">“{{ $site->headmaster_quote }}”</blockquote>
            <p class="mt-6 text-lg font-extrabold">{{ $site->headmaster_name }}</p>
            <p class="text-sm text-white/70">{{ $site->headmaster_title }}</p>
        </div>
    </div>
</section>
@endif
@endsection
