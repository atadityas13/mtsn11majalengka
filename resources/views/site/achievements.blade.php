@extends('layouts.site')

@section('title', 'Prestasi — '.$site->school_name)
@section('description', 'Prestasi siswa dan madrasah '.$site->school_name)

@section('content')
<div class="border-b border-kemenag/10 bg-kemenag-deep text-white">
    <div class="site-container py-12">
        <h1 class="font-display text-4xl font-extrabold md:text-5xl">Prestasi</h1>
    </div>
</div>
<section class="site-container py-12">
    <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($achievements as $item)
            <article class="overflow-hidden rounded-2xl border border-kemenag/10 bg-white shadow-sm">
                @if ($item->image)
                    <div class="aspect-[16/10] overflow-hidden bg-kemenag-soft">
                        <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->title }}" class="h-full w-full object-cover">
                    </div>
                @endif
                <div class="p-5">
                    @if ($item->level)<p class="news-meta text-kemenag">{{ $item->level }}</p>@endif
                    <h2 class="mt-2 font-display text-xl font-extrabold text-kemenag-deep">{{ $item->title }}</h2>
                    @if ($item->winner_name)<p class="mt-2 text-sm text-muted">{{ $item->winner_name }}</p>@endif
                    @if ($item->achieved_on)<p class="mt-1 text-xs text-muted">{{ $item->achieved_on->translatedFormat('d F Y') }}</p>@endif
                    @if ($item->description)<p class="mt-3 text-sm leading-relaxed text-ink/80">{{ $item->description }}</p>@endif
                </div>
            </article>
        @empty
            <p class="col-span-full rounded-2xl border border-dashed border-kemenag/20 bg-white p-8 text-muted">Belum ada prestasi.</p>
        @endforelse
    </div>
    <div class="mt-10">{{ $achievements->links() }}</div>
</section>
@endsection
