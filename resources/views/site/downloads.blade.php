@extends('layouts.site')

@section('title', 'Unduhan — '.$site->school_name)
@section('description', 'Dokumen dan berkas unduhan '.$site->school_name)

@section('content')
<div class="border-b border-kemenag/10 bg-kemenag-deep text-white">
    <div class="site-container py-12">
        <h1 class="font-display text-4xl font-extrabold md:text-5xl">Unduhan</h1>
        <p class="mt-3 text-white/75">Dokumen resmi madrasah yang dapat diunduh.</p>
    </div>
</div>
<section class="site-container space-y-4 py-12">
    @forelse ($downloads as $item)
        <article class="flex flex-col gap-4 rounded-2xl border border-kemenag/10 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div>
                @if ($item->category)
                    <p class="news-meta text-kemenag">{{ $item->category }}</p>
                @endif
                <h2 class="mt-1 font-display text-xl font-extrabold text-kemenag-deep">{{ $item->title }}</h2>
                @if ($item->description)
                    <p class="mt-2 text-sm text-muted">{{ $item->description }}</p>
                @endif
                <p class="mt-2 text-xs text-muted">Diunduh {{ $item->download_count }} kali</p>
            </div>
            <a href="{{ route('downloads.file', $item) }}" class="btn-primary shrink-0">Unduh</a>
        </article>
    @empty
        <p class="rounded-2xl border border-dashed border-kemenag/20 bg-white p-8 text-muted">Belum ada berkas unduhan. Unggah dari panel admin.</p>
    @endforelse
    <div>{{ $downloads->links() }}</div>
</section>
@endsection
