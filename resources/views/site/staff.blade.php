@extends('layouts.site')

@section('title', 'Guru & Tendik — '.$site->school_name)
@section('description', 'Tenaga pendidik dan kependidikan '.$site->school_name)

@section('content')
<div class="border-b border-kemenag/10 bg-kemenag-deep text-white">
    <div class="site-container py-12">
        <h1 class="font-display text-4xl font-extrabold md:text-5xl">Guru & Tendik</h1>
        <p class="mt-3 text-white/75">Profil foto dapat diganti kapan saja dari panel admin.</p>
    </div>
</div>
<section class="site-container py-12">
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @forelse ($staff as $member)
            <article class="overflow-hidden rounded-2xl border border-kemenag/10 bg-white text-center shadow-sm">
                <div class="aspect-square bg-kemenag-soft">
                    @if ($member->photo)
                        <img src="{{ asset('storage/'.$member->photo) }}" alt="{{ $member->name }}" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full items-center justify-center font-display text-4xl font-extrabold text-kemenag/30">
                            {{ \Illuminate\Support\Str::substr($member->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                <div class="p-4">
                    <h2 class="font-display text-lg font-extrabold text-kemenag-deep">{{ $member->name }}</h2>
                    <p class="mt-1 text-sm font-semibold text-kemenag">{{ $member->role }}</p>
                </div>
            </article>
        @empty
            <p class="col-span-full rounded-2xl border border-dashed border-kemenag/20 bg-white p-8 text-muted">Belum ada data guru/tendik. Tambahkan dari panel admin.</p>
        @endforelse
    </div>
</section>
@endsection
