@extends('layouts.site')

@section('title', $page->title.' — '.$site->school_name)

@section('content')
<div class="bg-madrasah pt-28 text-white">
    <div class="mx-auto max-w-3xl px-4 py-12 md:px-6">
        <h1 class="font-display text-5xl">{{ $page->title }}</h1>
        @if ($page->subtitle)<p class="mt-3 text-white/75">{{ $page->subtitle }}</p>@endif
    </div>
</div>
<div class="mx-auto max-w-3xl px-4 py-12 md:px-6">
    @if ($page->hero_image)
        <img src="{{ asset('storage/'.$page->hero_image) }}" alt="{{ $page->title }}" class="mb-8 aspect-[16/9] w-full object-cover">
    @endif
    <div class="prose prose-lg max-w-none prose-headings:font-display">{!! $page->body !!}</div>
</div>
@endsection
