@extends('layouts.site')

@section('title', $site->school_name.' — '.$site->tagline)

@section('content')
<section class="relative min-h-[92vh] overflow-hidden bg-madrasah-dark text-white">
    <div class="absolute inset-0">
        @if ($site->hero_image)
            <img src="{{ asset('storage/'.$site->hero_image) }}" alt="" class="h-full w-full object-cover opacity-45">
        @else
            <div class="h-full w-full bg-[radial-gradient(circle_at_20%_20%,#2f7a52_0%,transparent_45%),radial-gradient(circle_at_80%_10%,#c4a35a33_0%,transparent_35%),linear-gradient(160deg,#0f3d26,#1b5e3b_45%,#0b2818)]"></div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-madrasah-dark via-madrasah-dark/55 to-madrasah-dark/25"></div>
    </div>

    <div class="relative mx-auto flex min-h-[92vh] max-w-6xl flex-col justify-end px-4 pb-16 pt-32 md:px-6 md:pb-20">
        <p class="animate-[fadeUp_0.8s_ease_both] text-xs font-semibold uppercase tracking-[0.28em] text-[var(--accent)]">Madrasah Tsanawiyah Negeri</p>
        <h1 class="animate-[fadeUp_1s_ease_both] mt-4 max-w-3xl font-display text-5xl leading-[1.05] text-balance md:text-7xl">
            {{ $site->hero_title ?: $site->school_name }}
        </h1>
        <p class="animate-[fadeUp_1.2s_ease_both] mt-5 max-w-2xl text-base leading-relaxed text-white/80 md:text-lg">
            {{ $site->hero_subtitle ?: $site->tagline }}
        </p>
        <div class="animate-[fadeUp_1.35s_ease_both] mt-8 flex flex-wrap gap-3">
            @if ($site->hero_cta_url)
                <a href="{{ $site->hero_cta_url }}" target="_blank" rel="noopener" class="rounded-full bg-[var(--accent)] px-6 py-3 text-sm font-semibold text-madrasah-dark transition hover:brightness-105">
                    {{ $site->hero_cta_label ?: 'Info PPDB' }}
                </a>
            @endif
            <a href="{{ route('posts.index') }}" class="rounded-full border border-white/35 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                Lihat Berita
            </a>
        </div>
    </div>
</section>

<section class="mx-auto max-w-6xl px-4 py-16 md:px-6 md:py-20">
    <div class="flex items-end justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-madrasah">Berita Terbaru</p>
            <h2 class="mt-2 font-display text-4xl text-madrasah-dark md:text-5xl">Kabar Madrasah</h2>
        </div>
        <a href="{{ route('posts.index') }}" class="text-sm font-semibold text-madrasah hover:underline">Semua berita</a>
    </div>

    <div class="mt-10 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($posts as $post)
            <a href="{{ route('posts.show', $post->slug) }}" class="group block">
                <div class="aspect-[16/10] overflow-hidden bg-madrasah/10">
                    @if ($post->cover_image)
                        <img src="{{ asset('storage/'.$post->cover_image) }}" alt="{{ $post->title }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
                    @else
                        <div class="flex h-full items-center justify-center bg-gradient-to-br from-madrasah to-madrasah-dark font-display text-3xl text-white/80">MTsN 11</div>
                    @endif
                </div>
                <p class="mt-4 text-xs uppercase tracking-[0.16em] text-madrasah/70">
                    {{ optional($post->published_at)->translatedFormat('d F Y') }}
                    @if ($post->author_name) · {{ $post->author_name }} @endif
                </p>
                <h3 class="mt-2 font-display text-2xl leading-snug text-madrasah-dark transition group-hover:text-madrasah">{{ $post->title }}</h3>
                <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-ink/70">{{ $post->excerpt }}</p>
            </a>
        @empty
            <p class="text-ink/60">Belum ada berita. Tambahkan dari panel admin.</p>
        @endforelse
    </div>
</section>

<section class="border-y border-madrasah/10 bg-white/60">
    <div class="mx-auto grid max-w-6xl gap-10 px-4 py-16 md:grid-cols-[1fr_1.1fr] md:px-6 md:py-20">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-madrasah">Pengumuman</p>
            <h2 class="mt-2 font-display text-4xl text-madrasah-dark">Informasi penting</h2>
            <a href="{{ route('announcements.index') }}" class="mt-4 inline-block text-sm font-semibold text-madrasah hover:underline">Lihat semua</a>
        </div>
        <div class="space-y-5">
            @forelse ($announcements as $item)
                <article class="border-b border-madrasah/10 pb-5">
                    <div class="flex items-center gap-3 text-xs uppercase tracking-[0.14em] text-madrasah/70">
                        @if ($item->is_pinned)<span class="rounded-full bg-[var(--accent)]/20 px-2 py-0.5 text-madrasah-dark">Pinned</span>@endif
                        <span>{{ optional($item->published_on)->translatedFormat('d M Y') }}</span>
                    </div>
                    <h3 class="mt-2 font-display text-2xl text-madrasah-dark">{{ $item->title }}</h3>
                    <div class="prose prose-sm mt-2 max-w-none text-ink/70">{!! \Illuminate\Support\Str::limit(strip_tags($item->body), 160) !!}</div>
                </article>
            @empty
                <p class="text-ink/60">Belum ada pengumuman.</p>
            @endforelse
        </div>
    </div>
</section>

<section class="mx-auto max-w-6xl px-4 py-16 md:px-6 md:py-20">
    <div class="flex items-end justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-madrasah">Agenda</p>
            <h2 class="mt-2 font-display text-4xl text-madrasah-dark">Kegiatan mendatang</h2>
        </div>
        <a href="{{ route('agendas.index') }}" class="text-sm font-semibold text-madrasah hover:underline">Semua agenda</a>
    </div>
    <div class="mt-8 grid gap-4 md:grid-cols-2">
        @forelse ($agendas as $item)
            <article class="border border-madrasah/10 bg-white/70 p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-madrasah/70">
                    {{ $item->starts_at->translatedFormat('d F Y · H:i') }}
                </p>
                <h3 class="mt-2 font-display text-2xl text-madrasah-dark">{{ $item->title }}</h3>
                @if ($item->location)
                    <p class="mt-2 text-sm text-ink/65">{{ $item->location }}</p>
                @endif
            </article>
        @empty
            <p class="text-ink/60">Belum ada agenda terjadwal.</p>
        @endforelse
    </div>
</section>

@if ($gallery->isNotEmpty())
<section class="border-y border-madrasah/10 bg-white/50">
    <div class="mx-auto max-w-6xl px-4 py-16 md:px-6 md:py-20">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-madrasah">Galeri</p>
                <h2 class="mt-2 font-display text-4xl text-madrasah-dark">Dokumentasi kegiatan</h2>
            </div>
            <a href="{{ route('gallery.index') }}" class="text-sm font-semibold text-madrasah hover:underline">Lihat galeri</a>
        </div>
        <div class="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($gallery as $item)
                <figure class="group overflow-hidden">
                    <div class="aspect-[4/3] overflow-hidden bg-madrasah/10">
                        <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->title }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
                    </div>
                    <figcaption class="mt-3 font-display text-xl text-madrasah-dark">{{ $item->title }}</figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="mx-auto max-w-6xl px-4 py-16 md:px-6 md:py-20">
    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-madrasah">Layanan</p>
    <h2 class="mt-2 font-display text-4xl text-madrasah-dark">Akses cepat</h2>
    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            ['label' => 'PPDB Online', 'url' => $site->ppdb_url, 'desc' => 'Pendaftaran peserta didik baru'],
            ['label' => 'Rapor Digital', 'url' => $site->rdm_url, 'desc' => 'Portal RDM madrasah'],
            ['label' => 'Kemenag RI', 'url' => $site->kemenag_url, 'desc' => 'Portal Kementerian Agama'],
            ['label' => 'Kontak', 'url' => route('contact'), 'desc' => 'Alamat dan saluran komunikasi'],
        ] as $service)
            @if ($service['url'])
                <a href="{{ $service['url'] }}" @if(\Illuminate\Support\Str::startsWith($service['url'], 'http')) target="_blank" rel="noopener" @endif class="border border-madrasah/15 bg-white/70 p-5 transition hover:border-madrasah/40 hover:bg-white">
                    <p class="font-display text-2xl text-madrasah-dark">{{ $service['label'] }}</p>
                    <p class="mt-2 text-sm text-ink/65">{{ $service['desc'] }}</p>
                </a>
            @endif
        @endforeach
    </div>
</section>

@if ($site->headmaster_name)
<section class="bg-madrasah text-white">
    <div class="mx-auto grid max-w-6xl items-center gap-10 px-4 py-16 md:grid-cols-[0.8fr_1.2fr] md:px-6 md:py-20">
        <div class="aspect-[4/5] overflow-hidden bg-white/10">
            @if ($site->headmaster_photo)
                <img src="{{ asset('storage/'.$site->headmaster_photo) }}" alt="{{ $site->headmaster_name }}" class="h-full w-full object-cover">
            @else
                <div class="flex h-full items-center justify-center font-display text-5xl text-white/40">KM</div>
            @endif
        </div>
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[var(--accent)]">Sambutan</p>
            <blockquote class="mt-4 font-display text-3xl leading-snug text-balance md:text-4xl">“{{ $site->headmaster_quote }}”</blockquote>
            <p class="mt-6 text-lg font-semibold">{{ $site->headmaster_name }}</p>
            <p class="text-sm text-white/70">{{ $site->headmaster_title }}</p>
            @if ($site->accreditation_value)
                <p class="mt-8 inline-flex items-center gap-3 border border-white/20 px-4 py-2 text-sm">
                    <span class="text-[var(--accent)]">{{ $site->accreditation_label ?: 'Akreditasi' }}</span>
                    <span class="font-display text-2xl">{{ $site->accreditation_value }}</span>
                </p>
            @endif
        </div>
    </div>
</section>
@endif

<style>
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(18px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endsection
