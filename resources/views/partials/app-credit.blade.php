@php
    $schoolName = $schoolName ?? (\App\Models\SiteSetting::current()->school_name ?? 'MTsN 11 Majalengka');
    $variant = $variant ?? 'site';
    $kemenagUrl = 'https://kemenag.go.id/';
    $ataUrl = 'https://www.instagram.com/atadityas_13/';
@endphp

@if ($variant === 'admin')
    <div class="sicoma-admin-credit">
        <p>
            &copy; {{ date('Y') }} {{ $schoolName }} · Si COMA · Naungan
            <a href="{{ $kemenagUrl }}" target="_blank" rel="noopener noreferrer">Kementerian Agama RI</a>
        </p>
        <p class="sicoma-admin-credit__dev">
            Developed by
            <a href="{{ $ataUrl }}" target="_blank" rel="noopener noreferrer">ATA DevLabs</a>
        </p>
    </div>
@elseif ($variant === 'login')
    <p class="sicoma-login-foot">
        &copy; {{ date('Y') }} {{ $schoolName }} · Naungan
        <a href="{{ $kemenagUrl }}" target="_blank" rel="noopener noreferrer">Kementerian Agama RI</a>
        <span class="sicoma-login-credit">
            Developed by
            <a href="{{ $ataUrl }}" target="_blank" rel="noopener noreferrer"><strong>ATA DevLabs</strong></a>
        </span>
    </p>
@else
    <div class="border-t border-white/10 px-4 py-5 text-center text-xs text-white/45">
        <p>
            &copy; {{ date('Y') }} {{ $schoolName }}. Seluruh hak dilindungi · Naungan
            <a href="{{ $kemenagUrl }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-white/75 underline-offset-2 hover:text-white hover:underline">Kementerian Agama RI</a>.
        </p>
        <p class="mt-1.5 text-white/55">
            Developed by
            <a href="{{ $ataUrl }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-white/75 underline-offset-2 hover:text-white hover:underline">ATA DevLabs</a>
        </p>
    </div>
@endif
