@props(['logo' => null, 'label' => ''])

@if ($logo)
    <img src="{{ asset('storage/'.$logo) }}" alt="" class="layanan-ticker-logo" loading="lazy">
@else
    <span class="layanan-ticker-logo layanan-ticker-logo--mark" aria-hidden="true">
        <span class="layanan-ticker-initial">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($label, 0, 1)) }}</span>
    </span>
@endif
