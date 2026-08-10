@extends('layouts.site')

@section('title', 'Kontak — '.$site->school_name)

@section('content')
<div class="border-b border-kemenag/10 bg-kemenag-deep text-white">
    <div class="site-container py-12">
        <h1 class="font-display text-4xl font-extrabold md:text-5xl">Kontak</h1>
    </div>
</div>
<section class="site-container grid gap-8 py-12 md:grid-cols-2">
    <div class="space-y-4 rounded-2xl border border-kemenag/10 bg-white p-6 text-sm leading-relaxed shadow-sm">
        <p><span class="font-bold text-kemenag-deep">Alamat</span><br>{{ $settings->address }}</p>
        <p><span class="font-bold text-kemenag-deep">Telepon</span><br>{{ $settings->phone }}</p>
        <p><span class="font-bold text-kemenag-deep">Email</span><br>{{ $settings->email }}</p>
        <p><span class="font-bold text-kemenag-deep">NPSN</span><br>{{ $settings->npsn }}</p>
    </div>
    <div class="min-h-72 overflow-hidden rounded-2xl border border-kemenag/10 bg-kemenag-soft">
        @if ($settings->map_embed_url)
            <iframe src="{{ $settings->map_embed_url }}" class="h-full min-h-72 w-full border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        @else
            <div class="flex h-full min-h-72 items-center justify-center text-sm text-muted">
                Tambahkan URL embed peta di Pengaturan Situs.
            </div>
        @endif
    </div>
</section>
@endsection
