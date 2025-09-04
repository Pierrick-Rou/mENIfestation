function initDarkMode() {
    const btn = document.getElementById('theme-toggle');
    const icon = document.getElementById('theme-toggle-icon');
    const root = document.documentElement;

    if (!btn || !root) return;

    // Empêche double initialisation (Turbo, etc.)
    if (btn.dataset.bound === 'true') return;
    btn.dataset.bound = 'true';

    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

    const getStored = () => localStorage.getItem('theme'); // 'dark' | 'light' | null
    const store = (val) => localStorage.setItem('theme', val);

    const applyTheme = (mode) => {
        if (mode === 'dark') {
            root.classList.add('dark-mode');
            if (icon) icon.textContent = '☀️';
        } else {
            root.classList.remove('dark-mode');
            if (icon) icon.textContent = '🌙';
        }
    };

    // Synchroniser l’UI avec l’état actuel (localStorage ou préférence système)
    const current = getStored() || (prefersDark ? 'dark' : 'light');
    applyTheme(current);

    const toggleTheme = () => {
        const next = root.classList.contains('dark-mode') ? 'light' : 'dark';
        applyTheme(next);
        store(next);
    };

    btn.addEventListener('click', toggleTheme);
}

// Compatible DOM classique et Turbo
document.addEventListener('DOMContentLoaded', initDarkMode);
document.addEventListener('turbo:load', initDarkMode);
