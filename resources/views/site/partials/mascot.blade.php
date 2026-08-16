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
            <span class="site-mascot-glow" aria-hidden="true"></span>
            <div class="robot-root" aria-hidden="true">
                <svg viewBox="0 0 200 210" class="h-full w-full">
                    <defs>
                        <linearGradient id="nelBody" x1="15%" y1="5%" x2="90%" y2="95%">
                            <stop offset="0%" stop-color="#ffffff"/>
                            <stop offset="45%" stop-color="#e2e8f0"/>
                            <stop offset="100%" stop-color="#94a3b8"/>
                        </linearGradient>
                        <linearGradient id="nelAccent" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#34d399"/>
                            <stop offset="100%" stop-color="#0a7a3e"/>
                        </linearGradient>
                        <linearGradient id="nelGold" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#f8e7a0"/>
                            <stop offset="100%" stop-color="#d4a017"/>
                        </linearGradient>
                        <radialGradient id="nelEyeGlow" cx="50%" cy="45%" r="55%">
                            <stop offset="0%" stop-color="#7dd3fc"/>
                            <stop offset="60%" stop-color="#22d3ee"/>
                            <stop offset="100%" stop-color="#0ea5e9" stop-opacity="0"/>
                        </radialGradient>
                        <filter id="nelShadow" x="-20%" y="-20%" width="140%" height="140%">
                            <feGaussianBlur in="SourceAlpha" stdDeviation="2.8"/>
                            <feOffset dx="1" dy="3" result="o"/>
                            <feComponentTransfer><feFuncA type="linear" slope="0.32"/></feComponentTransfer>
                            <feMerge><feMergeNode/><feMergeNode in="SourceGraphic"/></feMerge>
                        </filter>
                    </defs>

                    {{-- Hover pad --}}
                    <ellipse class="robot-pad" cx="100" cy="198" rx="38" ry="6" fill="#0a7a3e" opacity="0.16"/>
                    <ellipse class="robot-pad" cx="100" cy="196" rx="24" ry="3.5" fill="#38bdf8" opacity="0.28"/>

                    {{-- Antenna --}}
                    <path d="M100,36 L100,16" stroke="#64748b" stroke-width="3" stroke-linecap="round"/>
                    <circle class="robot-antenna" cx="100" cy="12" r="5.5" fill="url(#nelGold)"/>
                    <circle class="robot-antenna" cx="100" cy="12" r="9" fill="#d4a017" opacity="0.22"/>

                    {{-- Head --}}
                    <path d="M68,58 Q68,30 100,28 Q132,30 132,58 Q132,78 118,86 Q132,90 132,108 Q132,128 100,130 Q68,128 68,108 Q68,90 82,86 Q68,78 68,58 Z"
                          fill="url(#nelBody)" stroke="#1e293b" stroke-width="1.5" filter="url(#nelShadow)"/>

                    {{-- Visor --}}
                    <rect x="78" y="52" width="44" height="28" rx="12" fill="#0f172a" opacity="0.96"/>
                    <rect x="82" y="55" width="36" height="6" rx="3" fill="#34d399" opacity="0.12"/>

                    <g class="robot-eye" style="transform-origin: 90px 67px;">
                        <circle cx="90" cy="67" r="6.5" fill="#67e8f9"/>
                        <circle cx="90" cy="67" r="11" fill="url(#nelEyeGlow)"/>
                        <circle class="robot-pupil" cx="91.5" cy="68" r="2.2" fill="#022c22"/>
                    </g>
                    <g class="robot-eye" style="transform-origin: 110px 67px;">
                        <circle cx="110" cy="67" r="6.5" fill="#67e8f9"/>
                        <circle cx="110" cy="67" r="11" fill="url(#nelEyeGlow)"/>
                        <circle class="robot-pupil" cx="111.5" cy="68" r="2.2" fill="#022c22"/>
                    </g>

                    <rect class="robot-mouth" x="90" y="88" width="20" height="3.5" rx="1.8" fill="#34d399" style="transform-origin: 100px 90px;"/>

                    {{-- Neck --}}
                    <rect x="92" y="128" width="16" height="10" rx="3" fill="#94a3b8" stroke="#1e293b" stroke-width="1"/>

                    {{-- Torso --}}
                    <path d="M74,138 Q74,134 100,134 Q126,134 126,138 L122,168 Q122,176 100,178 Q78,176 78,168 Z"
                          fill="url(#nelBody)" stroke="#1e293b" stroke-width="1.5" filter="url(#nelShadow)"/>
                    <path d="M84,142 L116,142 L114,158 Q114,162 100,163 Q86,162 86,158 Z" fill="url(#nelAccent)" opacity="0.92"/>

                    {{-- Chest core --}}
                    <circle cx="100" cy="152" r="10" fill="#022c22"/>
                    <circle class="robot-core" cx="100" cy="152" r="6" fill="#0a7a3e">
                        <animate attributeName="opacity" values="1;0.45;1" dur="1.7s" repeatCount="indefinite"/>
                        <animate attributeName="r" values="6;8;6" dur="1.7s" repeatCount="indefinite"/>
                    </circle>
                    <circle cx="100" cy="152" r="9" fill="none" stroke="url(#nelGold)" stroke-width="1.4"/>

                    {{-- Shoulders --}}
                    <circle cx="72" cy="142" r="8" fill="url(#nelBody)" stroke="#1e293b" stroke-width="1.3"/>
                    <circle cx="128" cy="142" r="8" fill="url(#nelBody)" stroke="#1e293b" stroke-width="1.3"/>
                    <circle class="robot-led" cx="72" cy="142" r="2.8" fill="#22d3ee"/>
                    <circle class="robot-led" cx="128" cy="142" r="2.8" fill="#22d3ee"/>

                    {{-- Arms --}}
                    <g style="transform-origin: 128px 142px;">
                        <path d="M128,142 Q152,138 160,118" fill="none" stroke="#cbd5e1" stroke-width="10" stroke-linecap="round"/>
                        <circle cx="160" cy="118" r="7" fill="#fff" stroke="#1e293b" stroke-width="1.5"/>
                    </g>
                    <g class="robot-arm" style="transform-origin: 72px 142px;">
                        <path d="M72,142 Q48,138 40,118" fill="none" stroke="#cbd5e1" stroke-width="10" stroke-linecap="round"/>
                        <circle cx="40" cy="118" r="7" fill="#fff" stroke="#1e293b" stroke-width="1.5"/>
                    </g>
                </svg>
            </div>
        </button>
    </div>
@endif
