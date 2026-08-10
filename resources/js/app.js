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
});
