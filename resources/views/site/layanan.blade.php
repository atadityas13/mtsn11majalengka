@extends('layouts.site')

@section('title', 'Layanan — '.$site->school_name)

@section('content')
<div class="border-b border-kemenag/10 bg-kemenag-deep text-white">
    <div class="site-container py-12">
        <h1 class="font-display text-4xl font-extrabold md:text-5xl">Layanan</h1>
        <p class="mt-3 max-w-2xl text-white/75">Akses layanan digital madrasah dan tautan resmi terkait.</p>
    </div>
</div>
<section class="site-container grid gap-4 py-12 sm:grid-cols-2">
    @foreach ([
        ['PPDB Online', $settings->ppdb_url, 'Portal penerimaan peserta didik baru'],
        ['Rapor Digital Madrasah', $settings->rdm_url, 'Login rapor digital siswa'],
        ['Kementerian Agama', $settings->kemenag_url, 'Portal resmi Kemenag RI'],
    ] as [$label, $url, $desc])
        @if ($url)
            <a href="{{ $url }}" target="_blank" rel="noopener" class="rounded-2xl border border-kemenag/10 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-kemenag/30 hover:shadow-md">
                <h2 class="font-display text-2xl font-extrabold text-kemenag-deep">{{ $label }}</h2>
                <p class="mt-2 text-sm text-muted">{{ $desc }}</p>
            </a>
        @endif
    @endforeach
</section>
@endsection
