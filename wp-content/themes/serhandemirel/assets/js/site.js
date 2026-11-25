/**
 * Behaviour shared by every page, with no libraries: mobile menu, the navbar
 * that hides on scroll, project filters, the local clock, back-to-top, and on
 * inner pages the reading bar, article outline, copy link, Insights filter and
 * "Load more". The front page adds GSAP and Lenis on top (main.js).
 */
(function () {
    'use strict';

    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var cfg = window.sdSite || {};

    function ready(fn) {
        if (document.readyState !== 'loading') { fn(); } else { document.addEventListener('DOMContentLoaded', fn); }
    }

    ready(function () {
        // --- Mobile menu ---
        var mobileBtn = document.getElementById('mobile-menu-btn');
        var iconMenu = document.getElementById('icon-menu');
        var iconClose = document.getElementById('icon-close');
        var menu = document.getElementById('mobile-fullscreen-menu');
        var menuContent = document.getElementById('mobile-menu-content');
        var menuOpen = false;

        function swap(el, add, remove) {
            if (!el) { return; }
            add.forEach(function (c) { el.classList.add(c); });
            remove.forEach(function (c) { el.classList.remove(c); });
        }

        function toggleMenu() {
            menuOpen = !menuOpen;
            if (menuOpen) {
                swap(menu, ['opacity-100', 'pointer-events-auto'], ['opacity-0', 'pointer-events-none']);
                swap(menuContent, ['translate-y-0', 'opacity-100', 'scale-100'], ['translate-y-8', 'opacity-0', 'scale-90']);
                swap(iconMenu, ['opacity-0', 'rotate-90', 'scale-50'], ['opacity-100', 'rotate-0']);
                swap(iconClose, ['opacity-100', 'rotate-0', 'scale-100'], ['opacity-0', '-rotate-90', 'scale-50']);
                document.body.style.overflow = 'hidden';
            } else {
                swap(menu, ['opacity-0', 'pointer-events-none'], ['opacity-100', 'pointer-events-auto']);
                swap(menuContent, ['translate-y-8', 'opacity-0', 'scale-90'], ['translate-y-0', 'opacity-100', 'scale-100']);
                swap(iconClose, ['opacity-0', '-rotate-90', 'scale-50'], ['opacity-100', 'rotate-0', 'scale-100']);
                swap(iconMenu, ['opacity-100', 'rotate-0'], ['opacity-0', 'rotate-90', 'scale-50']);
                document.body.style.overflow = '';
            }
            if (mobileBtn) { mobileBtn.setAttribute('aria-expanded', menuOpen ? 'true' : 'false'); }
        }

        if (mobileBtn && menu) {
            mobileBtn.addEventListener('click', toggleMenu);
            document.querySelectorAll('.mobile-link').forEach(function (link) {
                link.addEventListener('click', function () { if (menuOpen) { toggleMenu(); } });
            });
            document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && menuOpen) { toggleMenu(); } });
        }

        // --- Navbar hides on the way down, returns on the way up; reading bar ---
        var nav = document.getElementById('main-nav');
        var bar = document.getElementById('sd-reading-bar');
        var article = document.querySelector('[data-sd-article]');
        var lastScroll = 0;
        var ticking = false;

        function onScroll() {
            var y = window.pageYOffset;
            if (nav) {
                nav.style.transform = (y > 150 && y > lastScroll && !menuOpen) ? 'translate(-50%, -150%)' : 'translate(-50%, 0)';
            }
            if (bar && article) {
                var rect = article.getBoundingClientRect();
                var total = rect.height - window.innerHeight;
                var done = total > 0 ? Math.min(1, Math.max(0, -rect.top / total)) : 1;
                bar.style.transform = 'scaleX(' + done + ')';
            }
            lastScroll = y;
            ticking = false;
        }
        window.addEventListener('scroll', function () {
            if (!ticking) { ticking = true; window.requestAnimationFrame(onScroll); }
        }, { passive: true });

        // --- Project filters (front page and /work/) ---
        var filterBtns = document.querySelectorAll('.filter-btn');
        var items = document.querySelectorAll('.portfolio-item');
        filterBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                filterBtns.forEach(function (b) {
                    b.classList.remove('bg-white', 'text-black');
                    b.classList.add('text-white');
                    b.setAttribute('aria-pressed', 'false');
                });
                btn.classList.add('bg-white', 'text-black');
                btn.classList.remove('text-white');
                btn.setAttribute('aria-pressed', 'true');
                var filter = btn.getAttribute('data-filter');
                items.forEach(function (item) {
                    var show = filter === 'all' || (item.getAttribute('data-category') || '').split(' ').indexOf(filter) !== -1;
                    item.style.display = show ? '' : 'none';
                    if (show && !reduceMotion && item.animate) {
                        item.animate([{ opacity: 0, transform: 'scale(0.96)' }, { opacity: 1, transform: 'scale(1)' }], { duration: 350, easing: 'cubic-bezier(.2,.7,.2,1)' });
                    }
                });
            });
        });

        // --- Year, back to top, local clock ---
        var yearEl = document.getElementById('current-year');
        if (yearEl) { yearEl.textContent = new Date().getFullYear(); }
        var backToTop = document.getElementById('back-to-top');
        if (backToTop) {
            backToTop.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' }); });
        }
        var timeEl = document.getElementById('footer-time');
        if (timeEl) {
            var tick = function () {
                try {
                    timeEl.textContent = new Date().toLocaleTimeString('en-US', {
                        timeZone: cfg.timezone || 'Europe/Istanbul', hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit'
                    }) + ' ' + (cfg.timezoneLabel || '');
                } catch (e) { /* unknown time zone: leave the placeholder */ }
            };
            tick();
            setInterval(tick, 1000);
        }

        // --- Article outline: highlight the section being read ---
        var tocLinks = Array.prototype.slice.call(document.querySelectorAll('[data-toc]'));
        if (tocLinks.length && 'IntersectionObserver' in window) {
            var setActive = function (id) {
                tocLinks.forEach(function (l) {
                    var on = l.getAttribute('data-toc') === id;
                    l.classList.toggle('text-white', on);
                    l.classList.toggle('border-purple-500', on);
                    l.classList.toggle('text-gray-500', !on);
                    if (on) { l.setAttribute('aria-current', 'location'); } else { l.removeAttribute('aria-current'); }
                });
            };
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (e) { if (e.isIntersecting) { setActive(e.target.id); } });
            }, { rootMargin: '-20% 0px -70% 0px' });
            tocLinks.forEach(function (l) {
                var target = document.getElementById(l.getAttribute('data-toc'));
                if (target) { io.observe(target); }
            });
            document.querySelectorAll('[data-toc-mobile] a').forEach(function (a) {
                a.addEventListener('click', function () {
                    var d = a.closest('details');
                    if (d) { d.open = false; }
                });
            });
        }

        // --- Copy link ---
        document.querySelectorAll('[data-copy-link]').forEach(function (btn) {
            var label = btn.textContent;
            btn.addEventListener('click', function () {
                var done = function () {
                    btn.textContent = cfg.copied || 'Link copied';
                    setTimeout(function () { btn.textContent = label; }, 1800);
                };
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(window.location.href).then(done, function () { window.prompt('', window.location.href); });
                } else {
                    window.prompt('', window.location.href);
                }
            });
        });

        // --- Insights: filter the cards on this page while typing ---
        var filterInput = document.querySelector('[data-insights-filter]');
        var grid = document.querySelector('[data-insights-grid]');
        var empty = document.querySelector('[data-insights-empty]');
        function applyFilter() {
            if (!filterInput || !grid) { return; }
            var q = filterInput.value.trim().toLocaleLowerCase();
            var shown = 0;
            grid.querySelectorAll('[data-insight-card]').forEach(function (card) {
                var hit = !q || (card.getAttribute('data-search') || '').indexOf(q) !== -1;
                card.style.display = hit ? '' : 'none';
                if (hit) { shown++; }
            });
            if (empty) { empty.hidden = shown !== 0; }
        }
        if (filterInput) { filterInput.addEventListener('input', applyFilter); }

        // --- Insights: "Load more" appends the next page without a reload ---
        var more = document.querySelector('[data-load-more]');
        if (more && grid && window.fetch && window.DOMParser) {
            more.addEventListener('click', function (e) {
                e.preventDefault();
                var url = more.getAttribute('href');
                if (!url || more.getAttribute('aria-busy') === 'true') { return; }
                more.setAttribute('aria-busy', 'true');
                var label = more.textContent;
                more.textContent = cfg.loading || 'Loading…';
                fetch(url, { credentials: 'same-origin' }).then(function (r) { return r.text(); }).then(function (html) {
                    var doc = new DOMParser().parseFromString(html, 'text/html');
                    var next = doc.querySelector('[data-insights-grid]');
                    if (next) {
                        next.querySelectorAll('[data-insight-card]').forEach(function (card) {
                            grid.insertBefore(document.importNode(card, true), grid.querySelector('[data-insights-tail]'));
                        });
                    }
                    var nextMore = doc.querySelector('[data-load-more]');
                    if (nextMore) {
                        more.setAttribute('href', nextMore.getAttribute('href'));
                        more.textContent = label;
                        more.removeAttribute('aria-busy');
                    } else {
                        more.remove();
                    }
                    applyFilter();
                }).catch(function () {
                    window.location.href = url;
                });
            });
        }

        // Live search on the glossary and FAQ pages: hide items that do not
        // match, and groups left empty.
        document.querySelectorAll('[data-live-search]').forEach(function (input) {
            var scope = document.querySelector(input.getAttribute('data-live-search'));
            var none = document.querySelector('[data-live-empty]');
            if (!scope) return;
            input.addEventListener('input', function () {
                var q = input.value.trim().toLowerCase();
                var shown = 0;
                scope.querySelectorAll('[data-live-item]').forEach(function (item) {
                    var hit = !q || item.textContent.toLowerCase().indexOf(q) !== -1;
                    item.hidden = !hit;
                    if (hit) shown++;
                    if (hit && q && item.tagName === 'DETAILS') item.open = true;
                });
                scope.querySelectorAll('[data-live-group]').forEach(function (group) {
                    group.hidden = !group.querySelector('[data-live-item]:not([hidden])');
                });
                if (none) none.hidden = shown > 0;
            });
        });

        // Kind filter on the talks page.
        var kindBtns = document.querySelectorAll('[data-kind-filter]');
        kindBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var kind = btn.getAttribute('data-kind-filter');
                kindBtns.forEach(function (b) {
                    var on = b === btn;
                    b.setAttribute('aria-pressed', on ? 'true' : 'false');
                    b.classList.toggle('bg-white', on);
                    b.classList.toggle('text-black', on);
                    b.classList.toggle('border-white', on);
                    b.classList.toggle('font-semibold', on);
                    b.classList.toggle('text-gray-300', !on);
                    b.classList.toggle('border-white/10', !on);
                });
                document.querySelectorAll('[data-kind]').forEach(function (row) {
                    row.hidden = kind !== 'all' && row.getAttribute('data-kind') !== kind;
                });
                document.querySelectorAll('[data-year-group]').forEach(function (group) {
                    group.hidden = !group.querySelector('[data-kind]:not([hidden])');
                });
            });
        });
    });
})();
