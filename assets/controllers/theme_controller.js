import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://symfony.com/bundles/StimulusBundle/current/index.html#lazy-stimulus-controllers
*/

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['icon']


// Méthode appelée quand le contrôleur est connecté au DOM (au chargement de la page).
    connect() {
        // Détecte si l'utilisateur préfère le thème sombre au niveau système.
        this.prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
        this.initTheme();
        // Écoute les changements de préférence système (ex: passage en mode sombre dans les paramètres OS).
        this.prefersDarkScheme.addEventListener('change', () => this.onSystemThemeChange());
    }

    // Méthode appelée quand le contrôleur est déconnecté du DOM (rarement utilisé ici).
    disconnect() {
        // Supprime l'écouteur pour éviter les fuites mémoire.
        this.prefersDarkScheme.removeEventListener('change', () => this.onSystemThemeChange());
    }

    // Initialise le thème en fonction des préférences sauvegardées ou du système.
    initTheme() {
        const currentTheme = localStorage.getItem('theme'); // Récupère le thème sauvegardé dans localStorage.
        // Si le thème sauvegardé est "dark" OU si aucun thème n'est sauvegardé mais que le système préfère le sombre :
        if (currentTheme === 'dark' || (!currentTheme && this.prefersDarkScheme.matches)) {
            document.body.classList.add('dark-mode'); // Ajoute la classe CSS pour le thème sombre.
            if (this.hasIconTarget) { // Vérifie si la cible "icon" existe dans le HTML.
                this.iconTarget.textContent = '☀️'; // Change l'icône pour indiquer que le thème sombre est actif.
            }
        } else {
            document.body.classList.remove('dark-mode'); // Retire la classe CSS pour le thème sombre.
            if (this.hasIconTarget) { // Vérifie si la cible "icon" existe dans le HTML.
                this.iconTarget.textContent = '🌙'; // Change l'icône pour indiquer que le thème clair est actif.
            }
        }
    }

    // Méthode appelée quand on clique sur le bouton (via `data-action="click->theme#toggleTheme"`).
    toggleTheme() {

        // Si le thème sombre est déjà actif :
        if (document.body.classList.contains('dark-mode')) {
            document.body.classList.remove('dark-mode'); // Passe en thème clair.
            if (this.hasIconTarget) {
                this.iconTarget.textContent = '🌙'; // Met à jour l'icône.
            }
            localStorage.setItem('theme', 'light'); // Sauvegarde le choix dans localStorage.
        } else {
            document.body.classList.add('dark-mode'); // Passe en thème sombre.
            if (this.hasIconTarget) {
                this.iconTarget.textContent = '☀️'; // Met à jour l'icône.
            }
            localStorage.setItem('theme', 'dark'); // Sauvegarde le choix dans localStorage.
        }
    }

    // Méthode appelée quand les préférences système changent (ex: passage en mode sombre dans les paramètres OS).
    onSystemThemeChange() {
        // Si aucun thème n'est sauvegardé dans localStorage, applique le thème en fonction des préférences système.
        if (!localStorage.getItem('theme')) {
            this.initTheme();
        }
    }
}
