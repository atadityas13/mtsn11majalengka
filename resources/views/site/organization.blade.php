@extends('layouts.site')

@section('title', 'Struktur Organisasi — '.$site->school_name)
@section('description', 'Bagan struktur organisasi '.$site->school_name)

@php
    $n = fn (?string $slug) => $nodes->get($slug);
@endphp

@section('content')
<div class="border-b border-kemenag/10 bg-kemenag-deep text-white">
    <div class="site-container py-12">
        <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-gold">Profil</p>
        <h1 class="mt-2 font-display text-4xl font-extrabold md:text-5xl">Struktur Organisasi</h1>
        <p class="mt-3 max-w-2xl text-white/75">Bagan pejabat struktural MTsN 11 Majalengka. Nama dan foto dapat diperbarui dari panel admin.</p>
    </div>
</div>

<section class="site-container py-12 md:py-14">
    @if ($nodes->isEmpty())
        <p class="rounded-2xl border border-dashed border-kemenag/20 bg-white p-8 text-muted">Belum ada data struktur. Tambahkan dari panel admin.</p>
    @else
        <div class="org-chart overflow-x-auto pb-4" x-reveal>
            {{-- Level 0: Komite sejajar Kamad --}}
            <div class="org-row org-row-top">
                @foreach (['komite-madrasah', 'kepala-madrasah'] as $slug)
                    @if ($node = $n($slug))
                        @include('site.partials.org-node', ['node' => $node, 'variant' => 'top'])
                    @endif
                @endforeach
            </div>

            <div class="org-spine" aria-hidden="true"></div>

            {{-- Level 1: Kaur di tengah + 4 Waka --}}
            <div class="org-row org-row-mid">
                @foreach (['kaur-tata-usaha', 'waka-kurikulum', 'waka-kesiswaan', 'waka-sarpras', 'waka-humas'] as $slug)
                    @if ($node = $n($slug))
                        @include('site.partials.org-node', ['node' => $node, 'variant' => $slug === 'kaur-tata-usaha' ? 'kaur' : 'waka'])
                    @endif
                @endforeach
            </div>

            <div class="org-spine org-spine-soft" aria-hidden="true"></div>

            {{-- Level 2: staf Kaur + unit di bawah Waka --}}
            <div class="org-row org-row-staff">
                <div class="org-group">
                    <p class="org-group-label">Di bawah Kaur TU</p>
                    <div class="org-group-nodes">
                        @foreach (['bendahara', 'staf-tu'] as $slug)
                            @if ($node = $n($slug))
                                @include('site.partials.org-node', ['node' => $node, 'variant' => 'staff'])
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="org-group">
                    <p class="org-group-label">Di bawah Waka Kurikulum</p>
                    <div class="org-group-nodes">
                        @foreach (['kepala-laboratorium', 'kepala-perpustakaan'] as $slug)
                            @if ($node = $n($slug))
                                @include('site.partials.org-node', ['node' => $node, 'variant' => 'staff'])
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="org-group">
                    <p class="org-group-label">Di bawah Waka Kesiswaan</p>
                    <div class="org-group-nodes">
                        @if ($node = $n('kepala-asrama'))
                            @include('site.partials.org-node', ['node' => $node, 'variant' => 'staff'])
                        @endif
                    </div>
                </div>
            </div>

            <div class="org-spine" aria-hidden="true"></div>

            {{-- Level 3: Guru kolektif --}}
            @if ($node = $n('guru-wali-kelas'))
                <div class="org-row org-row-collective">
                    @include('site.partials.org-node', ['node' => $node, 'variant' => 'collective'])
                </div>
            @endif
        </div>

        <div class="mt-6 text-center text-sm text-muted">
            Lihat juga daftar lengkap
            <a href="{{ route('staff.index') }}" class="font-bold text-kemenag hover:underline">Guru & Tendik</a>.
        </div>
    @endif
</section>

@if ($cards->isNotEmpty())
<section class="border-t border-kemenag/10 bg-white py-12 md:py-14">
    <div class="site-container">
        <div class="mb-8" x-reveal>
            <p class="section-label">Pejabat</p>
            <h2 class="mt-2 font-display text-3xl font-extrabold text-kemenag-deep">Detail jabatan</h2>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($cards as $card)
                <article id="org-{{ $card->slug }}" class="scroll-mt-28 overflow-hidden rounded-2xl border border-kemenag/10 bg-surface shadow-sm" x-reveal>
                    <div class="aspect-square bg-kemenag-soft">
                        @if ($card->photo)
                            <img src="{{ asset('storage/'.$card->photo) }}" alt="{{ $card->name ?: $card->title }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full items-center justify-center font-display text-4xl font-extrabold text-kemenag/30">
                                {{ $card->initials() }}
                            </div>
                        @endif
                    </div>
                    <div class="p-4">
                        <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-kemenag">{{ $card->title }}</p>
                        <h3 class="mt-1 font-display text-lg font-extrabold text-kemenag-deep">
                            {{ $card->name ?: 'Belum diisi' }}
                        </h3>
                        @if ($card->description)
                            <p class="mt-2 text-sm leading-relaxed text-muted">{{ $card->description }}</p>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
