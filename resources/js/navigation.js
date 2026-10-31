const toggle = document.querySelector('[data-menu-toggle]');
const menu = document.getElementById('menu-movil');

if (toggle && menu) {
    const label = toggle.querySelector('[data-menu-label]');
    const icons = toggle.querySelectorAll('[data-menu-icon]');

    function isOpen() {
        return toggle.getAttribute('aria-expanded') === 'true';
    }

    function setOpen(open) {
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        menu.hidden = !open;

        if (label) {
            label.textContent = open ? 'Cerrar menú' : 'Abrir menú';
        }

        icons.forEach((icon) => {
            icon.classList.toggle('hidden', icon.dataset.menuIcon !== (open ? 'close' : 'open'));
        });
    }

    toggle.addEventListener('click', () => {
        setOpen(!isOpen());
    });

    menu.addEventListener('click', (event) => {
        if (event.target instanceof Element && event.target.closest('a')) {
            setOpen(false);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && isOpen()) {
            setOpen(false);
            toggle.focus();
        }
    });

    const desktop = window.matchMedia('(min-width: 1024px)');
    const closeOnDesktop = (event) => {
        if (event.matches) {
            setOpen(false);
        }
    };

    if (typeof desktop.addEventListener === 'function') {
        desktop.addEventListener('change', closeOnDesktop);
    } else if (typeof desktop.addListener === 'function') {
        desktop.addListener(closeOnDesktop);
    }
}
