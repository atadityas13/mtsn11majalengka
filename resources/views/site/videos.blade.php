@extends('layouts.site')

@section('title', 'Video — '.$site->school_name)
@section('description', 'Video dan short '.$site->school_name)

@section('content')
<div class="border-b border-kemenag/10 bg-kemenag-deep text-white">
    <div class="site-container flex flex-wrap items-end justify-between gap-4 py-12">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-gold">Multimedia</p>
            <h1 class="mt-2 font-display text-4xl font-extrabold md:text-5xl">Video Madrasah</h1>
        </div>
        @if ($shorts->isNotEmpty())
            <a href="{{ route('shorts.index') }}" class="inline-flex items-center rounded-md bg-gold px-4 py-2.5 text-sm font-extrabold text-kemenag-dark">Tonton Short →</a>
        @endif
    </div>
</div>

@if ($shorts->isNotEmpty())
<section class="border-b border-kemenag/10 bg-white py-10">
    <div class="site-container">
        <div class="mb-5 flex items-end justify-between gap-3">
            <div>
                <p class="section-label">Short</p>
                <h2 class="mt-2 font-display text-2xl font-extrabold text-kemenag-deep">Geser seperti Shorts</h2>
            </div>
            <a href="{{ route('shorts.index') }}" class="text-sm font-bold text-kemenag hover:underline">Buka feed →</a>
        </div>
        <div class="shorts-rail">
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
    </div>
</section>
@endif

<section class="site-container py-12">
    <div class="mb-6">
        <p class="section-label">Video</p>
        <h2 class="mt-2 font-display text-2xl font-extrabold text-kemenag-deep">Dokumentasi & profil</h2>
    </div>

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($videos as $video)
            <article class="overflow-hidden rounded-2xl border border-kemenag/10 bg-white shadow-sm">
                <div class="aspect-video overflow-hidden bg-kemenag-dark">
                    @if ($embed = $video->embedUrl())
                        <iframe
                            src="{{ $embed }}"
                            title="{{ $video->title }}"
                            class="h-full w-full"
                            loading="lazy"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                        ></iframe>
                    @elseif ($thumb = $video->thumbnailUrl())
                        <img src="{{ $thumb }}" alt="{{ $video->title }}" class="h-full w-full object-cover">
                    @endif
                </div>
                <div class="p-4">
                    <h3 class="font-display text-lg font-extrabold text-kemenag-deep">{{ $video->title }}</h3>
                    @if ($video->description)
                        <p class="mt-2 line-clamp-2 text-sm text-muted">{{ $video->description }}</p>
                    @endif
                </div>
            </article>
        @empty
            <p class="col-span-full rounded-2xl border border-dashed border-kemenag/20 bg-white p-8 text-muted">Belum ada video horizontal. Tambahkan dari panel admin (jenis: Video biasa).</p>
        @endforelse
    </div>

    <div class="mt-10">{{ $videos->links() }}</div>
</section>
@endsection
