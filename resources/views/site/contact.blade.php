@extends('layouts.site')

@section('title', 'Kontak — '.$site->school_name)

@section('content')
<div class="bg-madrasah pt-28 text-white">
    <div class="mx-auto max-w-6xl px-4 py-12 md:px-6">
        <h1 class="font-display text-5xl">Kontak</h1>
    </div>
</div>
<section class="mx-auto grid max-w-6xl gap-10 px-4 py-14 md:grid-cols-2 md:px-6">
    <div class="space-y-4 text-sm leading-relaxed text-ink/80">
        <p><span class="font-semibold text-madrasah-dark">Alamat</span><br>{{ $settings->address }}</p>
        <p><span class="font-semibold text-madrasah-dark">Telepon</span><br>{{ $settings->phone }}</p>
        <p><span class="font-semibold text-madrasah-dark">Email</span><br>{{ $settings->email }}</p>
        <p><span class="font-semibold text-madrasah-dark">NPSN</span><br>{{ $settings->npsn }}</p>
    </div>
    <div class="min-h-72 bg-madrasah/5">
        @if ($settings->map_embed_url)
            <iframe src="{{ $settings->map_embed_url }}" class="h-full min-h-72 w-full border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        @else
            <div class="flex h-full min-h-72 items-center justify-center text-sm text-ink/50">
                Tambahkan URL embed peta di Pengaturan Situs.
            </div>
        @endif
    </div>
</section>
@endsection
