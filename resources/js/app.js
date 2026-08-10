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
    if (!header) {
        return;
    }

    const onScroll = () => {
        header.classList.toggle('is-scrolled', window.scrollY > 12);
    };

    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
});
