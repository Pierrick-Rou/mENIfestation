document.addEventListener('DOMContentLoaded', function () {
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-toggle-icon');
    const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
    const currentTheme = localStorage.getItem('theme');

    // Fonction pour appliquer le thème
    function applyTheme(theme) {
        if (theme === 'dark') {
            document.documentElement.classList.add('dark-mode');
            themeIcon.textContent = '☀️';
            localStorage.setItem('theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark-mode');
            themeIcon.textContent = '🌙';
            localStorage.setItem('theme', 'light');
        }
    }

    // Appliquer le thème sauvegardé ou la préférence système
    if (currentTheme) {
        applyTheme(currentTheme);
    } else {
        applyTheme(prefersDarkScheme.matches ? 'dark' : 'light');
    }

    // Toggle au clic
    themeToggle.addEventListener('click', () => {
        if (document.documentElement.classList.contains('dark-mode')) {
            applyTheme('light');
        } else {
            applyTheme('dark');
        }
    });

    // Écoute changement système
    prefersDarkScheme.addEventListener('change', e => {
        if (!localStorage.getItem('theme')) {
            applyTheme(e.matches ? 'dark' : 'light');
        }
    });
});
