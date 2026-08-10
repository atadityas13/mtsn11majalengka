import Alpine from 'alpinejs';

document.addEventListener('alpine:init', () => {
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

        const setMuteUi = (slide, muted) => {
            const btn = slide.querySelector('[data-short-mute]');
            const icon = slide.querySelector('[data-mute-icon]');
            if (!btn || !icon) {
                return;
            }
            btn.classList.toggle('is-muted', muted);
            btn.setAttribute('aria-label', muted ? 'Nyalakan suara' : 'Matikan suara');
            icon.textContent = muted ? 'OFF' : 'ON';
        };

        const unload = (slide) => {
            const player = slide.querySelector('[data-short-player]');
            const poster = slide.querySelector('[data-short-poster]');
            const playBtn = slide.querySelector('[data-short-play]');
            if (player) {
                player.innerHTML = '';
            }
            poster?.classList.remove('is-hidden');
            playBtn?.classList.remove('is-hidden');
            slide.classList.remove('is-playing', 'platform-youtube', 'platform-tiktok', 'platform-instagram');
            setMuteUi(slide, true);
        };

        const load = (slide, { withSound = soundOn } = {}) => {
            const embed = withSound
                ? slide.dataset.embedSound || slide.dataset.embed
                : slide.dataset.embed;
            const platform = slide.dataset.platform || 'youtube';
            const player = slide.querySelector('[data-short-player]');
            const poster = slide.querySelector('[data-short-poster]');
            const playBtn = slide.querySelector('[data-short-play]');
            if (!embed || !player) {
                return;
            }

            player.innerHTML = '';
            const iframe = document.createElement('iframe');
            iframe.src = embed;
            iframe.title = 'Short video';
            iframe.allow =
                'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
            iframe.allowFullscreen = true;
            iframe.setAttribute('playsinline', 'true');
            iframe.setAttribute('scrolling', 'no');
            iframe.className = `short-iframe is-${platform}`;
            player.appendChild(iframe);
            poster?.classList.add('is-hidden');
            playBtn?.classList.add('is-hidden');
            slide.classList.add('is-playing');
            slide.classList.add(`platform-${platform}`);
            setMuteUi(slide, !withSound);
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
            slide.querySelector('[data-short-play]')?.addEventListener('click', () => {
                load(slide, { withSound: soundOn });
            });
            slide.querySelector('[data-short-mute]')?.addEventListener('click', (event) => {
                event.stopPropagation();
                soundOn = !soundOn;
                load(slide, { withSound: soundOn });
            });
        });
    }
});
