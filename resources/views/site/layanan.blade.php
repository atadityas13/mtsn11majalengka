@extends('layouts.site')

@section('title', 'Layanan — '.$site->school_name)

@section('content')
<div class="border-b border-kemenag/10 bg-kemenag-deep text-white">
    <div class="site-container py-12">
        <h1 class="font-display text-4xl font-extrabold md:text-5xl">Layanan</h1>
        <p class="mt-3 max-w-2xl text-white/75">Akses layanan digital madrasah dan tautan resmi terkait.</p>
    </div>
</div>
<section class="site-container grid gap-4 py-12 sm:grid-cols-2 lg:grid-cols-3">
    @forelse ($serviceLinks as $item)
        <a
            href="{{ $item->resolvedUrl() }}"
            @if ($item->isExternal()) target="_blank" rel="noopener" @endif
            class="group rounded-2xl border border-kemenag/10 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-kemenag/30 hover:shadow-md"
        >
            <div class="flex items-start gap-4">
                <x-layanan-app-icon :logo="$item->logo" :label="$item->label" />
                <div class="min-w-0">
                    <h2 class="font-display text-2xl font-extrabold text-kemenag-deep group-hover:text-kemenag">{{ $item->label }}</h2>
                    @if ($item->description)
                        <p class="mt-2 text-sm text-muted">{{ $item->description }}</p>
                    @endif
                </div>
            </div>
        </a>
    @empty
        <p class="col-span-full rounded-2xl border border-dashed border-kemenag/20 bg-white p-8 text-muted">
            Belum ada layanan yang tersedia saat ini.
        </p>
    @endforelse
</section>
@endsection
