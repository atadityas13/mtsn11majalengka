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
    $tgShare = 'https://t.me/share/url?url='.rawurlencode($shareUrl).'&text='.rawurlencode($shareTitle);
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

            <div class="article-content mt-8">
                {!! $post->renderedBody() !!}
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

                {{-- Share: ikon brand seperti situs berita --}}
                <div class="no-print mt-7" data-share-bar>
                    <p class="mb-3 text-sm font-bold text-ink">Bagikan Artikel</p>
                    <div class="flex flex-wrap items-center gap-2.5">
                        <a href="{{ $fbShare }}" target="_blank" rel="noopener" class="share-icon-btn share-icon-btn--fb" aria-label="Bagikan ke Facebook" title="Facebook">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M14.5 8.5V6.9c0-.7.5-1.4 1.5-1.4h1.5V3h-2.1C12.7 3 11 4.7 11 7.1v1.4H9v2.9h2V21h3.5v-9.6h2.3l.7-2.9h-3Z"/></svg>
                        </a>
                        <a href="{{ $xShare }}" target="_blank" rel="noopener" class="share-icon-btn share-icon-btn--x" aria-label="Bagikan ke X" title="X">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M17.7 3H20l-6.2 7.1L21 21h-5.9l-4.6-6-5.3 6H3.2l6.6-7.6L3 3h6l4.2 5.5L17.7 3Zm-1 16.4h1.6L7.4 4.5H5.7l11 14.9Z"/></svg>
                        </a>
                        <a href="{{ $waShare }}" target="_blank" rel="noopener" class="share-icon-btn share-icon-btn--wa" aria-label="Bagikan ke WhatsApp" title="WhatsApp">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12.04 2C6.58 2 2.15 6.4 2.15 11.82c0 1.96.52 3.87 1.5 5.55L2 22l4.8-1.56a10 10 0 0 0 5.24 1.43h.01c5.46 0 9.89-4.4 9.89-9.82C21.94 6.4 17.5 2 12.04 2Zm5.77 13.9c-.24.67-1.4 1.24-1.94 1.32-.5.07-1.13.1-1.82-.11-.42-.13-.96-.31-1.65-.6-2.9-1.25-4.79-4.17-4.94-4.36-.14-.19-1.2-1.6-1.2-3.05 0-1.45.76-2.16 1.03-2.45.27-.29.59-.36.79-.36h.57c.18 0 .42-.07.66.5.24.58.82 2 .89 2.15.07.14.12.32.02.51-.1.2-.14.32-.28.49-.14.17-.3.38-.42.51-.14.14-.29.29-.12.57.16.28.73 1.2 1.56 1.94 1.07.96 1.97 1.26 2.25 1.4.28.14.44.12.61-.07.16-.19.7-.81.89-1.09.19-.28.37-.23.63-.14.26.1 1.64.77 1.92.91.28.14.47.21.54.32.07.12.07.67-.17 1.34Z"/></svg>
                        </a>
                        <a href="{{ $tgShare }}" target="_blank" rel="noopener" class="share-icon-btn share-icon-btn--tg" aria-label="Bagikan ke Telegram" title="Telegram">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M21.5 3.3 2.9 10.4c-1.3.5-1.3 1.2-.2 1.5l4.8 1.5 1.8 5.6c.2.7.4 1 .9 1 .6 0 .8-.2 1.1-.5l2.7-2.6 5.6 4.1c1 .6 1.8.3 2.1-1l3.7-17.4c.4-1.5-.5-2.2-1.9-1.6ZM8.7 13.6l9.9-6.2c.5-.3.9-.1.5.2l-8 7.2-.3 3.3-1.9-4.5-.2-.2Z"/></svg>
                        </a>
                        <button type="button" class="share-icon-btn share-icon-btn--copy" data-copy-link data-url="{{ $shareUrl }}" aria-label="Salin tautan" title="Salin tautan">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M16 1H4c-1.1 0-2 .9-2 2v12h2V3h12V1Zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2Zm0 16H8V7h11v14Z"/></svg>
                        </button>
                        <button type="button" class="share-icon-btn share-icon-btn--print" onclick="window.print()" aria-label="Cetak artikel" title="Cetak">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3Zm-3 11H8v-5h8v5Zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1Zm-1-9H6v4h12V3Z"/></svg>
                        </button>
                    </div>
                </div>
            </footer>

            @if ($related)
                <section class="no-print related-news mt-12" aria-labelledby="related-news-heading">
                    <p class="section-label">Rekomendasi</p>
                    <h2 id="related-news-heading" class="mt-2 font-display text-2xl font-extrabold text-kemenag-deep">Berita terkait</h2>
                    <a href="{{ route('posts.show', $related->slug) }}" class="related-news__featured group mt-6">
                        <div class="related-news__featured-thumb">
                            @if ($related->cover_image)
                                <img src="{{ asset('storage/'.$related->cover_image) }}" alt="" loading="lazy">
                            @else
                                <span>MTsN 11</span>
                            @endif
                        </div>
                        <div class="related-news__featured-body">
                            @if ($related->category)
                                <p class="related-news__cat">{{ $related->category->name }}</p>
                            @endif
                            <h3>{{ $related->title }}</h3>
                            @if ($related->excerpt)
                                <p class="related-news__excerpt">{{ \Illuminate\Support\Str::limit($related->excerpt, 140) }}</p>
                            @endif
                            <p class="related-news__meta">{{ optional($related->published_at)->translatedFormat('d M Y') }} · {{ number_format($related->views_count) }} dilihat</p>
                        </div>
                    </a>
                </section>
            @endif

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

            @include('site.partials.archive-widget', [
                'search' => '',
                'activeCategory' => '',
                'activeYear' => null,
                'activeMonth' => null,
            ])
        </aside>
    </div>
</article>
@endsection
