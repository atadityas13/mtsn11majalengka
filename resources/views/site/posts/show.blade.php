@extends('layouts.site')

@section('title', $post->title.' — '.$site->school_name)
@section('description', $post->excerpt)
@section('og_type', 'article')
@section('og_title', $post->title)
@section('og_description', $post->excerpt)
@section('og_image', $post->cover_image ? asset('storage/'.$post->cover_image) : '')

@php
    $shareUrl = url()->current();
    $shareTitle = $post->title;
    $waShare = 'https://wa.me/?text='.rawurlencode($shareTitle.' '.$shareUrl);
    $fbShare = 'https://www.facebook.com/sharer/sharer.php?u='.rawurlencode($shareUrl);
    $xShare = 'https://twitter.com/intent/tweet?url='.rawurlencode($shareUrl).'&text='.rawurlencode($shareTitle);
    $mailShare = 'mailto:?subject='.rawurlencode($shareTitle).'&body='.rawurlencode($shareTitle."\n\n".$shareUrl);
@endphp

@section('content')
<article class="print-article">
    <div class="border-b border-kemenag/10 bg-gradient-to-br from-kemenag-deep to-kemenag-dark text-white">
        <div class="site-container py-10 md:py-12">
            <nav class="no-print flex flex-wrap items-center gap-2 text-xs font-semibold text-white/70" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-gold">Beranda</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('posts.index') }}" class="hover:text-gold">Berita</a>
                <span aria-hidden="true">/</span>
                <span class="line-clamp-1 text-white/90">{{ $post->title }}</span>
            </nav>
            @if ($post->category)
                <span class="mt-5 inline-flex rounded-md bg-gold/20 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-gold">{{ $post->category->name }}</span>
            @endif
            <h1 class="mt-4 max-w-4xl font-display text-3xl font-extrabold leading-tight text-balance md:text-5xl">{{ $post->title }}</h1>
            <div class="mt-5 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-white/75">
                <span>{{ optional($post->published_at)->translatedFormat('d F Y') }}</span>
                @if ($post->author_name)
                    <span>· {{ $post->author_name }}</span>
                @endif
                <span>· {{ $post->readingMinutes() }} menit baca</span>
                <span>· {{ number_format($post->views_count) }} dilihat</span>
            </div>
        </div>
    </div>

    <div class="site-container grid gap-8 py-10 lg:grid-cols-[minmax(0,1fr)_20rem] lg:gap-10 lg:py-12">
        <div class="min-w-0">
            <div class="no-print article-share mb-6" data-share-bar>
                <p class="mb-2 text-xs font-bold uppercase tracking-[0.16em] text-muted">Bagikan</p>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ $waShare }}" target="_blank" rel="noopener" class="share-btn share-btn--wa" aria-label="Bagikan ke WhatsApp">WhatsApp</a>
                    <a href="{{ $fbShare }}" target="_blank" rel="noopener" class="share-btn share-btn--fb" aria-label="Bagikan ke Facebook">Facebook</a>
                    <a href="{{ $xShare }}" target="_blank" rel="noopener" class="share-btn share-btn--x" aria-label="Bagikan ke X">X</a>
                    <a href="{{ $mailShare }}" class="share-btn share-btn--mail" aria-label="Bagikan lewat email">Email</a>
                    <button type="button" class="share-btn share-btn--copy" data-copy-link data-url="{{ $shareUrl }}" aria-label="Salin tautan">Salin tautan</button>
                    <button type="button" onclick="window.print()" class="share-btn share-btn--print">Cetak</button>
                </div>
            </div>

            @if ($post->cover_image)
                <figure class="mb-8 overflow-hidden rounded-2xl border border-kemenag/10 bg-kemenag-soft shadow-md">
                    <img src="{{ asset('storage/'.$post->cover_image) }}" alt="{{ $post->title }}" class="aspect-[16/9] w-full object-cover">
                </figure>
            @endif

            <div class="prose prose-lg max-w-none prose-headings:font-display prose-headings:text-kemenag-deep prose-a:text-kemenag prose-img:rounded-xl">
                {!! $post->body !!}
            </div>

            <section class="no-print mt-12 border-t border-kemenag/10 pt-10" id="komentar">
                <div class="flex items-end justify-between gap-3">
                    <div>
                        <p class="section-label">Diskusi</p>
                        <h2 class="mt-2 font-display text-2xl font-extrabold text-kemenag-deep">Komentar</h2>
                    </div>
                    <p class="text-sm text-muted">{{ $approvedComments->count() }} komentar</p>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse ($approvedComments as $comment)
                        <article class="comment-card">
                            <div class="flex items-center gap-3">
                                <span class="comment-avatar" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($comment->name, 0, 1)) }}</span>
                                <div>
                                    <p class="font-bold text-kemenag-deep">{{ $comment->name }}</p>
                                    <p class="text-xs text-muted">{{ $comment->created_at->translatedFormat('d M Y H:i') }}</p>
                                </div>
                            </div>
                            <p class="mt-3 text-sm leading-relaxed text-ink/90">{{ $comment->body }}</p>
                        </article>
                    @empty
                        <p class="rounded-xl border border-dashed border-kemenag/20 bg-white px-4 py-6 text-sm text-muted">Belum ada komentar. Jadilah yang pertama memberi tanggapan.</p>
                    @endforelse
                </div>

                <div class="comment-form mt-8">
                    <h3 class="font-display text-xl font-extrabold text-kemenag-deep">Tulis komentar</h3>
                    <p class="mt-1 text-sm text-muted">Tanggapan Anda membantu memperkaya diskusi publik madrasah.</p>

                    @if (session('comment_success'))
                        <div class="mt-4 rounded-md border border-kemenag/20 bg-kemenag-soft px-4 py-3 text-sm font-semibold text-kemenag-deep">
                            {{ session('comment_success') }}
                        </div>
                    @endif

                    <form method="post" action="{{ route('posts.comments.store', $post->slug) }}" class="mt-5 space-y-4">
                        @csrf
                        <div>
                            <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-muted">Komentar</label>
                            <textarea name="body" rows="4" required class="field-input">{{ old('body') }}</textarea>
                            @error('body') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-muted">Nama</label>
                                <input type="text" name="name" value="{{ old('name') }}" required class="field-input">
                                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-muted">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" required class="field-input">
                                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <button type="submit" class="btn-primary">Kirim komentar</button>
                    </form>
                </div>
            </section>
        </div>

        <aside class="no-print article-sidebar space-y-5">
            <div class="sidebar-card">
                <h2 class="sidebar-card-title">Berita Terbaru</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($latestPosts as $item)
                        <a href="{{ route('posts.show', $item->slug) }}" class="sidebar-news-item">
                            <div class="sidebar-news-thumb">
                                @if ($item->cover_image)
                                    <img src="{{ asset('storage/'.$item->cover_image) }}" alt="" loading="lazy">
                                @else
                                    <span>MTsN</span>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="line-clamp-2 text-sm font-bold leading-snug text-kemenag-deep">{{ $item->title }}</p>
                                <p class="mt-1 text-[11px] font-semibold text-muted">{{ optional($item->published_at)->translatedFormat('d M Y') }}</p>
                            </div>
                        </a>
                    @empty
                        <p class="text-sm text-muted">Belum ada berita lain.</p>
                    @endforelse
                </div>
            </div>

            <div class="sidebar-card">
                <h2 class="sidebar-card-title">Berita Populer</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($popularPosts as $item)
                        <a href="{{ route('posts.show', $item->slug) }}" class="sidebar-news-item">
                            <div class="sidebar-news-thumb">
                                @if ($item->cover_image)
                                    <img src="{{ asset('storage/'.$item->cover_image) }}" alt="" loading="lazy">
                                @else
                                    <span>MTsN</span>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="line-clamp-2 text-sm font-bold leading-snug text-kemenag-deep">{{ $item->title }}</p>
                                <p class="mt-1 text-[11px] font-semibold text-muted">{{ number_format($item->views_count) }} dilihat</p>
                            </div>
                        </a>
                    @empty
                        <p class="text-sm text-muted">Belum ada data popularitas.</p>
                    @endforelse
                </div>
            </div>

            <div class="sidebar-card">
                <h2 class="sidebar-card-title">Komentar Terbaru</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($recentComments as $item)
                        <a href="{{ route('posts.show', $item->post->slug) }}#komentar" class="block rounded-lg p-2 transition hover:bg-kemenag-soft/70">
                            <p class="text-xs font-bold text-kemenag">{{ $item->name }}</p>
                            <p class="mt-1 line-clamp-2 text-sm text-ink/85">{{ $item->body }}</p>
                            <p class="mt-1 line-clamp-1 text-[11px] font-semibold text-muted">pada {{ $item->post->title }}</p>
                        </a>
                    @empty
                        <p class="text-sm text-muted">Belum ada komentar.</p>
                    @endforelse
                </div>
            </div>
        </aside>
    </div>
</article>

@if ($related->isNotEmpty())
<section class="no-print border-t border-kemenag/10 bg-white">
    <div class="site-container py-12">
        <p class="section-label">Lanjutan</p>
        <h2 class="mt-2 font-display text-2xl font-extrabold text-kemenag-deep">Berita terkait</h2>
        <div class="mt-6 grid gap-4 md:grid-cols-3">
            @foreach ($related as $item)
                <a href="{{ route('posts.show', $item->slug) }}" class="group overflow-hidden rounded-2xl border border-kemenag/10 bg-surface shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="aspect-[16/10] overflow-hidden bg-kemenag-soft">
                        @if ($item->cover_image)
                            <img src="{{ asset('storage/'.$item->cover_image) }}" alt="" class="img-zoom h-full w-full object-cover" loading="lazy">
                        @else
                            <div class="flex h-full items-center justify-center text-xs font-bold text-kemenag">MTsN 11</div>
                        @endif
                    </div>
                    <div class="p-4">
                        <p class="news-meta">{{ optional($item->published_at)->translatedFormat('d M Y') }}</p>
                        <h3 class="mt-1 font-display text-lg font-bold leading-snug text-kemenag-deep group-hover:text-kemenag">{{ $item->title }}</h3>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
