const menuToggle = document.querySelector('.site-menu-toggle');
const siteNavigation = document.querySelector('#site-navigation');

if (menuToggle && siteNavigation) {
    menuToggle.addEventListener('click', () => {
        const isOpen = menuToggle.getAttribute('aria-expanded') === 'true';
        menuToggle.setAttribute('aria-expanded', String(!isOpen));
        menuToggle.setAttribute('aria-label', isOpen ? 'Abrir menú' : 'Cerrar menú');
        siteNavigation.classList.toggle('is-open', !isOpen);
    });

    siteNavigation.addEventListener('click', (event) => {
        if (event.target.closest('a')) {
            menuToggle.setAttribute('aria-expanded', 'false');
            menuToggle.setAttribute('aria-label', 'Abrir menú');
            siteNavigation.classList.remove('is-open');
        }
    });
}
