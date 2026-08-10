@extends('layouts.site')

@section('title', 'Galeri — '.$site->school_name)

@section('content')
<div class="border-b border-kemenag/10 bg-kemenag-deep text-white">
    <div class="site-container py-12">
        <h1 class="font-display text-4xl font-extrabold md:text-5xl">Galeri</h1>
    </div>
</div>
<section class="site-container py-12">
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($items as $item)
            <figure class="overflow-hidden rounded-2xl border border-kemenag/10 bg-white shadow-sm">
                <div class="aspect-[4/3] overflow-hidden bg-kemenag-soft">
                    <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->title }}" class="h-full w-full object-cover">
                </div>
                <figcaption class="p-4">
                    <p class="font-display text-lg font-bold text-kemenag-deep">{{ $item->title }}</p>
                    @if ($item->caption)<p class="mt-1 text-sm text-muted">{{ $item->caption }}</p>@endif
                </figcaption>
            </figure>
        @endforeach
    </div>
    <div class="mt-10">{{ $items->links() }}</div>
</section>
@endsection
