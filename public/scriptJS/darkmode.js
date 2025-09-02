document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('theme-toggle');
    const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
    const currentTheme = localStorage.getItem('theme');

    // Applique le thème sauvegardé ou celui du système
    if (currentTheme === 'dark' || (!currentTheme && prefersDarkScheme.matches)) {
        document.body.classList.add('dark-mode');
        document.getElementById('theme-toggle-icon').textContent = '☀️';
    }

    // Écouteur pour le bouton
    themeToggle.addEventListener('click', function() {
        if (document.body.classList.contains('dark-mode')) {
            document.body.classList.remove('dark-mode');
            document.getElementById('theme-toggle-icon').textContent = '🌙';
            localStorage.setItem('theme', 'light');
        } else {
            document.body.classList.add('dark-mode');
            document.getElementById('theme-toggle-icon').textContent = '☀️';
            localStorage.setItem('theme', 'dark');
        }
    });

    // Écouteur pour les changements de préférence système
    prefersDarkScheme.addEventListener('change', e => {
        if (!localStorage.getItem('theme')) {
            if (e.matches) {
                document.body.classList.add('dark-mode');
                document.getElementById('theme-toggle-icon').textContent = '☀️';
            } else {
                document.body.classList.remove('dark-mode');
                document.getElementById('theme-toggle-icon').textContent = '🌙';
            }
        }
    });
});
