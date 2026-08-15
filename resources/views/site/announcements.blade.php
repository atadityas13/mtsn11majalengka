@extends('layouts.site')

@section('title', 'Pengumuman — '.$site->school_name)

@section('content')
<div class="border-b border-kemenag/10 bg-kemenag-deep text-white">
    <div class="site-container py-12">
        <h1 class="font-display text-4xl font-extrabold md:text-5xl">Pengumuman</h1>
    </div>
</div>
<section class="site-container max-w-3xl space-y-6 py-12">
    @foreach ($announcements as $item)
        <article class="rounded-2xl border border-kemenag/10 bg-white p-6 shadow-sm">
            <p class="news-meta">{{ optional($item->published_on)->translatedFormat('d F Y') }}</p>
            <h2 class="mt-2 font-display text-2xl font-extrabold text-kemenag-deep">{{ $item->title }}</h2>
            <div class="article-content mt-4 max-w-none">{!! $item->renderedBody() !!}</div>
        </article>
    @endforeach
    <div>{{ $announcements->links() }}</div>
</section>
@endsection
