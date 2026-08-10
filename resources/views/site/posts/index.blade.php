@extends('layouts.site')

@section('title', 'Berita — '.$site->school_name)

@section('content')
<div class="bg-madrasah pt-28 text-white">
    <div class="mx-auto max-w-6xl px-4 py-12 md:px-6">
        <p class="text-xs uppercase tracking-[0.22em] text-[var(--accent)]">Publikasi</p>
        <h1 class="mt-2 font-display text-5xl">Berita Madrasah</h1>
    </div>
</div>

<section class="mx-auto max-w-6xl px-4 py-14 md:px-6">
    <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($posts as $post)
            <a href="{{ route('posts.show', $post->slug) }}" class="group block">
                <div class="aspect-[16/10] overflow-hidden bg-madrasah/10">
                    @if ($post->cover_image)
                        <img src="{{ asset('storage/'.$post->cover_image) }}" alt="{{ $post->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                    @endif
                </div>
                <p class="mt-3 text-xs uppercase tracking-[0.14em] text-madrasah/70">{{ optional($post->published_at)->translatedFormat('d F Y') }}</p>
                <h2 class="mt-2 font-display text-2xl text-madrasah-dark group-hover:text-madrasah">{{ $post->title }}</h2>
                <p class="mt-2 line-clamp-3 text-sm text-ink/70">{{ $post->excerpt }}</p>
            </a>
        @endforeach
    </div>
    <div class="mt-10">{{ $posts->links() }}</div>
</section>
@endsection
