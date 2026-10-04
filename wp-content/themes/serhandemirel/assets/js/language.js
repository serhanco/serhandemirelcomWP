/**
 * Language switcher: remembers the visitor's choice and runs the desktop menu.
 *
 * A click on any [data-sd-lang] link saves that language in a cookie, so the
 * browser-language check on the front page never overrides it.
 */
(function () {
    const cookie = (window.sdLanguage && window.sdLanguage.cookie) || 'sd_lang';

    function remember(lang) {
        document.cookie = cookie + '=' + lang + ';path=/;max-age=31536000;samesite=lax' + (location.protocol === 'https:' ? ';secure' : '');
    }

    document.addEventListener('click', (e) => {
        const link = e.target.closest('[data-sd-lang]');
        if (link) {
            remember(link.getAttribute('data-sd-lang'));
        }
    });

    const menu = document.querySelector('[data-sd-lang-menu]');
    if (!menu) {
        return;
    }
    const button = menu.querySelector('button');
    const links = Array.from(menu.querySelectorAll('a'));

    function setOpen(open, focusItem) {
        menu.classList.toggle('is-open', open);
        button.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (open && focusItem) {
            (links.find((a) => a.hasAttribute('aria-current')) || links[0]).focus();
        }
    }

    button.addEventListener('click', () => setOpen(!menu.classList.contains('is-open'), false));

    button.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            e.preventDefault();
            setOpen(true, true);
        }
    });

    menu.addEventListener('keydown', (e) => {
        const index = links.indexOf(e.target);
        if (e.key === 'Escape') {
            setOpen(false);
            button.focus();
        } else if (index > -1 && (e.key === 'ArrowDown' || e.key === 'ArrowUp')) {
            e.preventDefault();
            const step = e.key === 'ArrowDown' ? 1 : -1;
            links[(index + step + links.length) % links.length].focus();
        } else if (index > -1 && (e.key === 'Home' || e.key === 'End')) {
            e.preventDefault();
            links[e.key === 'Home' ? 0 : links.length - 1].focus();
        }
    });

    // Close when focus or a click leaves the menu.
    menu.addEventListener('focusout', (e) => {
        if (!menu.contains(e.relatedTarget)) {
            setOpen(false);
        }
    });
    document.addEventListener('click', (e) => {
        if (!menu.contains(e.target)) {
            setOpen(false);
        }
    });
})();
