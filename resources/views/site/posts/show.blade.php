@extends('layouts.site')

@section('title', $post->title.' — '.$site->school_name)
@section('description', $post->excerpt)

@section('content')
<article class="bg-madrasah pt-28 text-white">
    <div class="mx-auto max-w-3xl px-4 py-12 md:px-6">
        <p class="text-xs uppercase tracking-[0.18em] text-[var(--accent)]">
            {{ optional($post->published_at)->translatedFormat('d F Y') }}
            @if ($post->author_name) · {{ $post->author_name }} @endif
        </p>
        <h1 class="mt-4 font-display text-4xl leading-tight md:text-5xl">{{ $post->title }}</h1>
    </div>
</article>

<div class="mx-auto max-w-3xl px-4 py-10 md:px-6">
    @if ($post->cover_image)
        <img src="{{ asset('storage/'.$post->cover_image) }}" alt="{{ $post->title }}" class="mb-8 aspect-[16/9] w-full object-cover">
    @endif
    <div class="prose prose-lg max-w-none prose-headings:font-display prose-a:text-madrasah">
        {!! $post->body !!}
    </div>
</div>

@if ($related->isNotEmpty())
<section class="border-t border-madrasah/10 bg-white/50">
    <div class="mx-auto max-w-6xl px-4 py-14 md:px-6">
        <h2 class="font-display text-3xl text-madrasah-dark">Berita terkait</h2>
        <div class="mt-8 grid gap-6 md:grid-cols-3">
            @foreach ($related as $item)
                <a href="{{ route('posts.show', $item->slug) }}" class="block">
                    <h3 class="font-display text-xl text-madrasah-dark hover:text-madrasah">{{ $item->title }}</h3>
                    <p class="mt-2 text-xs uppercase tracking-[0.14em] text-madrasah/70">{{ optional($item->published_at)->translatedFormat('d M Y') }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
