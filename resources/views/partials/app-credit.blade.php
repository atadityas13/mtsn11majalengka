@php
    $schoolName = $schoolName ?? (\App\Models\SiteSetting::current()->school_name ?? 'MTsN 11 Majalengka');
    $variant = $variant ?? 'site';
    $kemenagUrl = 'https://kemenag.go.id/';
    $ataUrl = 'https://www.instagram.com/atadityas_13/';
@endphp

@if ($variant === 'admin')
    <div class="w-full border-t border-gray-200 bg-white px-4 py-4 text-center text-xs text-gray-500 dark:border-white/10 dark:bg-gray-950 dark:text-gray-400">
        <p>
            &copy; {{ date('Y') }} {{ $schoolName }} · Si COMA · Naungan
            <a href="{{ $kemenagUrl }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-gray-700 underline-offset-2 hover:underline dark:text-gray-200">Kementerian Agama RI</a>
        </p>
        <p class="mt-1.5">
            Developed by
            <a href="{{ $ataUrl }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-gray-700 underline-offset-2 hover:underline dark:text-gray-200">ATA DevLabs</a>
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
