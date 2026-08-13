@extends('layouts.site')

@section('title', $site->school_name.' — '.$site->tagline)

@section('content')
{{-- Hero: ken burns kuat + parallax scroll + cahaya --}}
<section class="hero-stage relative overflow-hidden text-white">
    <div class="hero-media" data-hero-parallax>
        <div class="absolute inset-0 pattern-mesh opacity-20"></div>
        @if ($site->hero_image)
            <img
                src="{{ asset('storage/'.$site->hero_image) }}"
                alt=""
                class="hero-kenburns h-full w-full object-cover"
                style="object-position: {{ $site->heroImageFocus() }}; transform-origin: {{ $site->heroImageFocus() }};"
                fetchpriority="high"
            >
        @else
            <div class="h-full w-full bg-[linear-gradient(135deg,#0a7a3e,#043f1f)]"></div>
        @endif
    </div>

    <div class="pointer-events-none absolute inset-0 bg-gradient-to-r from-kemenag-dark/78 via-kemenag-deep/35 to-transparent"></div>
    <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-kemenag-dark/65 via-transparent to-black/15"></div>
    <div class="pointer-events-none absolute -left-16 top-1/4 h-72 w-72 rounded-full bg-gold/15 blur-3xl"></div>

    <div class="site-container relative flex min-h-[clamp(22rem,52vh,34rem)] flex-col justify-center py-14 md:py-16">
        <p class="animate-rise section-label !text-gold">
            <span class="text-gold">Portal Resmi Madrasah</span>
        </p>
        <h1 class="animate-rise-delay mt-3 max-w-4xl font-display text-4xl font-extrabold leading-[1.05] text-balance md:text-5xl lg:text-[3.35rem]">
            {{ $site->hero_title ?: $site->school_name }}
        </h1>
        <p class="animate-rise-delay-2 mt-4 max-w-2xl text-sm leading-relaxed text-white/92 md:text-base">
            {{ $site->hero_subtitle ?: $site->tagline }}
        </p>
        <div class="animate-rise-delay-3 mt-8 flex flex-wrap gap-3">
            @if ($site->hero_cta_url)
                <a href="{{ $site->hero_cta_url }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center rounded-md bg-gold px-5 py-2.5 text-sm font-extrabold text-kemenag-dark transition hover:-translate-y-0.5 hover:brightness-110 hover:shadow-lg">
                    {{ $site->hero_cta_label ?: 'Info PPDB' }}
                </a>
            @endif
            <a href="{{ route('posts.index') }}" class="btn-ghost">Berita Terbaru</a>
        </div>
    </div>

    <div class="hero-scroll-hint" aria-hidden="true">
        <span></span>
    </div>
</section>

{{-- Statistik singkat --}}
<section class="border-b border-kemenag/10 bg-white">
    <div class="site-container stagger grid grid-cols-2 gap-4 py-8 md:grid-cols-4 md:gap-6" x-reveal>
        @foreach ([
            ['Siswa', $site->students_count, 'Peserta didik aktif'],
            ['Guru', $site->teachers_count, 'Tenaga pendidik'],
            ['Rombel', $site->classes_count, 'Kelas belajar'],
            ['Berdiri', $site->founded_year, 'Tahun berdiri'],
        ] as [$label, $value, $hint])
            @if ($value)
                <div class="reveal rounded-2xl border border-kemenag/10 bg-surface px-4 py-5 text-center">
                    <p class="font-display text-3xl font-extrabold text-kemenag-deep md:text-4xl" @if ($label !== 'Berdiri') data-count-up="{{ (int) $value }}" @endif>
                        {{ $label === 'Berdiri' ? $value : '0' }}
                    </p>
                    <p class="mt-1 text-sm font-bold text-kemenag">{{ $label }}</p>
                    <p class="mt-1 text-xs text-muted">{{ $hint }}</p>
                </div>
            @endif
        @endforeach
    </div>
</section>

{{-- Featured news: slideshow 5 + daftar 5 --}}
<section class="site-container py-14 md:py-16">
    <div class="mb-8 flex items-end justify-between gap-4" x-reveal>
        <div>
            <p class="section-label">Berita</p>
            <h2 class="mt-2 font-display text-3xl font-extrabold text-kemenag-deep md:text-4xl">Berita Terbaru</h2>
        </div>
        <a href="{{ route('posts.index') }}" class="text-sm font-bold text-kemenag hover:underline">Lihat semua →</a>
    </div>

    @if ($posts->isNotEmpty())
        <div class="grid gap-6 lg:grid-cols-[1.4fr_1fr]" data-news-slider data-interval="4000">
            <div class="relative overflow-hidden rounded-2xl bg-kemenag-dark text-white shadow-lg">
                <div class="relative aspect-[16/10] md:aspect-[16/9]">
                    @foreach ($posts as $index => $post)
                        <div
                            class="news-slide absolute inset-0 transition-opacity duration-500 ease-out {{ $index === 0 ? 'is-active opacity-100 z-[1]' : 'pointer-events-none opacity-0 z-0' }}"
                            data-slide="{{ $index }}"
                        >
                            <a href="{{ route('posts.show', $post->slug) }}" class="group relative block h-full">
                                @if ($post->cover_image)
                                    <img src="{{ asset('storage/'.$post->cover_image) }}" alt="{{ $post->title }}" class="h-full w-full object-cover opacity-70 transition duration-700 group-hover:scale-105">
                                @else
                                    <div class="h-full w-full pattern-mesh"></div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-kemenag-dark via-kemenag-dark/45 to-transparent"></div>
                                <div class="absolute inset-x-0 bottom-0 p-6 md:p-8">
                                    @if ($post->category)
                                        <span class="mb-2 inline-block rounded bg-gold/20 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-gold">{{ $post->category->name }}</span>
                                    @endif
                                    <p class="news-meta !text-gold">{{ optional($post->published_at)->translatedFormat('d F Y') }}</p>
                                    <h3 class="mt-2 font-display text-2xl font-extrabold leading-snug md:text-3xl">{{ $post->title }}</h3>
                                    <p class="mt-3 line-clamp-2 max-w-2xl text-sm text-white/75">{{ $post->excerpt }}</p>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                @if ($posts->count() > 1)
                    <div class="absolute bottom-4 right-4 z-10 flex items-center gap-2">
                        <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/15 text-white backdrop-blur hover:bg-white/25" data-slider-prev aria-label="Sebelumnya">‹</button>
                        <div class="flex gap-1.5 px-1" data-slider-dots>
                            @foreach ($posts as $index => $post)
                                <button
                                    type="button"
                                    class="news-dot h-2 rounded-full transition-all {{ $index === 0 ? 'w-5 bg-gold' : 'w-2 bg-white/40' }}"
                                    data-slider-dot="{{ $index }}"
                                    aria-label="Slide {{ $index + 1 }}"
                                ></button>
                            @endforeach
                        </div>
                        <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/15 text-white backdrop-blur hover:bg-white/25" data-slider-next aria-label="Berikutnya">›</button>
                    </div>
                @endif
            </div>

            <div class="flex flex-col divide-y divide-kemenag/10 overflow-hidden rounded-2xl border border-kemenag/10 bg-white shadow-sm" x-reveal>
                @foreach ($posts as $index => $post)
                    <button
                        type="button"
                        class="news-list-item group flex w-full gap-4 p-4 text-left transition hover:bg-kemenag-soft/60 {{ $index === 0 ? 'bg-kemenag-soft/80' : '' }}"
                        data-slider-item="{{ $index }}"
                    >
                        <div class="h-20 w-28 shrink-0 overflow-hidden rounded-lg bg-kemenag-soft">
                            @if ($post->cover_image)
                                <img src="{{ asset('storage/'.$post->cover_image) }}" alt="" class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full items-center justify-center bg-kemenag text-xs font-bold text-white">MTsN</div>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="news-meta">{{ optional($post->published_at)->translatedFormat('d M Y') }}</p>
                            <h3 class="news-list-title mt-1 line-clamp-2 font-display text-lg font-bold leading-snug text-kemenag-deep group-hover:text-kemenag {{ $index === 0 ? 'text-kemenag' : '' }}">{{ $post->title }}</h3>
                            <a href="{{ route('posts.show', $post->slug) }}" class="mt-2 inline-block text-xs font-bold text-kemenag hover:underline" data-slider-link>Baca →</a>
                        </div>
                    </button>
                @endforeach
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

@if ($shorts->isNotEmpty() || $homeVideos->isNotEmpty())
<section class="bg-white py-14 md:py-16">
    <div class="site-container">
        @if ($shorts->isNotEmpty())
            <div class="mb-5 flex items-end justify-between gap-3" x-reveal>
                <div>
                    <p class="section-label">Short</p>
                    <h2 class="mt-2 font-display text-3xl font-extrabold text-kemenag-deep">Short Video</h2>
                    <p class="mt-2 text-sm text-muted">Geser vertikal seperti YouTube Shorts / TikTok.</p>
                </div>
                <a href="{{ route('shorts.index') }}" class="text-sm font-bold text-kemenag hover:underline">Buka feed →</a>
            </div>
            <div class="shorts-rail mb-12" x-reveal>
                @foreach ($shorts as $short)
                    <a href="{{ route('shorts.index') }}" class="shorts-rail-card">
                        <div class="shorts-rail-thumb">
                            @if ($thumb = $short->thumbnailUrl())
                                <img src="{{ $thumb }}" alt="{{ $short->title }}">
                            @else
                                <div class="flex h-full items-center justify-center bg-kemenag text-white">▶</div>
                            @endif
                            <span class="shorts-rail-badge">Short</span>
                        </div>
                        <p class="mt-2 line-clamp-2 text-sm font-bold text-kemenag-deep">{{ $short->title }}</p>
                    </a>
                @endforeach
            </div>
        @endif

        @if ($homeVideos->isNotEmpty())
            <div class="mb-5 flex items-end justify-between gap-3" x-reveal>
                <div>
                    <p class="section-label">Video</p>
                    <h2 class="mt-2 font-display text-3xl font-extrabold text-kemenag-deep">Video Terbaru</h2>
                </div>
                <a href="{{ route('videos.index') }}" class="text-sm font-bold text-kemenag hover:underline">Semua video →</a>
            </div>
            <div class="grid gap-4 md:grid-cols-3">
                @foreach ($homeVideos as $video)
                    <a href="{{ route('videos.index') }}" class="group overflow-hidden rounded-2xl border border-kemenag/10 bg-surface" x-reveal>
                        <div class="aspect-video overflow-hidden bg-kemenag-soft">
                            @if ($thumb = $video->thumbnailUrl())
                                <img src="{{ $thumb }}" alt="{{ $video->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="font-display text-lg font-extrabold text-kemenag-deep group-hover:text-kemenag">{{ $video->title }}</h3>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endif

{{-- Video profil --}}
@if ($site->youtubeEmbedUrl())
<section class="site-container py-14 md:py-16">
    <div class="mb-8" x-reveal>
        <p class="section-label">Profil</p>
        <h2 class="mt-2 font-display text-3xl font-extrabold text-kemenag-deep md:text-4xl">Video Profil Madrasah</h2>
        <p class="mt-2 max-w-2xl text-sm text-muted">Video dapat diganti kapan saja dari panel admin (Pengaturan Situs).</p>
    </div>
    <div class="overflow-hidden rounded-2xl border border-kemenag/10 bg-kemenag-dark shadow-lg" x-reveal="reveal-scale">
        <div class="aspect-video">
            <iframe
                src="{{ $site->youtubeEmbedUrl() }}"
                title="Video profil {{ $site->school_name }}"
                class="h-full w-full"
                loading="lazy"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
            ></iframe>
        </div>
    </div>
</section>
@endif

{{-- Prestasi --}}
@if ($achievements->isNotEmpty())
<section class="bg-white py-14 md:py-16">
    <div class="site-container">
        <div class="mb-8 flex items-end justify-between gap-4" x-reveal>
            <div>
                <p class="section-label">Prestasi</p>
                <h2 class="mt-2 font-display text-3xl font-extrabold text-kemenag-deep">Capaian Madrasah</h2>
            </div>
            <a href="{{ route('achievements.index') }}" class="text-sm font-bold text-kemenag hover:underline">Semua prestasi →</a>
        </div>
        <div class="stagger grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($achievements as $item)
                <article class="reveal overflow-hidden rounded-2xl border border-kemenag/10 bg-surface" x-reveal>
                    @if ($item->image)
                        <div class="aspect-[16/10] overflow-hidden">
                            <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->title }}" class="img-zoom h-full w-full object-cover">
                        </div>
                    @endif
                    <div class="p-5">
                        @if ($item->level)
                            <p class="news-meta text-kemenag">{{ $item->level }}</p>
                        @endif
                        <h3 class="mt-2 font-display text-xl font-extrabold text-kemenag-deep">{{ $item->title }}</h3>
                        @if ($item->winner_name)
                            <p class="mt-2 text-sm text-muted">{{ $item->winner_name }}</p>
                        @endif
                    </div>
                </article>
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

{{-- Akreditasi di bagian paling bawah konten beranda --}}
<section class="border-t border-kemenag/10 bg-white py-12 md:py-14">
    <div class="site-container" x-reveal>
        <div class="mx-auto max-w-3xl text-center">
            <p class="section-label !mx-auto">Status Madrasah</p>
            <h2 class="mt-2 font-display text-3xl font-extrabold text-kemenag-deep md:text-4xl">
                {{ $site->accreditation_label ?: 'Akreditasi' }}
                @if ($site->accreditation_value)
                    {{ $site->accreditation_value }}
                @endif
            </h2>
            @if ($site->npsn)
                <p class="mt-2 text-sm text-muted">NPSN {{ $site->npsn }}</p>
            @endif
        </div>

        <div class="mx-auto mt-8 max-w-3xl">
            @if ($site->accreditation_image)
                <figure class="overflow-hidden rounded-2xl border border-kemenag/10 bg-surface shadow-sm">
                    <img
                        src="{{ asset('storage/'.$site->accreditation_image) }}"
                        alt="Surat {{ $site->accreditation_label ?: 'Akreditasi' }} {{ $site->school_name }}"
                        class="h-auto w-full object-contain"
                    >
                </figure>
            @else
                <div class="rounded-2xl border border-dashed border-kemenag/25 bg-surface px-6 py-12 text-center">
                    <p class="font-display text-xl font-extrabold text-kemenag-deep">
                        {{ $site->accreditation_label ?: 'Akreditasi' }}
                        {{ $site->accreditation_value ?: 'A' }}
                    </p>
                    <p class="mt-2 text-sm text-muted">Unggah gambar surat akreditasi dari panel admin (Pengaturan Situs).</p>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
