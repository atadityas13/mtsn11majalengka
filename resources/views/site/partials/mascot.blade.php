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
            <p class="site-mascot-name">Nelaska</p>
            <p x-text="displayText" class="min-h-[2.5em]"></p>
            <div class="site-mascot-hint">Klik Nelaska untuk menyembunyikan sementara</div>
        </div>

        <button
            type="button"
            class="site-mascot-robot"
            @click="onTap()"
            aria-label="Sembunyikan Nelaska sementara"
        >
            <span class="site-mascot-aura" aria-hidden="true"></span>
            <div class="robot-root-masterpiece">
                <svg viewBox="0 0 200 220" class="h-full w-full" role="img" aria-hidden="true">
                    <defs>
                        <linearGradient id="nelaskaBody" x1="18%" y1="8%" x2="88%" y2="92%">
                            <stop offset="0%" stop-color="#f8fafc"/>
                            <stop offset="42%" stop-color="#d1fae5"/>
                            <stop offset="100%" stop-color="#0a7a3e"/>
                        </linearGradient>
                        <linearGradient id="nelaskaVisor" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#022c22"/>
                            <stop offset="100%" stop-color="#0f172a"/>
                        </linearGradient>
                        <linearGradient id="nelaskaGold" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#f5e6a6"/>
                            <stop offset="100%" stop-color="#d4a017"/>
                        </linearGradient>
                        <radialGradient id="nelaskaEye" cx="50%" cy="45%" r="55%">
                            <stop offset="0%" stop-color="#7dd3fc"/>
                            <stop offset="55%" stop-color="#22d3ee"/>
                            <stop offset="100%" stop-color="#0ea5e9" stop-opacity="0"/>
                        </radialGradient>
                        <radialGradient id="nelaskaCoreGlow" cx="50%" cy="50%" r="50%">
                            <stop offset="0%" stop-color="#86efac"/>
                            <stop offset="100%" stop-color="#0a7a3e" stop-opacity="0"/>
                        </radialGradient>
                        <filter id="nelaskaShadow" x="-25%" y="-25%" width="150%" height="150%">
                            <feGaussianBlur in="SourceAlpha" stdDeviation="3.5"/>
                            <feOffset dx="1" dy="3" result="offsetblur"/>
                            <feComponentTransfer><feFuncA type="linear" slope="0.35"/></feComponentTransfer>
                            <feMerge><feMergeNode/><feMergeNode in="SourceGraphic"/></feMerge>
                        </filter>
                    </defs>

                    {{-- Hover pad --}}
                    <ellipse cx="100" cy="208" rx="42" ry="7" fill="#0a7a3e" opacity="0.18" class="robot-hover-pod"/>
                    <ellipse cx="100" cy="206" rx="28" ry="4" fill="#38bdf8" opacity="0.35" class="robot-hover-pod"/>

                    {{-- Antenna --}}
                    <line x1="100" y1="38" x2="100" y2="18" stroke="#94a3b8" stroke-width="3" stroke-linecap="round"/>
                    <circle cx="100" cy="14" r="6" fill="url(#nelaskaGold)" class="robot-antenna-tip"/>
                    <circle cx="100" cy="14" r="10" fill="#d4a017" opacity="0.25" class="robot-antenna-tip"/>

                    {{-- Body --}}
                    <path d="M62,70 Q62,34 100,32 Q138,34 138,70 Q138,94 124,104 Q148,114 148,146 Q148,178 100,180 Q52,178 52,146 Q52,114 76,104 Q62,94 62,70 Z"
                          fill="url(#nelaskaBody)" stroke="#064e3b" stroke-width="1.6" filter="url(#nelaskaShadow)"/>

                    {{-- Shoulder lights --}}
                    <circle cx="58" cy="118" r="9" fill="url(#nelaskaBody)" stroke="#064e3b" stroke-width="1.4"/>
                    <circle cx="142" cy="118" r="9" fill="url(#nelaskaBody)" stroke="#064e3b" stroke-width="1.4"/>
                    <circle cx="58" cy="118" r="3.5" fill="#22d3ee" class="robot-shoulder-led"/>
                    <circle cx="142" cy="118" r="3.5" fill="#22d3ee" class="robot-shoulder-led"/>

                    {{-- Arms --}}
                    <g style="transform-origin: 142px 118px;">
                        <path d="M142,118 Q168,112 176,88" fill="none" stroke="#cbd5e1" stroke-width="11" stroke-linecap="round"/>
                        <circle cx="176" cy="88" r="7.5" fill="#fff" stroke="#064e3b" stroke-width="1.8"/>
                    </g>
                    <g class="robot-arm" style="transform-origin: 58px 118px;">
                        <path d="M58,118 Q32,112 24,88" fill="none" stroke="#cbd5e1" stroke-width="11" stroke-linecap="round"/>
                        <circle cx="24" cy="88" r="7.5" fill="#fff" stroke="#064e3b" stroke-width="1.8"/>
                    </g>

                    {{-- Visor --}}
                    <rect x="70" y="58" width="60" height="34" rx="16" fill="url(#nelaskaVisor)" opacity="0.96"/>
                    <rect x="74" y="62" width="52" height="8" rx="4" fill="#34d399" opacity="0.15"/>

                    <g class="robot-eye" style="transform-origin: 88px 76px;">
                        <circle cx="88" cy="76" r="8" fill="#67e8f9"/>
                        <circle cx="88" cy="76" r="13" fill="url(#nelaskaEye)"/>
                        <circle class="robot-pupil" cx="90" cy="77" r="2.8" fill="#022c22"/>
                    </g>
                    <g class="robot-eye" style="transform-origin: 112px 76px;">
                        <circle cx="112" cy="76" r="8" fill="#67e8f9"/>
                        <circle cx="112" cy="76" r="13" fill="url(#nelaskaEye)"/>
                        <circle class="robot-pupil" cx="114" cy="77" r="2.8" fill="#022c22"/>
                    </g>

                    {{-- Mouth bar --}}
                    <rect x="86" y="96" width="28" height="4" rx="2" fill="#34d399" class="robot-mouth" style="transform-origin: center;"/>

                    {{-- Name badge --}}
                    <rect x="78" y="112" width="44" height="14" rx="7" fill="#022c22" opacity="0.9"/>
                    <g transform="translate(100 122) scale(-1 1)">
                        <text text-anchor="middle" font-size="8" font-family="Arial, sans-serif" font-weight="700" fill="#f5e6a6">NELASKA</text>
                    </g>

                    {{-- Energy core --}}
                    <circle cx="100" cy="148" r="18" fill="url(#nelaskaCoreGlow)" class="robot-core-glow"/>
                    <circle cx="100" cy="148" r="12" fill="#022c22"/>
                    <circle cx="100" cy="148" r="7" class="robot-core" fill="#0a7a3e">
                        <animate attributeName="opacity" values="1;0.45;1" dur="1.8s" repeatCount="indefinite"/>
                        <animate attributeName="r" values="7;9.5;7" dur="1.8s" repeatCount="indefinite"/>
                    </circle>
                    <circle cx="100" cy="148" r="11" fill="none" stroke="#d4a017" stroke-width="1.5" opacity="0.85"/>
                </svg>
            </div>
        </button>
    </div>
@endif
