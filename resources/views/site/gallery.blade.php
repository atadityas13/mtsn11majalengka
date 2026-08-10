@extends('layouts.site')

@section('title', 'Galeri — '.$site->school_name)

@section('content')
<div class="bg-madrasah pt-28 text-white">
    <div class="mx-auto max-w-6xl px-4 py-12 md:px-6">
        <h1 class="font-display text-5xl">Galeri</h1>
    </div>
</div>
<section class="mx-auto max-w-6xl px-4 py-14 md:px-6">
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($items as $item)
            <figure>
                <div class="aspect-[4/3] overflow-hidden bg-madrasah/10">
                    <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->title }}" class="h-full w-full object-cover">
                </div>
                <figcaption class="mt-3">
                    <p class="font-display text-xl text-madrasah-dark">{{ $item->title }}</p>
                    @if ($item->caption)<p class="text-sm text-ink/65">{{ $item->caption }}</p>@endif
                </figcaption>
            </figure>
        @endforeach
    </div>
    <div class="mt-10">{{ $items->links() }}</div>
</section>
@endsection
