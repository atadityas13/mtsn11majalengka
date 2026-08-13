@extends('layouts.site')

@section('title', 'Kontak — '.$site->school_name)
@section('description', 'Hubungi '.$site->school_name)

@section('content')
<div class="border-b border-kemenag/10 bg-kemenag-deep text-white">
    <div class="site-container py-12">
        <h1 class="font-display text-4xl font-extrabold md:text-5xl">Kontak</h1>
        <p class="mt-3 max-w-2xl text-white/75">Sampaikan pertanyaan atau saran melalui formulir, telepon, atau WhatsApp.</p>
    </div>
</div>

<section class="site-container grid gap-8 py-12 lg:grid-cols-2">
    <div class="space-y-6">
        <div class="space-y-4 rounded-2xl border border-kemenag/10 bg-white p-6 text-sm leading-relaxed shadow-sm">
            <p><span class="font-bold text-kemenag-deep">Alamat</span><br>{{ $settings->address }}</p>
            <p><span class="font-bold text-kemenag-deep">Telepon</span><br>{{ $settings->phone }}</p>
            <p><span class="font-bold text-kemenag-deep">Email</span><br>{{ $settings->email }}</p>
            <p><span class="font-bold text-kemenag-deep">NPSN</span><br>{{ $settings->npsn }}</p>
            @if ($settings->whatsappLink())
                <a href="{{ $settings->whatsappLink() }}" target="_blank" rel="noopener" class="btn-primary">Chat WhatsApp</a>
            @endif
        </div>
        <div class="min-h-72 overflow-hidden rounded-2xl border border-kemenag/10 bg-kemenag-soft">
            @if ($settings->map_embed_url)
                <iframe src="{{ $settings->map_embed_url }}" class="h-full min-h-72 w-full border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            @else
                <div class="flex h-full min-h-72 items-center justify-center text-sm text-muted">
                    Peta lokasi belum tersedia.
                </div>
            @endif
        </div>
    </div>

    <div class="rounded-2xl border border-kemenag/10 bg-white p-6 shadow-sm md:p-8">
        <h2 class="font-display text-2xl font-extrabold text-kemenag-deep">Kirim pesan</h2>
        <p class="mt-2 text-sm text-muted">Sampaikan pertanyaan atau saran Anda. Kami akan merespons secepatnya.</p>

        @if (session('success'))
            <div class="mt-4 rounded-md border border-kemenag/20 bg-kemenag-soft px-4 py-3 text-sm font-semibold text-kemenag-deep">
                {{ session('success') }}
            </div>
        @endif

        <form method="post" action="{{ route('contact.store') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-muted">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-md border border-kemenag/20 px-3 py-2.5 text-sm outline-none focus:border-kemenag focus:ring-2 focus:ring-kemenag/20">
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-muted">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-md border border-kemenag/20 px-3 py-2.5 text-sm outline-none focus:border-kemenag focus:ring-2 focus:ring-kemenag/20">
                    @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-muted">Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-md border border-kemenag/20 px-3 py-2.5 text-sm outline-none focus:border-kemenag focus:ring-2 focus:ring-kemenag/20">
                </div>
            </div>
            <div>
                <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-muted">Subjek</label>
                <input type="text" name="subject" value="{{ old('subject') }}" class="w-full rounded-md border border-kemenag/20 px-3 py-2.5 text-sm outline-none focus:border-kemenag focus:ring-2 focus:ring-kemenag/20">
            </div>
            <div>
                <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-muted">Pesan</label>
                <textarea name="message" rows="5" required class="w-full rounded-md border border-kemenag/20 px-3 py-2.5 text-sm outline-none focus:border-kemenag focus:ring-2 focus:ring-kemenag/20">{{ old('message') }}</textarea>
                @error('message')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="btn-primary">Kirim pesan</button>
        </form>
    </div>
</section>
@endsection
