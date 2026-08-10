@extends('layouts.site')

@section('title', 'Agenda — '.$site->school_name)

@section('content')
<div class="bg-madrasah pt-28 text-white">
    <div class="mx-auto max-w-6xl px-4 py-12 md:px-6">
        <h1 class="font-display text-5xl">Agenda</h1>
    </div>
</div>
<section class="mx-auto max-w-3xl space-y-6 px-4 py-14 md:px-6">
    @foreach ($agendas as $item)
        <article class="border border-madrasah/10 bg-white/70 p-6">
            <p class="text-xs uppercase tracking-[0.14em] text-madrasah/70">
                {{ $item->starts_at->translatedFormat('d F Y H:i') }}
                @if ($item->ends_at) — {{ $item->ends_at->translatedFormat('d F Y H:i') }} @endif
            </p>
            <h2 class="mt-2 font-display text-3xl text-madrasah-dark">{{ $item->title }}</h2>
            @if ($item->location)<p class="mt-2 text-sm text-ink/70">Lokasi: {{ $item->location }}</p>@endif
            @if ($item->description)<p class="mt-3 text-sm leading-relaxed text-ink/80">{{ $item->description }}</p>@endif
        </article>
    @endforeach
    <div>{{ $agendas->links() }}</div>
</section>
@endsection
