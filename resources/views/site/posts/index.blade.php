@extends('layouts.site')

@section('title', 'Berita — '.$site->school_name)

@section('content')
<div class="border-b border-kemenag/10 bg-kemenag-deep text-white">
    <div class="site-container py-12">
        <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-gold">Publikasi</p>
        <h1 class="mt-2 font-display text-4xl font-extrabold md:text-5xl">Berita Madrasah</h1>
    </div>
</div>

<section class="site-container py-12 md:py-14">
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($posts as $post)
            <a href="{{ route('posts.show', $post->slug) }}" class="group overflow-hidden rounded-2xl border border-kemenag/10 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="aspect-[16/10] overflow-hidden bg-kemenag-soft">
                    @if ($post->cover_image)
                        <img src="{{ asset('storage/'.$post->cover_image) }}" alt="{{ $post->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                    @else
                        <div class="flex h-full items-center justify-center pattern-mesh text-sm font-bold text-white">MTsN 11</div>
                    @endif
                </div>
                <div class="p-5">
                    <p class="news-meta">{{ optional($post->published_at)->translatedFormat('d F Y') }}</p>
                    <h2 class="mt-2 font-display text-xl font-extrabold text-kemenag-deep group-hover:text-kemenag">{{ $post->title }}</h2>
                    <p class="mt-2 line-clamp-3 text-sm text-muted">{{ $post->excerpt }}</p>
                </div>
            </a>
        @endforeach
    </div>
    <div class="mt-10">{{ $posts->links() }}</div>
</section>
@endsection
