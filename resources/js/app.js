import Alpine from 'alpinejs';

document.addEventListener('alpine:init', () => {
    Alpine.data('pushPrompt', (config = {}) => ({
        visible: false,
        guiding: false,
        guideBottom: false,
        busy: false,
        error: '',
        publicKey: null,
        registration: null,
        configUrl: config.configUrl,
        subscribeUrl: config.subscribeUrl,
        unsubscribeUrl: config.unsubscribeUrl,
        csrf: config.csrf || document.querySelector('meta[name="csrf-token"]')?.content || '',

        detectGuidePlacement() {
            // Chrome desktop & Android modern: dialog izin di atas (dekat address bar).
            // Arahkan panah ke atas di semua perangkat.
            this.guideBottom = false;
        },

        async init() {
            if (! ('Notification' in window) || ! ('serviceWorker' in navigator) || ! ('PushManager' in window)) {
                return;
            }

            if (sessionStorage.getItem('push_prompt_dismissed') === '1') {
                return;
            }

            if (Notification.permission === 'denied') {
                return;
            }

            try {
                const res = await fetch(this.configUrl, { headers: { Accept: 'application/json' } });
                const data = await res.json();
                if (! data.enabled || ! data.publicKey) {
                    return;
                }
                this.publicKey = data.publicKey;
            } catch (e) {
                return;
            }

            this.registration = await navigator.serviceWorker.register('/sw.js', { scope: '/' });
            await navigator.serviceWorker.ready;

            const existing = await this.registration.pushManager.getSubscription();
            if (existing) {
                try {
                    await this.persistSubscription(existing);
                } catch (e) {
                    // ignore
                }
                return;
            }

            setTimeout(() => {
                this.visible = true;
            }, 1800);
        },

        dismiss() {
            this.visible = false;
            this.guiding = false;
            this.unlockPage();
            sessionStorage.setItem('push_prompt_dismissed', '1');
        },

        lockPage() {
            document.documentElement.classList.add('push-guide-active');
            document.body.classList.add('push-guide-active');
        },

        unlockPage() {
            document.documentElement.classList.remove('push-guide-active');
            document.body.classList.remove('push-guide-active');
        },

        async subscribe() {
            this.busy = true;
            this.error = '';

            try {
                if (! this.registration) {
                    this.registration = await navigator.serviceWorker.register('/sw.js', { scope: '/' });
                    await navigator.serviceWorker.ready;
                }

                // Jika belum pernah diminta, tampilkan petunjuk + blur sambil dialog browser muncul.
                if (Notification.permission === 'default') {
                    this.detectGuidePlacement();
                    this.guiding = true;
                    this.lockPage();
                    // Biarkan overlay sempat tampil sebelum dialog sistem.
                    await new Promise((resolve) => setTimeout(resolve, 280));
                }

                const permission = await Notification.requestPermission();

                this.guiding = false;
                this.unlockPage();

                if (permission !== 'granted') {
                    this.visible = true;
                    this.error = permission === 'denied'
                        ? 'Izin notifikasi diblokir di browser. Aktifkan lewat pengaturan situs.'
                        : 'Izin belum diberikan. Silakan coba lagi dan pilih Izinkan/Allow.';
                    this.busy = false;
                    return;
                }

                const subscription = await this.registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: this.urlBase64ToUint8Array(this.publicKey),
                });

                await this.persistSubscription(subscription);
                this.visible = false;
                sessionStorage.setItem('push_prompt_dismissed', '1');
            } catch (e) {
                this.guiding = false;
                this.unlockPage();
                this.visible = true;
                this.error = 'Gagal mengaktifkan notifikasi. Coba refresh halaman.';
            } finally {
                this.busy = false;
            }
        },

        async persistSubscription(subscription) {
            const payload = subscription.toJSON();
            const res = await fetch(this.subscribeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': this.csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(payload),
            });

            if (! res.ok) {
                throw new Error('subscribe failed');
            }
        },

        urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
            const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
            const raw = window.atob(base64);
            const output = new Uint8Array(raw.length);
            for (let i = 0; i < raw.length; i++) {
                output[i] = raw.charCodeAt(i);
            }
            return output;
        },
    }));

    Alpine.directive('reveal', (el, { expression }, { cleanup }) => {
        el.classList.add(expression || 'reveal');

        const markVisible = (target) => {
            target.classList.add('is-visible');
            target.querySelectorAll('.reveal, .reveal-left, .reveal-scale').forEach((child) => {
                child.classList.add('is-visible');
            });
        };

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        markVisible(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.14, rootMargin: '0px 0px -40px 0px' }
        );

        observer.observe(el);
        cleanup(() => observer.disconnect());
    });

    Alpine.data('siteMascot', (messages = []) => ({
        messages: Array.isArray(messages) && messages.length ? messages : ['Halo!'],
        visible: true,
        talking: false,
        listening: false,
        busy: false,
        displayText: '',
        fullText: '',
        messageIndex: 0,
        typingTimer: null,
        sequenceTimer: null,
        hideTimer: null,
        idleTimer: null,
        speakToken: 0,
        dismissing: false,
        reduceMotion: window.matchMedia('(prefers-reduced-motion: reduce)').matches,

        get pendingText() {
            return this.fullText.slice(this.displayText.length);
        },

        init() {
            setTimeout(() => this.playSequence(), 900);
            this.idleTimer = setInterval(() => {
                if (this.visible && ! this.busy && ! this.dismissing && Math.random() > 0.55) {
                    this.speakOne(this.randomMessage());
                }
            }, 22000);
        },

        destroy() {
            this.speakToken++;
            clearTimeout(this.typingTimer);
            clearTimeout(this.sequenceTimer);
            clearTimeout(this.hideTimer);
            clearInterval(this.idleTimer);
        },

        randomMessage() {
            return this.messages[Math.floor(Math.random() * this.messages.length)];
        },

        wait(ms, token) {
            return new Promise((resolve) => {
                this.sequenceTimer = setTimeout(() => {
                    resolve(token === this.speakToken);
                }, ms);
            });
        },

        charDelay(ch, next) {
            if (/[.!?]/.test(ch)) {
                return 140;
            }
            if (/[,;:]/.test(ch)) {
                return 90;
            }
            if (ch === ' ') {
                return 42;
            }
            if (next === ' ') {
                return 30;
            }
            return 22;
        },

        typeWriter(text, token) {
            clearTimeout(this.typingTimer);
            this.fullText = text;
            this.displayText = '';

            if (this.reduceMotion) {
                this.displayText = text;
                return Promise.resolve(token === this.speakToken);
            }

            return new Promise((resolve) => {
                let i = 0;

                const tick = () => {
                    if (token !== this.speakToken) {
                        resolve(false);
                        return;
                    }

                    if (i >= text.length) {
                        resolve(true);
                        return;
                    }

                    const ch = text.charAt(i);
                    this.displayText += ch;
                    i++;
                    this.typingTimer = setTimeout(
                        tick,
                        this.charDelay(ch, text.charAt(i)),
                    );
                };

                tick();
            });
        },

        readPause(text) {
            const words = text.trim().split(/\s+/).filter(Boolean).length;
            return Math.min(4200, 1400 + words * 260);
        },

        clearSpeech() {
            this.talking = false;
            this.displayText = '';
            this.fullText = '';
            this.busy = false;
        },

        async speakOne(text) {
            if (this.busy || ! this.visible || this.dismissing) {
                return;
            }

            const token = ++this.speakToken;
            this.busy = true;
            this.talking = true;
            this.listening = false;

            const typed = await this.typeWriter(text, token);
            if (! typed) {
                return;
            }

            const still = await this.wait(this.readPause(text), token);
            if (! still) {
                return;
            }

            this.clearSpeech();
        },

        async playSequence() {
            if (this.busy || ! this.visible || this.dismissing) {
                return;
            }

            const token = ++this.speakToken;
            this.busy = true;
            this.talking = true;
            this.listening = false;

            for (let i = 0; i < this.messages.length; i++) {
                if (token !== this.speakToken || ! this.visible || this.dismissing) {
                    return;
                }

                this.messageIndex = i;
                const text = this.messages[i];
                const typed = await this.typeWriter(text, token);
                if (! typed) {
                    return;
                }

                const still = await this.wait(this.readPause(text), token);
                if (! still) {
                    return;
                }

                if (i < this.messages.length - 1) {
                    this.displayText = '';
                    this.fullText = '';
                    const gap = await this.wait(320, token);
                    if (! gap) {
                        return;
                    }
                }
            }

            if (token !== this.speakToken) {
                return;
            }

            this.clearSpeech();
        },

        async onTap() {
            if (! this.visible || this.dismissing) {
                return;
            }

            this.dismissing = true;
            clearInterval(this.idleTimer);
            this.idleTimer = null;
            clearTimeout(this.hideTimer);

            const token = ++this.speakToken;
            clearTimeout(this.typingTimer);
            clearTimeout(this.sequenceTimer);

            this.busy = true;
            this.talking = true;
            this.listening = false;

            const bye = 'Dadah, sampai jumpa lagi!';
            const typed = await this.typeWriter(bye, token);
            if (typed) {
                await this.wait(Math.min(2200, this.readPause(bye)), token);
            }

            this.visible = false;
            setTimeout(() => {
                this.talking = false;
                this.displayText = '';
                this.fullText = '';
                this.busy = false;
            }, 520);
        },
    }));
});

window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    const header = document.querySelector('[data-site-header]');
    if (header) {
        const onScroll = () => {
            header.classList.toggle('is-scrolled', window.scrollY > 12);
        };

        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
    }

    document.querySelectorAll('[data-copy-link]').forEach((button) => {
        button.addEventListener('click', async () => {
            const url = button.getAttribute('data-url') || window.location.href;
            const hasIcon = Boolean(button.querySelector('img, svg'));
            const label = button.textContent;

            try {
                await navigator.clipboard.writeText(url);
                button.classList.add('is-copied');
                if (!hasIcon) {
                    button.textContent = 'Tersalin';
                } else {
                    button.setAttribute('title', 'Tersalin');
                    button.setAttribute('aria-label', 'Tautan tersalin');
                }
                window.setTimeout(() => {
                    button.classList.remove('is-copied');
                    if (!hasIcon) {
                        button.textContent = label;
                    } else {
                        button.setAttribute('title', 'Salin tautan');
                        button.setAttribute('aria-label', 'Salin tautan');
                    }
                }, 1800);
            } catch (error) {
                window.prompt('Salin tautan ini:', url);
            }
        });
    });

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const heroStage = document.querySelector('.hero-stage');
    const heroMedia = document.querySelector('[data-hero-parallax]');
    if (heroStage && heroMedia && !reduceMotion) {
        let ticking = false;
        const updateParallax = () => {
            const max = heroStage.offsetHeight;
            const y = Math.min(window.scrollY, max);
            heroMedia.style.transform = `translate3d(0, ${y * 0.32}px, 0)`;
            ticking = false;
        };
        window.addEventListener(
            'scroll',
            () => {
                if (!ticking) {
                    window.requestAnimationFrame(updateParallax);
                    ticking = true;
                }
            },
            { passive: true }
        );
        updateParallax();
    }

    document.querySelectorAll('[data-count-up]').forEach((el) => {
        const target = Number(el.dataset.countUp || 0);
        const suffix = el.dataset.countSuffix || '';
        if (!Number.isFinite(target) || target <= 0) {
            return;
        }

        const format = (value) => String(value) + suffix;

        const run = () => {
            if (reduceMotion) {
                el.textContent = format(target);
                return;
            }
            const duration = 3200;
            const start = performance.now();
            const tick = (now) => {
                const progress = Math.min((now - start) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 2.2);
                el.textContent = format(Math.round(target * eased));
                if (progress < 1) {
                    requestAnimationFrame(tick);
                }
            };
            requestAnimationFrame(tick);
        };

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        run();
                        observer.unobserve(el);
                    }
                });
            },
            { threshold: 0.45 }
        );
        observer.observe(el);
    });

    document.querySelectorAll('[data-news-slider]').forEach((root) => {
        const slides = [...root.querySelectorAll('[data-slide]')];
        const dots = [...root.querySelectorAll('[data-slider-dot]')];
        const items = [...root.querySelectorAll('[data-slider-item]')];
        const total = slides.length;

        if (total < 2) {
            return;
        }

        let active = 0;
        let timer = null;
        const interval = Number(root.dataset.interval || 4000);
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        const paint = (index) => {
            active = index;

            slides.forEach((slide, i) => {
                const on = i === active;
                slide.classList.toggle('is-active', on);
                slide.classList.toggle('opacity-100', on);
                slide.classList.toggle('z-[1]', on);
                slide.classList.toggle('opacity-0', !on);
                slide.classList.toggle('pointer-events-none', !on);
                slide.classList.toggle('z-0', !on);
            });

            dots.forEach((dot, i) => {
                const on = i === active;
                dot.classList.toggle('bg-gold', on);
                dot.classList.toggle('w-5', on);
                dot.classList.toggle('bg-white/40', !on);
                dot.classList.toggle('w-2', !on);
            });

            items.forEach((item, i) => {
                const on = i === active;
                item.classList.toggle('bg-kemenag-soft/80', on);
                const title = item.querySelector('.news-list-title');
                if (title) {
                    title.classList.toggle('text-kemenag', on);
                }
            });
        };

        const stop = () => {
            if (timer) {
                clearInterval(timer);
                timer = null;
            }
        };

        const start = () => {
            stop();
            if (reduceMotion) {
                return;
            }
            timer = setInterval(() => {
                paint((active + 1) % total);
            }, interval);
        };

        const go = (index) => {
            paint(index);
            start();
        };

        root.querySelector('[data-slider-prev]')?.addEventListener('click', () => {
            go((active - 1 + total) % total);
        });

        root.querySelector('[data-slider-next]')?.addEventListener('click', () => {
            go((active + 1) % total);
        });

        dots.forEach((dot) => {
            dot.addEventListener('click', () => go(Number(dot.dataset.sliderDot)));
        });

        items.forEach((item) => {
            item.addEventListener('click', (event) => {
                if (event.target.closest('[data-slider-link]')) {
                    return;
                }
                go(Number(item.dataset.sliderItem));
            });
        });

        root.addEventListener('mouseenter', stop);
        root.addEventListener('mouseleave', start);

        paint(0);
        start();
    });

    const shortFeed = document.querySelector('[data-short-feed]');
    if (shortFeed) {
        const slides = [...shortFeed.querySelectorAll('[data-short-slide]')];
        let soundOn = false;
        let ytApiPromise = null;

        const tiktokMessage = (iframe, type, value = null) => {
            if (!iframe?.contentWindow) {
                return;
            }
            iframe.contentWindow.postMessage(
                {
                    'x-tiktok-player': true,
                    type,
                    value,
                },
                '*'
            );
        };

        const loadYouTubeApi = () => {
            if (window.YT?.Player) {
                return Promise.resolve(window.YT);
            }

            if (ytApiPromise) {
                return ytApiPromise;
            }

            ytApiPromise = new Promise((resolve) => {
                const previous = window.onYouTubeIframeAPIReady;
                window.onYouTubeIframeAPIReady = () => {
                    if (typeof previous === 'function') {
                        previous();
                    }
                    resolve(window.YT);
                };

                if (!document.querySelector('script[data-youtube-api]')) {
                    const tag = document.createElement('script');
                    tag.src = 'https://www.youtube.com/iframe_api';
                    tag.async = true;
                    tag.dataset.youtubeApi = '1';
                    document.head.appendChild(tag);
                }
            });

            return ytApiPromise;
        };

        const setMuteUi = (slide, muted) => {
            const btn = slide.querySelector('[data-short-mute]');
            if (!btn) {
                return;
            }
            btn.classList.toggle('is-muted', muted);
            btn.setAttribute('aria-label', muted ? 'Nyalakan suara' : 'Matikan suara');
        };

        const setPausedUi = (slide, paused) => {
            slide.querySelector('[data-short-play]')?.classList.toggle('is-visible', paused);
        };

        const setInteractive = (slide, on) => {
            slide.classList.toggle('is-interactive', on);
            const layer = slide.querySelector('[data-short-scroll-layer]');
            if (layer) {
                layer.style.display = on ? 'none' : '';
            }
        };

        const getController = (slide) => slide._shortController || null;

        const unload = (slide) => {
            if (slide._tiktokReadyHandler) {
                window.removeEventListener('message', slide._tiktokReadyHandler);
                slide._tiktokReadyHandler = null;
            }

            const controller = getController(slide);
            if (controller?.destroy) {
                try {
                    controller.destroy();
                } catch {
                    // ignore destroy races
                }
            }
            slide._shortController = null;

            const player = slide.querySelector('[data-short-player]');
            const poster = slide.querySelector('[data-short-poster]');
            if (player) {
                player.innerHTML = '';
            }
            poster?.classList.remove('is-hidden');
            slide.classList.remove(
                'is-playing',
                'is-interactive',
                'platform-youtube',
                'platform-tiktok',
                'platform-instagram'
            );
            setInteractive(slide, false);
            setPausedUi(slide, false);
            setMuteUi(slide, !soundOn);
        };

        const applySound = (slide) => {
            const platform = slide.dataset.platform || 'youtube';
            const controller = getController(slide);

            if (platform === 'tiktok') {
                const iframe = slide.querySelector('[data-short-player] iframe');
                if (iframe) {
                    if (soundOn) {
                        // Harus synchronous di click handler agar browser izinkan suara.
                        tiktokMessage(iframe, 'unMute');
                        tiktokMessage(iframe, 'play');
                    } else {
                        tiktokMessage(iframe, 'mute');
                    }
                }
                setMuteUi(slide, !soundOn);
                return;
            }

            if (!controller) {
                setMuteUi(slide, !soundOn);
                return;
            }

            if (soundOn) {
                controller.unMute?.();
                controller.setVolume?.(100);
            } else {
                controller.mute?.();
            }
            setMuteUi(slide, !soundOn);
        };

        const togglePause = (slide) => {
            const controller = getController(slide);
            if (!controller?.togglePause) {
                return;
            }
            const paused = controller.togglePause();
            setPausedUi(slide, paused);
        };

        const createEmbedIframe = (platform, src) => {
            const iframe = document.createElement('iframe');
            iframe.src = src;
            iframe.title = 'Short video';
            iframe.allow =
                'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
            iframe.allowFullscreen = true;
            iframe.referrerPolicy = 'strict-origin-when-cross-origin';
            iframe.setAttribute('playsinline', 'true');
            iframe.setAttribute('allowfullscreen', 'true');
            iframe.className = `short-iframe is-${platform}`;
            return iframe;
        };

        const loadTikTok = (slide) => {
            const embed = slide.dataset.embed;
            const player = slide.querySelector('[data-short-player]');
            if (!embed || !player) {
                return;
            }

            player.innerHTML = '';
            const iframe = createEmbedIframe('tiktok', embed);
            player.appendChild(iframe);

            const onReady = (event) => {
                if (event.origin !== 'https://www.tiktok.com') {
                    return;
                }
                const data = event.data;
                if (!data || data['x-tiktok-player'] !== true) {
                    return;
                }
                if (data.type === 'onPlayerReady') {
                    if (!soundOn) {
                        tiktokMessage(iframe, 'mute');
                    } else {
                        tiktokMessage(iframe, 'unMute');
                    }
                    tiktokMessage(iframe, 'play');
                    setMuteUi(slide, !soundOn);
                }
            };

            slide._tiktokReadyHandler = onReady;
            window.addEventListener('message', onReady);

            slide._shortController = {
                mute: () => tiktokMessage(iframe, 'mute'),
                unMute: () => {
                    tiktokMessage(iframe, 'unMute');
                    tiktokMessage(iframe, 'play');
                },
                togglePause: () => false,
                destroy: () => {
                    window.removeEventListener('message', onReady);
                    player.innerHTML = '';
                },
            };

            // Fallback jika onPlayerReady terlambat/tidak datang.
            window.setTimeout(() => {
                if (!soundOn) {
                    tiktokMessage(iframe, 'mute');
                }
            }, 800);
        };

        const loadInstagram = (slide) => {
            const embed = slide.dataset.embed;
            const external = slide.dataset.externalUrl;
            const player = slide.querySelector('[data-short-player]');
            const poster = slide.querySelector('[data-short-poster]');
            if (!player) {
                return;
            }

            player.innerHTML = '';

            if (!embed) {
                const box = document.createElement('div');
                box.className = 'short-external-fallback';
                box.innerHTML = `
                    <p>URL Instagram belum bisa dibaca.</p>
                    ${external ? `<a href="${external}" target="_blank" rel="noopener noreferrer">Buka di Instagram</a>` : ''}
                `;
                player.appendChild(box);
                poster?.classList.add('is-hidden');
                return;
            }

            // Kembalikan embed interaktif (bisa di-play), scroll via strip atas/bawah + tombol next.
            const iframe = createEmbedIframe('instagram', embed);
            player.appendChild(iframe);
            poster?.classList.add('is-hidden');

            slide._shortController = {
                destroy: () => {
                    player.innerHTML = '';
                },
            };
        };

        const loadYouTube = async (slide) => {
            const videoId = slide.dataset.youtubeId;
            const playerHost = slide.querySelector('[data-short-player]');
            if (!videoId || !playerHost) {
                return;
            }

            const YT = await loadYouTubeApi();
            const mount = document.createElement('div');
            playerHost.innerHTML = '';
            playerHost.appendChild(mount);

            await new Promise((resolve) => {
                const player = new YT.Player(mount, {
                    videoId,
                    width: '100%',
                    height: '100%',
                    playerVars: {
                        autoplay: 1,
                        mute: 1,
                        controls: 0,
                        rel: 0,
                        modestbranding: 1,
                        playsinline: 1,
                        fs: 0,
                        iv_load_policy: 3,
                        disablekb: 1,
                        loop: 1,
                        playlist: videoId,
                        origin: window.location.origin,
                    },
                    events: {
                        onReady: (event) => {
                            slide._shortController = {
                                mute: () => event.target.mute(),
                                unMute: () => event.target.unMute(),
                                setVolume: (value) => event.target.setVolume(value),
                                togglePause: () => {
                                    const state = event.target.getPlayerState();
                                    if (state === YT.PlayerState.PLAYING) {
                                        event.target.pauseVideo();
                                        return true;
                                    }
                                    event.target.playVideo();
                                    return false;
                                },
                                destroy: () => {
                                    try {
                                        event.target.destroy();
                                    } catch {
                                        // ignore
                                    }
                                },
                            };

                            event.target.playVideo();
                            applySound(slide);
                            setPausedUi(slide, false);
                            resolve();
                        },
                        onStateChange: (event) => {
                            if (event.data === YT.PlayerState.PAUSED) {
                                setPausedUi(slide, true);
                            }
                            if (event.data === YT.PlayerState.PLAYING) {
                                setPausedUi(slide, false);
                            }
                        },
                        onError: () => resolve(),
                    },
                });

                slide._shortController = {
                    destroy: () => {
                        try {
                            player.destroy();
                        } catch {
                            // ignore
                        }
                    },
                };
            });
        };

        const load = async (slide) => {
            const platform = slide.dataset.platform || 'youtube';
            const poster = slide.querySelector('[data-short-poster]');

            unload(slide);
            slide.classList.add(`platform-${platform}`);
            slide.classList.add('is-playing');
            setMuteUi(slide, !soundOn);

            if (platform === 'youtube' && slide.dataset.youtubeId) {
                poster?.classList.add('is-hidden');
                await loadYouTube(slide);
                return;
            }

            if (platform === 'tiktok') {
                poster?.classList.add('is-hidden');
                loadTikTok(slide);
                return;
            }

            if (platform === 'instagram') {
                loadInstagram(slide);
                return;
            }
        };

        const goNext = (slide) => {
            const index = slides.indexOf(slide);
            const next = slides[index + 1];
            if (!next) {
                return;
            }
            next.scrollIntoView({ behavior: 'smooth', block: 'start' });
        };

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    const slide = entry.target;
                    if (entry.isIntersecting && entry.intersectionRatio > 0.65) {
                        slides.forEach((other) => {
                            if (other !== slide) {
                                unload(other);
                            }
                        });
                        load(slide);
                    } else if (!entry.isIntersecting) {
                        unload(slide);
                    }
                });
            },
            { threshold: [0.65, 0.9] }
        );

        slides.forEach((slide) => {
            observer.observe(slide);

            slide.querySelector('[data-short-hit]')?.addEventListener('click', () => {
                togglePause(slide);
            });

            slide.querySelector('[data-short-mute]')?.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                soundOn = !soundOn;
                applySound(slide);
            });

            slide.querySelector('[data-short-next]')?.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                goNext(slide);
            });
        });
    }

    const setupLayananMarquee = () => {
        const root = document.querySelector('[data-layanan-marquee]');
        if (!root) {
            return;
        }

        const viewport = root.querySelector('.layanan-apps-viewport');
        const rail = root.querySelector('.layanan-apps-rail');
        const group = root.querySelector('[data-layanan-group]');
        if (!viewport || !rail || !group) {
            return;
        }

        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const sourceHtml = group.innerHTML;

        const makeGroup = (attrs = {}) => {
            const node = document.createElement('div');
            node.className = 'layanan-apps-group';
            node.innerHTML = sourceHtml;
            Object.entries(attrs).forEach(([key, value]) => node.setAttribute(key, value));
            if (attrs['aria-hidden'] === 'true') {
                node.querySelectorAll('a').forEach((link) => link.setAttribute('tabindex', '-1'));
            }
            return node;
        };

        const sync = () => {
            rail.innerHTML = '';
            root.classList.remove('is-marquee');

            if (reduceMotion) {
                rail.appendChild(makeGroup({ 'data-layanan-group': 'true' }));
                return;
            }

            // Satu set diisi sampai setidaknya selebar viewport (supaya tetap running meski item sedikit)
            const set = document.createElement('div');
            set.className = 'layanan-apps-set';
            set.setAttribute('data-layanan-set', 'true');
            set.style.display = 'flex';
            set.style.flexWrap = 'nowrap';
            set.style.alignItems = 'center';

            set.appendChild(makeGroup({ 'data-layanan-group': 'true' }));
            rail.appendChild(set);

            let guard = 0;
            while (set.scrollWidth < viewport.clientWidth && guard < 12) {
                set.appendChild(
                    makeGroup({
                        'data-layanan-pad': 'true',
                        'aria-hidden': 'true',
                    })
                );
                guard += 1;
            }

            // Duplikat set untuk loop seamless (-50%)
            const clone = set.cloneNode(true);
            clone.setAttribute('data-layanan-clone', 'true');
            clone.setAttribute('aria-hidden', 'true');
            clone.querySelectorAll('a').forEach((link) => link.setAttribute('tabindex', '-1'));
            rail.appendChild(clone);

            root.classList.add('is-marquee');
        };

        sync();
        window.addEventListener('resize', () => {
            window.requestAnimationFrame(sync);
        });
    };

    setupLayananMarquee();
});
