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
    $postTags = $post->tagList();
@endphp

@section('content')
<article class="print-article">
    {{-- Banner slideshow berita terbaru --}}
    @if ($bannerPosts->isNotEmpty())
    <section class="no-print news-banner" data-news-slider data-interval="5000" aria-label="Berita terbaru">
        <div class="relative overflow-hidden bg-kemenag-dark">
            <div class="relative min-h-[clamp(22rem,52vh,34rem)]">
                @foreach ($bannerPosts as $index => $banner)
                    <div
                        class="news-slide absolute inset-0 transition-opacity duration-700 ease-out {{ $index === 0 ? 'is-active opacity-100 z-[1]' : 'pointer-events-none opacity-0 z-0' }}"
                        data-slide="{{ $index }}"
                    >
                        <a href="{{ route('posts.show', $banner->slug) }}" class="group relative flex h-full min-h-[clamp(22rem,52vh,34rem)] flex-col justify-end">
                            @if ($banner->cover_image)
                                <img
                                    src="{{ asset('storage/'.$banner->cover_image) }}"
                                    alt=""
                                    class="absolute inset-0 h-full w-full object-cover opacity-70 transition duration-700 group-hover:scale-[1.03]"
                                    @if ($index === 0) fetchpriority="high" @else loading="lazy" @endif
                                >
                            @else
                                <div class="absolute inset-0 bg-[linear-gradient(135deg,#0a7a3e,#043f1f)]"></div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-kemenag-dark via-kemenag-dark/55 to-black/20"></div>
                            <div class="relative z-[1] px-6 py-10 md:px-10 md:py-14">
                                <div class="site-container !px-0">
                                    <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-gold">Berita Terbaru</p>
                                    @if ($banner->category)
                                        <span class="mt-3 inline-flex rounded bg-white/15 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-white">{{ $banner->category->name }}</span>
                                    @endif
                                    <h2 class="mt-3 max-w-4xl font-display text-2xl font-extrabold leading-tight text-white md:text-4xl">{{ $banner->title }}</h2>
                                    @if ($banner->excerpt)
                                        <p class="mt-3 max-w-3xl line-clamp-2 text-sm leading-relaxed text-white/85 md:text-base">{{ $banner->excerpt }}</p>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            @if ($bannerPosts->count() > 1)
                <div class="absolute bottom-4 right-4 z-10 flex items-center gap-2 md:bottom-6 md:right-8">
                    <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/15 text-white backdrop-blur hover:bg-white/25" data-slider-prev aria-label="Sebelumnya">‹</button>
                    <div class="flex gap-1.5 px-1" data-slider-dots>
                        @foreach ($bannerPosts as $index => $banner)
                            <button
                                type="button"
                                class="news-dot h-2 rounded-full transition-all {{ $index === 0 ? 'w-5 bg-gold' : 'w-2 bg-white/40' }}"
                                data-slider-dot="{{ $index }}"
                                aria-label="Slide {{ $index + 1 }}"
                            ></button>
                        @endforeach
                    </div>
                    <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/15 text-white backdrop-blur hover:bg-white/25" data-slider-next aria-label="Berikutnya">›</button>
                </div>
            @endif
        </div>
    </section>
    @endif

    <div class="site-container grid gap-8 py-8 lg:grid-cols-[minmax(0,1fr)_20rem] lg:gap-10 lg:py-10">
        <div class="min-w-0">
            <nav class="no-print flex flex-wrap items-center gap-2 text-xs font-semibold text-muted" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-kemenag">Beranda</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('posts.index') }}" class="hover:text-kemenag">Berita</a>
                <span aria-hidden="true">/</span>
                <span class="line-clamp-1 text-ink/70">{{ $post->title }}</span>
            </nav>

            <h1 class="article-title mt-4">{{ $post->title }}</h1>

            <div class="mt-4 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-muted">
                <time datetime="{{ optional($post->published_at)->toDateString() }}">{{ optional($post->published_at)->translatedFormat('l, d F Y') }}</time>
                <span aria-hidden="true">·</span>
                <span>{{ $post->readingMinutes() }} menit baca</span>
                <span aria-hidden="true">·</span>
                <span>{{ number_format($post->views_count) }} dilihat</span>
            </div>

            @if ($post->cover_image)
                <figure class="mt-7 overflow-hidden rounded-xl border border-kemenag/10 bg-kemenag-soft">
                    <img src="{{ asset('storage/'.$post->cover_image) }}" alt="{{ $post->title }}" class="aspect-[16/9] w-full object-cover">
                </figure>
            @endif

            <div class="prose prose-lg mt-8 max-w-none prose-headings:font-display prose-headings:text-kemenag-deep prose-a:text-kemenag prose-img:rounded-xl">
                {!! $post->body !!}
            </div>

            {{-- Tags, kontributor, redaktur di bawah berita --}}
            <footer class="article-meta-footer mt-10 border-t border-kemenag/10 pt-6">
                @if (count($postTags))
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-[0.14em] text-muted">Tags</span>
                        @foreach ($postTags as $tag)
                            <span class="article-tag">{{ $tag }}</span>
                        @endforeach
                    </div>
                @endif

                <div class="mt-5 grid gap-3 text-sm sm:grid-cols-2">
                    @if ($post->author_name)
                        <p><span class="font-bold text-kemenag-deep">Kontributor:</span> <span class="text-ink/85">{{ $post->author_name }}</span></p>
                    @endif
                    @if ($post->editor_name)
                        <p><span class="font-bold text-kemenag-deep">Redaktur:</span> <span class="text-ink/85">{{ $post->editor_name }}</span></p>
                    @endif
                </div>

                {{-- Share dengan ikon di bawah artikel --}}
                <div class="no-print mt-7" data-share-bar>
                    <p class="mb-3 text-xs font-bold uppercase tracking-[0.16em] text-muted">Bagikan</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ $waShare }}" target="_blank" rel="noopener" class="share-icon-btn" aria-label="Bagikan ke WhatsApp" title="WhatsApp">
                            <img src="{{ asset('images/social/whatsapp.png') }}" alt="" width="40" height="40">
                        </a>
                        <a href="{{ $fbShare }}" target="_blank" rel="noopener" class="share-icon-btn" aria-label="Bagikan ke Facebook" title="Facebook">
                            <img src="{{ asset('images/social/facebook.png') }}" alt="" width="40" height="40">
                        </a>
                        <a href="{{ $xShare }}" target="_blank" rel="noopener" class="share-icon-btn" aria-label="Bagikan ke X" title="X">
                            <img src="{{ asset('images/social/x.png') }}" alt="" width="40" height="40">
                        </a>
                        <a href="{{ $mailShare }}" class="share-icon-btn" aria-label="Bagikan lewat email" title="Email">
                            <img src="{{ asset('images/social/email.png') }}" alt="" width="40" height="40">
                        </a>
                        <button type="button" class="share-icon-btn" data-copy-link data-url="{{ $shareUrl }}" aria-label="Salin tautan" title="Salin tautan">
                            <img src="{{ asset('images/social/link.png') }}" alt="" width="40" height="40">
                        </button>
                    </div>
                </div>
            </footer>

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
