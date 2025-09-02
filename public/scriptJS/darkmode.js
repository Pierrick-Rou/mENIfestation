document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('theme-toggle');
    const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
    const currentTheme = localStorage.getItem('theme');

    // Applique le thème sauvegardé en priorité
    if (currentTheme === 'dark') {
        document.body.classList.add('dark-mode');
        document.getElementById('theme-toggle-icon').textContent = '☀️';
    } else if (currentTheme === 'light') {
        document.body.classList.remove('dark-mode');
        document.getElementById('theme-toggle-icon').textContent = '🌙';
    } else {
        // Si aucun thème sauvegardé → on suit la préférence système
        if (prefersDarkScheme.matches) {
            document.body.classList.add('dark-mode');
            document.getElementById('theme-toggle-icon').textContent = '☀️';
        } else {
            document.body.classList.remove('dark-mode');
            document.getElementById('theme-toggle-icon').textContent = '🌙';
        }
    }

    // Toggle via le bouton
    themeToggle.addEventListener('click', function() {
        if (document.body.classList.contains('dark-mode')) {
            document.body.classList.remove('dark-mode');
            document.getElementById('theme-toggle-icon').textContent = '🌙';
            localStorage.setItem('theme', 'light'); // sauvegarde
        } else {
            document.body.classList.add('dark-mode');
            document.getElementById('theme-toggle-icon').textContent = '☀️';
            localStorage.setItem('theme', 'dark'); // sauvegarde
        }
    });

    // Si l’utilisateur change ses préférences système et qu’on n’a pas forcé un choix
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
