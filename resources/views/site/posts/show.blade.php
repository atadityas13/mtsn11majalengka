@extends('layouts.site')

@section('title', $post->title.' — '.$site->school_name)
@section('description', $post->excerpt)
@section('og_type', 'article')
@section('og_title', $post->title)
@section('og_description', $post->excerpt)
@section('og_image', $post->cover_image ? asset('storage/'.$post->cover_image) : '')

@section('content')
<article class="print-article">
    <div class="border-b border-kemenag/10 bg-kemenag-deep text-white">
        <div class="site-container max-w-3xl py-12">
            <p class="news-meta !text-gold">
                {{ optional($post->published_at)->translatedFormat('d F Y') }}
                @if ($post->author_name) · {{ $post->author_name }} @endif
                @if ($post->category) · {{ $post->category->name }} @endif
            </p>
            <h1 class="mt-4 font-display text-4xl font-extrabold leading-tight md:text-5xl">{{ $post->title }}</h1>
            <button type="button" onclick="window.print()" class="no-print mt-6 inline-flex items-center gap-2 rounded-md border border-white/30 px-3 py-2 text-xs font-bold uppercase tracking-wide text-white/90 hover:bg-white/10">
                Cetak artikel
            </button>
        </div>
    </div>

    <div class="site-container max-w-3xl py-10">
        @if ($post->cover_image)
            <img src="{{ asset('storage/'.$post->cover_image) }}" alt="{{ $post->title }}" class="mb-8 aspect-[16/9] w-full rounded-2xl object-cover shadow-md">
        @endif
        <div class="prose prose-lg max-w-none prose-headings:font-display prose-headings:text-kemenag-deep prose-a:text-kemenag">
            {!! $post->body !!}
        </div>
    </div>
</article>

@if ($related->isNotEmpty())
<section class="no-print border-t border-kemenag/10 bg-white">
    <div class="site-container py-12">
        <h2 class="font-display text-2xl font-extrabold text-kemenag-deep">Berita terkait</h2>
        <div class="mt-6 grid gap-4 md:grid-cols-3">
            @foreach ($related as $item)
                <a href="{{ route('posts.show', $item->slug) }}" class="rounded-xl border border-kemenag/10 p-4 hover:bg-kemenag-soft/50">
                    <h3 class="font-display text-lg font-bold text-kemenag-deep">{{ $item->title }}</h3>
                    <p class="mt-2 news-meta">{{ optional($item->published_at)->translatedFormat('d M Y') }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
