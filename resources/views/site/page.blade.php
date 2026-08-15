@extends('layouts.site')

@section('title', $page->title.' — '.$site->school_name)

@section('content')
<div class="border-b border-kemenag/10 bg-kemenag-deep text-white">
    <div class="site-container max-w-3xl py-12">
        <h1 class="font-display text-4xl font-extrabold md:text-5xl">{{ $page->title }}</h1>
        @if ($page->subtitle)<p class="mt-3 text-white/75">{{ $page->subtitle }}</p>@endif
    </div>
</div>
<div class="site-container max-w-3xl py-12">
    @if ($page->hero_image)
        <img src="{{ asset('storage/'.$page->hero_image) }}" alt="{{ $page->title }}" class="mb-8 aspect-[16/9] w-full rounded-2xl object-cover shadow-md">
    @endif
    <div class="article-content mt-0 max-w-none">{!! $page->renderedBody() !!}</div>
</div>
@endsection
