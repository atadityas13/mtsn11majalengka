@extends('layouts.site')

@section('title', 'Layanan — '.$site->school_name)

@section('content')
<div class="bg-madrasah pt-28 text-white">
    <div class="mx-auto max-w-6xl px-4 py-12 md:px-6">
        <h1 class="font-display text-5xl">Layanan</h1>
        <p class="mt-3 max-w-2xl text-white/75">Akses layanan digital madrasah dan tautan resmi terkait.</p>
    </div>
</div>
<section class="mx-auto grid max-w-6xl gap-4 px-4 py-14 sm:grid-cols-2 md:px-6">
    @foreach ([
        ['PPDB Online', $settings->ppdb_url, 'Portal penerimaan peserta didik baru'],
        ['Rapor Digital Madrasah', $settings->rdm_url, 'Login rapor digital siswa'],
        ['Kementerian Agama', $settings->kemenag_url, 'Portal resmi Kemenag RI'],
    ] as [$label, $url, $desc])
        @if ($url)
            <a href="{{ $url }}" target="_blank" rel="noopener" class="border border-madrasah/15 bg-white p-6 transition hover:border-madrasah/40">
                <h2 class="font-display text-3xl text-madrasah-dark">{{ $label }}</h2>
                <p class="mt-2 text-sm text-ink/70">{{ $desc }}</p>
            </a>
        @endif
    @endforeach
</section>
@endsection
