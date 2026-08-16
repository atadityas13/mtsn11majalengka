@if ($site->mascotIsVisible())
    @php
        $customLines = collect(preg_split('/\r\n|\r|\n/', (string) $site->mascot_message))
            ->map(fn (string $line) => trim($line))
            ->filter()
            ->values()
            ->all();

        $hour = (int) now()->timezone(config('app.timezone', 'Asia/Jakarta'))->format('G');
        $timeHello = match (true) {
            $hour >= 4 && $hour < 11 => 'Selamat pagi',
            $hour >= 11 && $hour < 15 => 'Selamat siang',
            $hour >= 15 && $hour < 18 => 'Selamat sore',
            default => 'Selamat malam',
        };

        $school = $site->school_name ?: 'MTsN 11 Majalengka';

        $scriptMessages = array_values(array_unique(array_filter([
            "{$timeHello}! Aku Nelaska, robot sahabat {$school}.",
            ...$customLines,
            "Senang bertemu Anda di situs {$school}.",
            'Butuh info cepat? Klik tombol WhatsApp hijau di bawah ya!',
            'Sampai jumpa lagi — Nelaska selalu siap menyapa!',
        ])));
    @endphp

    <div
        class="site-mascot no-print{{ $site->whatsappLink() ? ' site-mascot--above-wa' : '' }}"
        x-data="siteMascot(@js($scriptMessages))"
        x-cloak
        x-show="visible"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-3"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0 translate-y-2"
        role="complementary"
        aria-label="Nelaska, robot ucapan situs"
        :class="{ 'is-talking': talking, 'is-listening': listening }"
    >
        <div
            class="site-mascot-bubble"
            x-show="talking && displayText"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <p x-text="displayText"></p>
        </div>

        <button
            type="button"
            class="site-mascot-robot"
            @click="onTap()"
            aria-label="Sembunyikan Nelaska sementara"
        >
            <img
                src="{{ asset('images/nelaska-mascot.png') }}"
                alt="Nelaska"
                width="160"
                height="160"
                class="site-mascot-img"
                loading="lazy"
                decoding="async"
            >
        </button>
    </div>
@endif
