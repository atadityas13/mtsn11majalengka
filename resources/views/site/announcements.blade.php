@extends('layouts.site')

@section('title', 'Pengumuman — '.$site->school_name)

@section('content')
<div class="bg-madrasah pt-28 text-white">
    <div class="mx-auto max-w-6xl px-4 py-12 md:px-6">
        <h1 class="font-display text-5xl">Pengumuman</h1>
    </div>
</div>
<section class="mx-auto max-w-3xl space-y-8 px-4 py-14 md:px-6">
    @foreach ($announcements as $item)
        <article class="border-b border-madrasah/10 pb-8">
            <p class="text-xs uppercase tracking-[0.14em] text-madrasah/70">{{ optional($item->published_on)->translatedFormat('d F Y') }}</p>
            <h2 class="mt-2 font-display text-3xl text-madrasah-dark">{{ $item->title }}</h2>
            <div class="prose mt-4 max-w-none">{!! $item->body !!}</div>
        </article>
    @endforeach
    <div>{{ $announcements->links() }}</div>
</section>
@endsection
