import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://symfony.com/bundles/StimulusBundle/current/index.html#lazy-stimulus-controllers
*/

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['icon']

    initialize() {
        // Called once when the controller is first instantiated (per element)

        // Here you can initialize variables, create scoped callables for event
        // listeners, instantiate external libraries, etc.
        // this._fooBar = this.fooBar.bind(this)
    }

    connect() {
        // Called every time the controller is connected to the DOM
        // (on page load, when it's added to the DOM, moved in the DOM, etc.)

        // Here you can add event listeners on the element or target elements,
        // add or remove classes, attributes, dispatch custom events, etc.
        // this.fooTarget.addEventListener('click', this._fooBar)
        this.prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
        this.initTheme();
        this.prefersDarkScheme.addEventListener('change', () => this.onSystemThemeChange());
    }

    // Add custom controller actions here
    // fooBar() { this.fooTarget.classList.toggle(this.bazClass) }

    disconnect() {
        // Called anytime its element is disconnected from the DOM
        // (on page change, when it's removed from or moved in the DOM, etc.)

        // Here you should remove all event listeners added in "connect()"
        // this.fooTarget.removeEventListener('click', this._fooBar)
        this.prefersDarkScheme.removeEventListener('change', () => this.onSystemThemeChange());
    }

    initTheme() {
        const currentTheme = localStorage.getItem('theme');
        if (currentTheme === 'dark' || (!currentTheme && this.prefersDarkScheme.matches)) {
            document.body.classList.add('dark-mode');
            if (this.hasIconTarget) {
                this.iconTarget.textContent = '☀️';
            }
        } else {
            document.body.classList.remove('dark-mode');
            if (this.hasIconTarget) {
                this.iconTarget.textContent = '🌙';
            }
        }
    }

    toggleTheme() {
        if (document.body.classList.contains('dark-mode')) {
            document.body.classList.remove('dark-mode');
            if (this.hasIconTarget) {
                this.iconTarget.textContent = '🌙';
            }
            localStorage.setItem('theme', 'light');
        } else {
            document.body.classList.add('dark-mode');
            if (this.hasIconTarget) {
                this.iconTarget.textContent = '☀️';
            }
            localStorage.setItem('theme', 'dark');
        }
    }

    onSystemThemeChange() {
        if (!localStorage.getItem('theme')) {
            this.initTheme();
        }
    }
}
