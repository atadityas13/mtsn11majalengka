<div
    x-data="pushPrompt(@js([
        'configUrl' => route('push.config'),
        'subscribeUrl' => route('push.subscribe'),
        'unsubscribeUrl' => route('push.unsubscribe'),
        'schoolName' => $site->school_name,
        'csrf' => csrf_token(),
    ]))"
    class="no-print"
>
    {{-- Kartu tawaran awal --}}
    <div
        x-cloak
        x-show="visible && ! guiding"
        x-transition.opacity.duration.300ms
        class="push-prompt"
        role="dialog"
        aria-live="polite"
        aria-label="Ikuti pemberitahuan berita"
    >
        <div class="push-prompt-card">
            <div class="push-prompt-icon" aria-hidden="true">
                <i class="bi bi-newspaper"></i>
            </div>
            <div class="push-prompt-body">
                <p class="push-prompt-title">Ikuti kami</p>
                <p class="push-prompt-text">
                    Selalu update berita dan kabar terbaru dari kami.
                </p>
                <p x-show="error" x-text="error" class="push-prompt-error"></p>
            </div>
            <div class="push-prompt-actions">
                <button type="button" class="push-prompt-btn push-prompt-btn--primary" @click="subscribe()" :disabled="busy">
                    <span x-show="!busy">Ikuti</span>
                    <span x-show="busy">Menyiapkan…</span>
                </button>
                <button type="button" class="push-prompt-btn push-prompt-btn--ghost" @click="dismiss()" :disabled="busy">Lain kali</button>
            </div>
        </div>
    </div>

    {{-- Petunjuk ke dialog Izinkan/Allow bawaan browser --}}
    <div
        x-cloak
        x-show="guiding"
        x-transition.opacity.duration.200ms
        class="push-guide"
        role="dialog"
        aria-modal="true"
        aria-label="Petunjuk izinkan notifikasi"
    >
        <div class="push-guide-blur" aria-hidden="true"></div>

        <div class="push-guide-beam" aria-hidden="true"></div>

        <div class="push-guide-hint">
            <div class="push-guide-arrow" aria-hidden="true">
                <span class="push-guide-arrow-line"></span>
                <span class="push-guide-arrow-head">
                    <i class="bi bi-caret-up-fill"></i>
                </span>
            </div>
            <p class="push-guide-title">Satu langkah lagi</p>
            <p class="push-guide-text">
                Lihat ke <strong>atas browser</strong>, lalu klik
                <span class="push-guide-pill">Izinkan</span>
                atau
                <span class="push-guide-pill">Allow</span>
            </p>
            <p class="push-guide-note">Halaman diredupkan sementara sampai Anda memilih.</p>
        </div>
    </div>
</div>
