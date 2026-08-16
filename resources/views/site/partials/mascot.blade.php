@if ($site->mascotIsVisible())
    @php
        $customLines = collect(preg_split('/\r\n|\r|\n/', (string) $site->mascot_message))
            ->map(fn (string $line) => trim($line))
            ->filter()
            ->values()
            ->all();

        $hour = (int) now()->timezone(config('app.timezone', 'Asia/Jakarta'))->format('G');
        $timeHello = match (true) {
            $hour >= 4 && $hour < 11 => 'Selamat pagi!',
            $hour >= 11 && $hour < 15 => 'Selamat siang!',
            $hour >= 15 && $hour < 18 => 'Selamat sore!',
            default => 'Selamat malam!',
        };

        $school = $site->school_name ?: 'MTsN 11 Majalengka';
        $theme = $site->mascot_theme ?: 'default';

        $extraLines = match ($theme) {
            'hut_ri' => [
                'Dirgahayu Republik Indonesia! Merdeka!',
                'Mari jaga semangat kebangsaan di madrasah kita.',
            ],
            'ramadan' => [
                'Marhaban ya Ramadan. Semoga ibadah kita dimudahkan.',
                'Jaga silaturahmi dan semangat belajar di bulan penuh berkah.',
            ],
            default => [
                "Senang bertemu Anda di situs {$school}.",
                'Butuh info? Klik tombol WhatsApp di bawah ya!',
            ],
        };

        $scriptMessages = array_values(array_unique(array_filter([
            $timeHello.' Aku robot madrasah.',
            ...$customLines,
            ...$extraLines,
        ])));
    @endphp

    <div
        class="site-mascot no-print site-mascot--{{ $theme }}{{ $site->whatsappLink() ? ' site-mascot--above-wa' : '' }}"
        x-data="siteMascot(@js($scriptMessages), @js('site-mascot-dismissed-'.md5(($site->mascot_message ?? '').'|'.$theme.'|'.optional($site->mascot_ends_on)->toDateString())))"
        x-cloak
        x-show="!hidden"
        role="complementary"
        aria-label="Robot ucapan situs"
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
            <button type="button" class="site-mascot-dismiss" @click="dismiss()" aria-label="Sembunyikan robot">×</button>
            <p x-text="displayText" class="min-h-[2.5em]"></p>
            <div class="site-mascot-hint" x-show="!busy">Klik robot untuk bicara lagi</div>
        </div>

        <button
            type="button"
            class="site-mascot-robot"
            @click="onTap()"
            :aria-label="talking ? 'Robot sedang berbicara' : 'Ajak robot berbicara'"
        >
            <div class="robot-root-masterpiece">
                <svg viewBox="0 0 200 200" class="h-full w-full">
                    <defs>
                        <linearGradient id="siteMascotGradMetallic" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#ffffff;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#94a3b8;stop-opacity:1" />
                        </linearGradient>
                        <radialGradient id="siteMascotEyeGlow" cx="50%" cy="50%" r="50%">
                            <stop offset="0%" style="stop-color:#38bdf8;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#0ea5e9;stop-opacity:0" />
                        </radialGradient>
                        <filter id="siteMascotSoftShadow" x="-20%" y="-20%" width="140%" height="140%">
                            <feGaussianBlur in="SourceAlpha" stdDeviation="3" />
                            <feOffset dx="2" dy="2" result="offsetblur" />
                            <feComponentTransfer><feFuncA type="linear" slope="0.3"/></feComponentTransfer>
                            <feMerge><feMergeNode/><feMergeNode in="SourceGraphic"/></feMerge>
                        </filter>
                    </defs>
                    <path d="M60,65 Q60,30 100,30 Q140,30 140,65 Q140,90 125,100 Q150,110 150,140 Q150,170 100,170 Q50,170 50,140 Q50,110 75,100 Q60,90 60,65"
                          fill="url(#siteMascotGradMetallic)" stroke="#1e293b" stroke-width="1.5" filter="url(#siteMascotSoftShadow)" />
                    <rect x="72" y="55" width="56" height="32" rx="14" fill="#0f172a" opacity="0.95"/>
                    <g class="robot-eye" style="transform-origin: 86px 71px;">
                        <circle cx="86" cy="71" r="7" fill="#38bdf8" />
                        <circle cx="86" cy="71" r="12" fill="url(#siteMascotEyeGlow)" />
                        <circle class="robot-pupil" cx="88" cy="72" r="2.5" fill="#0f172a" />
                    </g>
                    <g class="robot-eye" style="transform-origin: 114px 71px;">
                        <circle cx="114" cy="71" r="7" fill="#38bdf8" />
                        <circle cx="114" cy="71" r="12" fill="url(#siteMascotEyeGlow)" />
                        <circle class="robot-pupil" cx="116" cy="72" r="2.5" fill="#0f172a" />
                    </g>
                    <rect x="88" y="90" width="24" height="3" rx="1.5" fill="#6366f1" class="robot-mouth" style="transform-origin: center;"/>
                    <circle cx="100" cy="135" r="15" fill="#0f172a"/>
                    <circle cx="100" cy="135" r="8" class="robot-core" fill="#0a7a3e">
                        <animate attributeName="opacity" values="1;0.4;1" dur="2s" repeatCount="indefinite" />
                        <animate attributeName="r" values="8;11;8" dur="2s" repeatCount="indefinite" />
                    </circle>
                    <path d="M70,170 Q100,195 130,170" fill="#64748b" class="robot-hover-pod" />
                    <rect x="85" y="188" width="30" height="5" rx="2.5" fill="#38bdf8" class="robot-hover-pod" opacity="0.7"/>
                    <circle cx="60" cy="118" r="8" fill="url(#siteMascotGradMetallic)" stroke="#1e293b" stroke-width="1.5" />
                    <circle cx="138" cy="118" r="8" fill="url(#siteMascotGradMetallic)" stroke="#1e293b" stroke-width="1.5" />
                    <g style="transform-origin: 138px 118px;">
                        <path d="M138,118 Q165,115 175,90" fill="none" stroke="#cbd5e1" stroke-width="12" stroke-linecap="round"/>
                        <circle cx="175" cy="90" r="8" fill="#ffffff" stroke="#1e293b" stroke-width="2"/>
                    </g>
                    <g class="robot-arm" style="transform-origin: 60px 118px;">
                        <path d="M60,118 Q35,115 25,90" fill="none" stroke="#cbd5e1" stroke-width="12" stroke-linecap="round"/>
                        <circle cx="25" cy="90" r="8" fill="#ffffff" stroke="#1e293b" stroke-width="2"/>
                    </g>
                </svg>
            </div>
        </button>
    </div>
@endif
