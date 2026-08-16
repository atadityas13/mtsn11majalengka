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
            x-show="talking && fullText"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <p class="site-mascot-bubble-text">
                <span x-text="displayText"></span><span class="site-mascot-bubble-pending" aria-hidden="true" x-text="pendingText"></span>
            </p>
        </div>

        <button
            type="button"
            class="site-mascot-robot"
            @click="onTap()"
            aria-label="Sembunyikan Nelaska sementara"
        >
            {{-- SVG mirip gradient AI robot (kepala layar + tubuh bulat + lengan gantung) --}}
            <div class="robot-root">
                <svg viewBox="0 0 200 220" class="h-full w-full" aria-hidden="true">
                    <defs>
                        <linearGradient id="nelaskaBody" x1="18%" y1="8%" x2="86%" y2="92%">
                            <stop offset="0%" stop-color="#ffffff"/>
                            <stop offset="42%" stop-color="#f3f4f6"/>
                            <stop offset="100%" stop-color="#d1d5db"/>
                        </linearGradient>
                        <linearGradient id="nelaskaBodySide" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#9ca3af" stop-opacity="0.35"/>
                            <stop offset="35%" stop-color="#ffffff" stop-opacity="0"/>
                            <stop offset="65%" stop-color="#ffffff" stop-opacity="0"/>
                            <stop offset="100%" stop-color="#9ca3af" stop-opacity="0.28"/>
                        </linearGradient>
                        <linearGradient id="nelaskaFace" x1="50%" y1="0%" x2="50%" y2="100%">
                            <stop offset="0%" stop-color="#1f2937"/>
                            <stop offset="100%" stop-color="#0b1220"/>
                        </linearGradient>
                        <linearGradient id="nelaskaArm" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#ffffff"/>
                            <stop offset="100%" stop-color="#c4c9d1"/>
                        </linearGradient>
                        <radialGradient id="nelaskaGlow" cx="50%" cy="50%" r="50%">
                            <stop offset="0%" stop-color="#67e8f9" stop-opacity="0.9"/>
                            <stop offset="100%" stop-color="#22d3ee" stop-opacity="0"/>
                        </radialGradient>
                        <filter id="nelaskaSoft" x="-25%" y="-25%" width="150%" height="150%">
                            <feDropShadow dx="0" dy="6" stdDeviation="5" flood-color="#0f172a" flood-opacity="0.18"/>
                        </filter>
                    </defs>

                    {{-- Bayangan hover --}}
                    <ellipse class="robot-shadow" cx="100" cy="205" rx="40" ry="7.5" fill="#94a3b8" opacity="0.32"/>

                    {{-- Lengan stub (di belakang tubuh) — standby bawah --}}
                    <g class="robot-arm robot-arm--left" style="transform-origin: 64px 130px;">
                        <path d="M64,126 C52,132 48,152 50,172 C52,178 58,180 62,176 C66,160 66,142 64,126 Z" fill="url(#nelaskaArm)"/>
                    </g>
                    <g class="robot-arm robot-arm--right" style="transform-origin: 136px 130px;">
                        <path d="M136,126 C148,132 152,152 150,172 C148,178 142,180 138,176 C134,160 134,142 136,126 Z" fill="url(#nelaskaArm)"/>
                    </g>

                    {{-- Tubuh bulat --}}
                    <g filter="url(#nelaskaSoft)">
                        <ellipse cx="100" cy="148" rx="40" ry="44" fill="url(#nelaskaBody)"/>
                        <ellipse cx="100" cy="148" rx="40" ry="44" fill="url(#nelaskaBodySide)"/>
                        <path d="M78,146 H122" stroke="#cbd5e1" stroke-width="2.5" stroke-linecap="round" opacity="0.75"/>
                        <path d="M84,158 H116" stroke="#e5e7eb" stroke-width="2" stroke-linecap="round" opacity="0.85"/>
                    </g>

                    {{-- Joint telinga --}}
                    <circle cx="50" cy="64" r="10" fill="url(#nelaskaBody)"/>
                    <circle cx="150" cy="64" r="10" fill="url(#nelaskaBody)"/>
                    <circle cx="50" cy="64" r="5" fill="#d1d5db"/>
                    <circle cx="150" cy="64" r="5" fill="#d1d5db"/>

                    {{-- Kepala + layar wajah --}}
                    <g filter="url(#nelaskaSoft)">
                        <rect x="52" y="26" width="96" height="82" rx="28" fill="url(#nelaskaBody)"/>
                        <rect x="52" y="26" width="96" height="82" rx="28" fill="url(#nelaskaBodySide)"/>
                        <rect x="64" y="40" width="72" height="54" rx="18" fill="url(#nelaskaFace)"/>
                        <rect x="64" y="40" width="72" height="54" rx="18" fill="url(#nelaskaGlow)" opacity="0.16" class="robot-face-glow"/>

                        <g class="robot-face">
                            <path class="robot-eye" d="M76,58 Q86,48 96,58" fill="none" stroke="#22d3ee" stroke-width="5" stroke-linecap="round"/>
                            <path class="robot-eye" d="M104,58 Q114,48 124,58" fill="none" stroke="#22d3ee" stroke-width="5" stroke-linecap="round"/>
                            <path class="robot-mouth" d="M90,74 Q100,84 110,74" fill="none" stroke="#22d3ee" stroke-width="4.5" stroke-linecap="round"/>
                        </g>
                    </g>
                </svg>
            </div>
        </button>
    </div>
@endif
