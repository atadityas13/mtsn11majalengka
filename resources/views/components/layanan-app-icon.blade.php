@props(['type' => 'default', 'logo' => null, 'label' => ''])

@if ($logo)
    <img src="{{ asset('storage/'.$logo) }}" alt="" class="layanan-ticker-logo" loading="lazy">
@else
    <span class="layanan-ticker-logo layanan-ticker-logo--mark" aria-hidden="true">
        @switch($type)
            @case('ppdb')
                <svg viewBox="0 0 40 40" class="h-7 w-7" fill="none"><rect width="40" height="40" rx="10" fill="#0a7a3e"/><path d="M12 28V12h10.5a5.5 5.5 0 0 1 0 11H16v5H12zm4-9h6a2 2 0 1 0 0-4H16v4z" fill="#fff"/><path d="M22 22l6 6M28 22l-6 6" stroke="#d4a017" stroke-width="2" stroke-linecap="round"/></svg>
                @break
            @case('rdm')
                <svg viewBox="0 0 40 40" class="h-7 w-7" fill="none"><rect width="40" height="40" rx="10" fill="#065c2e"/><path d="M11 10h13l5 5v15a2 2 0 0 1-2 2H11a2 2 0 0 1-2-2V12a2 2 0 0 1 2-2z" fill="#fff" fill-opacity=".95"/><path d="M24 10v5h5" stroke="#0a7a3e" stroke-width="1.5"/><path d="M14 19h10M14 23h10M14 27h7" stroke="#0a7a3e" stroke-width="1.6" stroke-linecap="round"/></svg>
                @break
            @case('kemenag')
                <svg viewBox="0 0 40 40" class="h-7 w-7" fill="none"><rect width="40" height="40" rx="10" fill="#043f1f"/><circle cx="20" cy="18" r="8" stroke="#d4a017" stroke-width="2"/><path d="M20 11v14M14 18h12" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/><path d="M12 30h16" stroke="#d4a017" stroke-width="2" stroke-linecap="round"/></svg>
                @break
            @case('contact')
                <svg viewBox="0 0 40 40" class="h-7 w-7" fill="none"><rect width="40" height="40" rx="10" fill="#0a7a3e"/><path d="M14 12h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-5l-4 4v-4h-3a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2z" fill="#fff"/><circle cx="16.5" cy="18" r="1.2" fill="#0a7a3e"/><circle cx="20" cy="18" r="1.2" fill="#0a7a3e"/><circle cx="23.5" cy="18" r="1.2" fill="#0a7a3e"/></svg>
                @break
            @case('download')
                <svg viewBox="0 0 40 40" class="h-7 w-7" fill="none"><rect width="40" height="40" rx="10" fill="#065c2e"/><path d="M20 10v14m0 0l-5-5m5 5l5-5M12 28h16" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                @break
            @case('video')
                <svg viewBox="0 0 40 40" class="h-7 w-7" fill="none"><rect width="40" height="40" rx="10" fill="#0a7a3e"/><rect x="9" y="13" width="15" height="14" rx="2" fill="#fff"/><path d="M26 16l6-3v14l-6-3V16z" fill="#d4a017"/></svg>
                @break
            @case('staff')
                <svg viewBox="0 0 40 40" class="h-7 w-7" fill="none"><rect width="40" height="40" rx="10" fill="#043f1f"/><circle cx="20" cy="15" r="5" fill="#fff"/><path d="M10 29c1.5-5 5-7.5 10-7.5S28.5 24 30 29" stroke="#d4a017" stroke-width="2.2" stroke-linecap="round"/></svg>
                @break
            @default
                <svg viewBox="0 0 40 40" class="h-7 w-7" fill="none"><rect width="40" height="40" rx="10" fill="#0a7a3e"/><text x="20" y="25" text-anchor="middle" fill="#fff" font-size="14" font-weight="700" font-family="Outfit, sans-serif">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($label, 0, 1)) }}</text></svg>
        @endswitch
    </span>
@endif
