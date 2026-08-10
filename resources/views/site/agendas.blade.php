@extends('layouts.site')

@section('title', 'Agenda — '.$site->school_name)

@section('content')
<div class="border-b border-kemenag/10 bg-kemenag-deep text-white">
    <div class="site-container py-12">
        <h1 class="font-display text-4xl font-extrabold md:text-5xl">Agenda</h1>
    </div>
</div>
<section class="site-container max-w-3xl space-y-4 py-12">
    @foreach ($agendas as $item)
        <article class="rounded-2xl border border-kemenag/10 bg-white p-6 shadow-sm">
            <p class="news-meta">
                {{ $item->starts_at->translatedFormat('d F Y H:i') }}
                @if ($item->ends_at) — {{ $item->ends_at->translatedFormat('d F Y H:i') }} @endif
            </p>
            <h2 class="mt-2 font-display text-2xl font-extrabold text-kemenag-deep">{{ $item->title }}</h2>
            @if ($item->location)<p class="mt-2 text-sm text-muted">Lokasi: {{ $item->location }}</p>@endif
            @if ($item->description)<p class="mt-3 text-sm leading-relaxed text-ink/80">{{ $item->description }}</p>@endif
        </article>
    @endforeach
    <div>{{ $agendas->links() }}</div>
</section>
@endsection
