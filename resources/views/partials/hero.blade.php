<section class="hero" id="hero" aria-roledescription="carousel" aria-label="Destacados"></section>
<script>
    const HERO_CONFIG = {
        autoplay: true,
        interval: 6000,
        pauseOnHover: true,
        loop: true,
        slides: @json($banners)
    };

    const BASE_TOOLTIP = "{{ $baseTooltip ?? '' }}";
    const BASE_TITLE = "{{ $baseTitle ?? '' }}";
    const BASE_ASCENT = "{{ $baseAscent ?? '' }}";
    const BASE_DESCRIPTION = "{{ $baseDescription ?? '' }}";
    const BASE_LINK_TEXT = "{{ $baseLinkText ?? '' }}";
    const BASE_LINK_ADDRESS = "{{ $baseLinkAddress ?? '' }}";

    (function initHero() {
        const hero = document.getElementById('hero');
        const slides = (HERO_CONFIG.slides || []).filter(s => s && s.image);
        if (!hero || !slides.length) return;

        const len = slides.length,
            multi = len > 1;
        const interval = Math.max(2000, +HERO_CONFIG.interval || 6000);
        const autoplay = multi && HERO_CONFIG.autoplay !== false;
        const loop = HERO_CONFIG.loop !== false;
        const esc = s => String(s == null ? (BASE_TITLE ?? ' ') : s).replace(/[&<>"']/g, c => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        } [c]));
        const render_slide = (s, i) => {
            if (s.isVideo) {
                return '<video src="' + esc(s.image) + '" alt="" decoding="async" ' + (i === 0 ?
                    'fetchpriority="high"' : 'loading="lazy"') + ' style="object-position:' + esc(s
                    .position || 'center') + '; object-fit: fill" autoplay muted loop></video>'
            }
            return '<img src="' + esc(s.image) + '" alt="" decoding="async" ' + (i === 0 ?
                'fetchpriority="high"' : 'loading="lazy"') + ' style="object-position:' + esc(s.position ||
                'center') + '; object-fit: cover">';
        }

        hero.style.setProperty('--hero-interval', interval + 'ms');
        hero.innerHTML =
            '<div class="hero-track">' + slides.map((s, i) => {
                const tag = i === 0 ? 'h1' : 'h2';
                const cta = s.cta ? '<button class="btn-cta" data-cta data-href="' + esc(s.cta.href) +
                    '" data-toast="' + esc(s.cta.toast) + '">' + esc(s.cta.label) +
                    ' <i class="fas fa-arrow-right"></i></button>' : (BASE_LINK_ADDRESS ? '<button class="btn-cta" data-cta data-href="' + BASE_LINK_ADDRESS +
                    '">' + BASE_LINK_TEXT +
                    ' <i class="fas fa-arrow-right"></i></button>' : '');
                return '<article class="hero-slide' + (i === 0 ? ' is-active' : '') +
                    '" role="group" aria-roledescription="slide" aria-label="' + (i + 1) + ' de ' + len + '">' +
                    '<div class="hero-bg">' + render_slide(s, i) + '</div>' +
                    '<div class="hero-inner">' +
                    '<div class="hero-kicker">' + (s.kicker || (BASE_TOOLTIP ?? ' ')) + '</div>' +
                    '<' + tag + ' class="hero-title">' + esc(s.title) + (s.accent ? '<span>' + esc(s.accent) +
                        '</span>' : '<span>'+ (BASE_ASCENT ?? ' ') +'</span>') + '</' + tag + '>' +
                    '<p class="hero-text">' + (s.text ? esc(s.text) :
                        BASE_DESCRIPTION ?? ' '
                        ) + '</p>' + cta +
                    '</div></article>';
            }).join('') + '</div>' +
            (multi ?
                '<button class="hero-arrow prev" aria-label="Slide anterior"><i class="fas fa-chevron-left"></i></button>' +
                '<button class="hero-arrow next" aria-label="Slide siguiente"><i class="fas fa-chevron-right"></i></button>' +
                '<div class="hero-nav">' + slides.map((_, i) => '<button class="hero-dot' + (i === 0 ? ' active' :
                    '') + '" aria-label="Ir al slide ' + (i + 1) + '"></button>').join('') +
                (autoplay ?
                    '<button class="hero-pause" aria-label="Pausar carousel"><i class="fas fa-pause"></i></button>' :
                    '') + '</div>' +
                (autoplay ? '<div class="hero-progress" aria-hidden="true"><span></span></div>' : '') :
                '');

        hero.addEventListener('click', e => {
            const b = e.target.closest('[data-cta]');
            if (!b) return;
            const href = b.dataset.href;
            if (href && href.startsWith('#')) {
                const t = document.querySelector(href);
                if (t) t.scrollIntoView({
                    behavior: 'smooth'
                });
            } else if (href) {
                location.href = href;
            } else showToast(b.dataset.toast || 'Explorando el catálogo…', 'fa-compact-disc');
        });

        if (!multi) return;

        const els = hero.querySelectorAll('.hero-slide'),
            dots = hero.querySelectorAll('.hero-dot');
        const bar = hero.querySelector('.hero-progress span'),
            pauseBtn = hero.querySelector('.hero-pause');
        let idx = 0,
            timer = null,
            remaining = interval,
            startedAt = 0;
        const hold = {
            user: false,
            hover: false,
            focus: false,
            hidden: document.hidden,
            offscreen: false
        };
        const canRun = () => autoplay && !Object.values(hold).some(Boolean);

        function restartBar() {
            if (!bar) return;
            bar.classList.remove('run');
            void bar.offsetWidth;
            if (autoplay) bar.classList.add('run');
        }

        function stop() {
            if (timer) {
                clearTimeout(timer);
                timer = null;
                remaining = Math.max(0, remaining - (Date.now() - startedAt));
            }
            if (bar) bar.style.animationPlayState = 'paused';
        }

        function start() {
            if (timer) {
                clearTimeout(timer);
                timer = null;
            }
            if (!canRun()) {
                if (bar) bar.style.animationPlayState = 'paused';
                return;
            }
            if (bar) bar.style.animationPlayState = 'running';
            startedAt = Date.now();
            timer = setTimeout(() => go(idx + 1), remaining);
        }

        function sync() {
            stop();
            start();
        }

        function go(n) {
            if (!loop && (n < 0 || n >= len)) {
                stop();
                return;
            }
            idx = (n + len) % len;
            els.forEach((el, i) => el.classList.toggle('is-active', i === idx));
            dots.forEach((d, i) => {
                d.classList.toggle('active', i === idx);
                d.setAttribute('aria-current', i === idx ? 'true' : 'false');
            });
            stop();
            remaining = interval;
            restartBar();
            start();
        }

        hero.querySelector('.prev').addEventListener('click', () => go(idx - 1));
        hero.querySelector('.next').addEventListener('click', () => go(idx + 1));
        dots.forEach((d, i) => d.addEventListener('click', () => go(i)));

        if (pauseBtn) pauseBtn.addEventListener('click', () => {
            hold.user = !hold.user;
            pauseBtn.innerHTML = '<i class="fas ' + (hold.user ? 'fa-play' : 'fa-pause') + '"></i>';
            pauseBtn.setAttribute('aria-label', hold.user ? 'Reanudar carousel' : 'Pausar carousel');
            sync();
        });

        if (HERO_CONFIG.pauseOnHover !== false) {
            hero.addEventListener('pointerenter', e => {
                if (e.pointerType === 'mouse') {
                    hold.hover = true;
                    sync();
                }
            });
            hero.addEventListener('pointerleave', () => {
                hold.hover = false;
                sync();
            });
        }
        hero.addEventListener('focusin', e => {
            if (e.target.matches(':focus-visible')) {
                hold.focus = true;
                sync();
            }
        });
        hero.addEventListener('focusout', () => {
            hold.focus = false;
            sync();
        });
        document.addEventListener('visibilitychange', () => {
            hold.hidden = document.hidden;
            sync();
        });
        if ('IntersectionObserver' in window) new IntersectionObserver(([en]) => {
            hold.offscreen = !en.isIntersecting;
            sync();
        }, {
            threshold: .2
        }).observe(hero);

        hero.addEventListener('keydown', e => {
            if (e.key === 'ArrowLeft') go(idx - 1);
            else if (e.key === 'ArrowRight') go(idx + 1);
        });
        let sx = 0,
            sy = 0;
        hero.addEventListener('touchstart', e => {
            sx = e.touches[0].clientX;
            sy = e.touches[0].clientY;
        }, {
            passive: true
        });
        hero.addEventListener('touchend', e => {
            const dx = e.changedTouches[0].clientX - sx,
                dy = e.changedTouches[0].clientY - sy;
            if (Math.abs(dx) > 45 && Math.abs(dx) > Math.abs(dy) * 1.3) go(idx + (dx < 0 ? 1 : -1));
        }, {
            passive: true
        });

        restartBar();
        start();
    })();
</script>
