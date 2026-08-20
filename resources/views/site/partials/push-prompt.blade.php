<div
    x-data="pushPrompt(@js([
        'configUrl' => route('push.config'),
        'subscribeUrl' => route('push.subscribe'),
        'unsubscribeUrl' => route('push.unsubscribe'),
        'schoolName' => $site->school_name,
        'csrf' => csrf_token(),
    ]))"
    x-cloak
    x-show="visible"
    x-transition.opacity.duration.300ms
    class="push-prompt no-print"
    role="dialog"
    aria-live="polite"
    aria-label="Tawaran notifikasi"
>
    <div class="push-prompt-card">
        <div class="push-prompt-icon" aria-hidden="true">
            <i class="bi bi-bell-fill"></i>
        </div>
        <div class="push-prompt-body">
            <p class="push-prompt-title">Aktifkan notifikasi?</p>
            <p class="push-prompt-text">Dapatkan kabar berita & pengumuman baru dari {{ $site->school_name }} langsung di browser Anda.</p>
            <p x-show="error" x-text="error" class="push-prompt-error"></p>
        </div>
        <div class="push-prompt-actions">
            <button type="button" class="push-prompt-btn push-prompt-btn--primary" @click="subscribe()" :disabled="busy">
                <span x-show="!busy">Aktifkan</span>
                <span x-show="busy">Memproses…</span>
            </button>
            <button type="button" class="push-prompt-btn push-prompt-btn--ghost" @click="dismiss()" :disabled="busy">Nanti saja</button>
        </div>
    </div>
</div>
