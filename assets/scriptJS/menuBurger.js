function initMenuBurger() {
    const burger = document.getElementById('burger-btn');
    const navLinks = document.querySelector('.nav-links');

    if (!burger || !navLinks) {
        // Les éléments ne sont pas présents sur cette page.
        return;
    }

    // Éviter les handlers dupliqués si Turbo recharge la page
    burger.dataset.bound = burger.dataset.bound || 'false';
    if (burger.dataset.bound === 'true') {
        return;
    }
    burger.dataset.bound = 'true';

    burger.addEventListener('click', () => {
        navLinks.classList.toggle('active');
        burger.classList.toggle('toggle');
    });
}

// Initialiser selon l’environnement (Turbo ou non)
document.addEventListener('DOMContentLoaded', initMenuBurger);
document.addEventListener('turbo:load', initMenuBurger);
