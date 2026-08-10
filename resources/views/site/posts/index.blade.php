@extends('layouts.site')

@section('title', 'Berita — '.$site->school_name)
@section('description', 'Berita dan publikasi terbaru '.$site->school_name)

@section('content')
<div class="border-b border-kemenag/10 bg-kemenag-deep text-white">
    <div class="site-container py-12">
        <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-gold">Publikasi</p>
        <h1 class="mt-2 font-display text-4xl font-extrabold md:text-5xl">Berita Madrasah</h1>
    </div>
</div>

<section class="site-container py-12 md:py-14" x-data="{ loading: false }" @submit="loading = true">
    <form method="get" action="{{ route('posts.index') }}" class="mb-8 flex flex-col gap-3 md:flex-row md:items-center">
        <input
            type="search"
            name="q"
            value="{{ $search }}"
            placeholder="Cari berita..."
            class="w-full rounded-md border border-kemenag/20 bg-white px-4 py-2.5 text-sm outline-none focus:border-kemenag focus:ring-2 focus:ring-kemenag/20 md:max-w-md"
        >
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('posts.index', array_filter(['q' => $search ?: null])) }}"
               class="rounded-full px-3 py-1.5 text-xs font-bold {{ $activeCategory === '' ? 'bg-kemenag text-white' : 'bg-white text-kemenag-deep border border-kemenag/15' }}">
                Semua
            </a>
            @foreach ($categories as $category)
                <a href="{{ route('posts.index', array_filter(['q' => $search ?: null, 'kategori' => $category->slug])) }}"
                   class="rounded-full px-3 py-1.5 text-xs font-bold {{ $activeCategory === $category->slug ? 'bg-kemenag text-white' : 'bg-white text-kemenag-deep border border-kemenag/15' }}">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
        <button type="submit" class="btn-primary md:ml-auto">Cari</button>
    </form>

    <div x-show="loading" class="mb-6 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @for ($i = 0; $i < 3; $i++)
            <div class="overflow-hidden rounded-2xl border border-kemenag/10 bg-white p-0">
                <div class="skeleton aspect-[16/10] rounded-none"></div>
                <div class="space-y-3 p-5">
                    <div class="skeleton h-3 w-24"></div>
                    <div class="skeleton h-5 w-4/5"></div>
                    <div class="skeleton h-4 w-full"></div>
                </div>
            </div>
        @endfor
    </div>

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3" x-show="!loading">
        @forelse ($posts as $post)
            <a href="{{ route('posts.show', $post->slug) }}" class="group overflow-hidden rounded-2xl border border-kemenag/10 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="aspect-[16/10] overflow-hidden bg-kemenag-soft">
                    @if ($post->cover_image)
                        <img src="{{ asset('storage/'.$post->cover_image) }}" alt="{{ $post->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                    @else
                        <div class="flex h-full items-center justify-center pattern-mesh text-sm font-bold text-white">MTsN 11</div>
                    @endif
                </div>
                <div class="p-5">
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="news-meta">{{ optional($post->published_at)->translatedFormat('d F Y') }}</p>
                        @if ($post->category)
                            <span class="rounded bg-kemenag-soft px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-kemenag">{{ $post->category->name }}</span>
                        @endif
                    </div>
                    <h2 class="mt-2 font-display text-xl font-extrabold text-kemenag-deep group-hover:text-kemenag">{{ $post->title }}</h2>
                    <p class="mt-2 line-clamp-3 text-sm text-muted">{{ $post->excerpt }}</p>
                </div>
            </a>
        @empty
            <p class="col-span-full rounded-2xl border border-dashed border-kemenag/20 bg-white p-8 text-muted">Tidak ada berita yang cocok.</p>
        @endforelse
    </div>
    <div class="mt-10">{{ $posts->links() }}</div>
</section>
@endsection
