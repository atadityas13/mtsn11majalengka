@extends('layouts.site')

@section('title', 'Struktur Organisasi — '.$site->school_name)
@section('description', 'Bagan struktur organisasi '.$site->school_name)

@php
    $n = fn (?string $slug) => $nodes->get($slug);
    $komite = $n('komite-madrasah');
    $kamad = $n('kepala-madrasah');
@endphp

@section('content')
<div class="border-b border-kemenag/10 bg-kemenag-deep text-white">
    <div class="site-container py-12">
        <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-gold">Profil</p>
        <h1 class="mt-2 font-display text-4xl font-extrabold md:text-5xl">Struktur Organisasi</h1>
        <p class="mt-3 max-w-2xl text-white/75">Susunan pejabat struktural madrasah.</p>
    </div>
</div>

<section class="site-container py-12 md:py-16">
    @if ($nodes->isEmpty())
        <p class="rounded-2xl border border-dashed border-kemenag/20 bg-white p-8 text-muted">Data struktur organisasi belum tersedia.</p>
    @else
        {{-- Leadership spotlight --}}
        <div class="mb-12 grid gap-4 sm:grid-cols-2 sm:gap-6" x-reveal>
            @if ($komite)
                @include('site.partials.org-card', ['node' => $komite, 'featured' => false])
            @endif
            @if ($kamad)
                @include('site.partials.org-card', ['node' => $kamad, 'featured' => true])
            @endif
        </div>

        {{-- Classic nested-list org tree --}}
        <div class="org-shell" x-reveal>
            <div class="tf-tree tf-gap-lg">
                <ul>
                    <li>
                        @if ($kamad)
                            <div class="tf-nc tf-nc-root">
                                <span class="tf-role">{{ $kamad->title }}</span>
                                <span class="tf-name">{{ $kamad->name ?: 'Belum diisi' }}</span>
                            </div>
                        @endif
                        <ul>
                            {{-- Kaur branch --}}
                            <li>
                                @if ($node = $n('kaur-tata-usaha'))
                                    @include('site.partials.org-tf-node', ['node' => $node, 'accent' => 'gold'])
                                @endif
                                <ul>
                                    @foreach (['bendahara', 'staf-tu'] as $slug)
                                        @if ($node = $n($slug))
                                            <li>@include('site.partials.org-tf-node', ['node' => $node])</li>
                                        @endif
                                    @endforeach
                                </ul>
                            </li>

                            {{-- Waka Kurikulum --}}
                            <li>
                                @if ($node = $n('waka-kurikulum'))
                                    @include('site.partials.org-tf-node', ['node' => $node])
                                @endif
                                <ul>
                                    @foreach (['kepala-laboratorium', 'kepala-perpustakaan'] as $slug)
                                        @if ($node = $n($slug))
                                            <li>@include('site.partials.org-tf-node', ['node' => $node])</li>
                                        @endif
                                    @endforeach
                                </ul>
                            </li>

                            {{-- Waka Kesiswaan --}}
                            <li>
                                @if ($node = $n('waka-kesiswaan'))
                                    @include('site.partials.org-tf-node', ['node' => $node])
                                @endif
                                <ul>
                                    @if ($node = $n('kepala-asrama'))
                                        <li>@include('site.partials.org-tf-node', ['node' => $node])</li>
                                    @endif
                                </ul>
                            </li>

                            {{-- Waka Sarpras --}}
                            <li>
                                @if ($node = $n('waka-sarpras'))
                                    @include('site.partials.org-tf-node', ['node' => $node])
                                @endif
                            </li>

                            {{-- Waka Humas --}}
                            <li>
                                @if ($node = $n('waka-humas'))
                                    @include('site.partials.org-tf-node', ['node' => $node])
                                @endif
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>

        @if ($node = $n('guru-wali-kelas'))
            <a href="{{ route('staff.index') }}" class="mt-10 flex flex-col items-center gap-4 rounded-2xl border border-kemenag/15 bg-kemenag-deep px-6 py-8 text-center text-white transition hover:-translate-y-0.5 hover:shadow-lg sm:flex-row sm:text-left" x-reveal>
                <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-full bg-white/10 text-2xl font-extrabold text-gold">
                    @if ($node->photo)
                        <img src="{{ asset('storage/'.$node->photo) }}" alt="" class="h-full w-full object-cover">
                    @else
                        {{ $node->initials() }}
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-gold">{{ $node->title }}</p>
                    <p class="mt-1 font-display text-2xl font-extrabold">{{ $node->name ?: 'Seluruh tenaga pendidik' }}</p>
                    <p class="mt-2 text-sm text-white/70">Di bawah koordinasi seluruh Wakil Kepala Madrasah — lihat daftar lengkap guru & tendik.</p>
                </div>
                <span class="shrink-0 rounded-md bg-gold px-4 py-2 text-sm font-extrabold text-kemenag-dark">Lihat daftar →</span>
            </a>
        @endif
    @endif
</section>

@if ($cards->isNotEmpty())
<section class="border-t border-kemenag/10 bg-white py-12 md:py-16">
    <div class="site-container">
        <div class="mb-8" x-reveal>
            <p class="section-label">Direktori</p>
            <h2 class="mt-2 font-display text-3xl font-extrabold text-kemenag-deep">Pejabat struktural</h2>
        </div>
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($cards->where('lane', '!=', 'collective') as $card)
                <article id="org-{{ $card->slug }}" class="scroll-mt-28 overflow-hidden rounded-2xl border border-kemenag/10 bg-surface transition hover:-translate-y-0.5 hover:shadow-md" x-reveal>
                    <div class="aspect-[4/5] bg-kemenag-soft">
                        @if ($card->photo)
                            <img src="{{ asset('storage/'.$card->photo) }}" alt="{{ $card->name ?: $card->title }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full items-center justify-center font-display text-5xl font-extrabold text-kemenag/25">
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
                            <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-muted">{{ $card->description }}</p>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
