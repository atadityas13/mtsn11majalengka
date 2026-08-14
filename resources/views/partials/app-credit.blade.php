@php
    $schoolName = $schoolName ?? (\App\Models\SiteSetting::current()->school_name ?? 'MTsN 11 Majalengka');
    $variant = $variant ?? 'site';
    $kemenagUrl = 'https://kemenag.go.id/';
    $ataUrl = 'https://www.instagram.com/atadityas_13/';
@endphp

@if ($variant === 'login')
    <p class="sicoma-login-foot">
        &copy; {{ date('Y') }} {{ $schoolName }}. Seluruh hak dilindungi · Naungan
        <a href="{{ $kemenagUrl }}" target="_blank" rel="noopener noreferrer">Kementerian Agama RI</a>
        <span class="sicoma-login-credit">
            Developed by
            <a href="{{ $ataUrl }}" target="_blank" rel="noopener noreferrer"><strong>ATA DevLabs</strong></a>
        </span>
    </p>
@else
    {{-- site + admin: teks & struktur sama seperti footer frontend --}}
    <div @class([
        'sicoma-site-credit',
        'sicoma-site-credit--admin' => $variant === 'admin',
        'border-t border-white/10 px-4 py-5 text-center text-xs text-white/45' => $variant !== 'admin',
    ])>
        <p>
            &copy; {{ date('Y') }} {{ $schoolName }}. Seluruh hak dilindungi · Naungan
            <a href="{{ $kemenagUrl }}" target="_blank" rel="noopener noreferrer" @class([
                'font-semibold text-white/75 underline-offset-2 hover:text-white hover:underline' => $variant !== 'admin',
            ])>Kementerian Agama RI</a>.
        </p>
        <p @class(['mt-1.5', 'text-white/55' => $variant !== 'admin'])>
            Developed by
            <a href="{{ $ataUrl }}" target="_blank" rel="noopener noreferrer" @class([
                'font-semibold text-white/75 underline-offset-2 hover:text-white hover:underline' => $variant !== 'admin',
            ])>ATA DevLabs</a>
        </p>
    </div>
@endif
