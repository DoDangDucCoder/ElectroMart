/**
 * Dark/Light Mode Toggle Functionality
 * Handles theme switching with localStorage persistence
 */

class ThemeToggle {
    constructor() {
        this.currentTheme = this.getStoredTheme() || this.getSystemTheme();
        this.toggleButton = null;
        this.init();
    }

    init() {
        this.createToggleButton();
        this.applyTheme(this.currentTheme);
        this.bindEvents();
        this.setupPageTransition();
    }

    getSystemTheme() {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    getStoredTheme() {
        return localStorage.getItem('theme');
    }

    storeTheme(theme) {
        localStorage.setItem('theme', theme);
    }

    createToggleButton() {
        // Create toggle button
        this.toggleButton = document.createElement('div');
        this.toggleButton.className = 'theme-toggle';
        this.toggleButton.setAttribute('aria-label', 'Toggle theme');
        this.toggleButton.setAttribute('role', 'button');
        this.toggleButton.setAttribute('tabindex', '0');

        // Create icon
        const icon = document.createElement('i');
        icon.className = `theme-toggle-icon ${this.currentTheme === 'dark' ? 'sun' : 'moon'}`;
        icon.innerHTML = this.currentTheme === 'dark' ? '☀️' : '🌙';
        
        this.toggleButton.appendChild(icon);
        document.body.appendChild(this.toggleButton);

        // Add tooltip
        this.toggleButton.title = `Switch to ${this.currentTheme === 'dark' ? 'light' : 'dark'} mode`;
    }

    bindEvents() {
        // Click event
        this.toggleButton.addEventListener('click', () => {
            this.toggleTheme();
        });

        // Keyboard event
        this.toggleButton.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.toggleTheme();
            }
        });

        // Listen for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!this.getStoredTheme()) {
                this.currentTheme = e.matches ? 'dark' : 'light';
                this.applyTheme(this.currentTheme);
                this.updateButton();
            }
        });

        // Listen for storage changes (sync across tabs)
        window.addEventListener('storage', (e) => {
            if (e.key === 'theme') {
                this.currentTheme = e.newValue || this.getSystemTheme();
                this.applyTheme(this.currentTheme);
                this.updateButton();
            }
        });
    }

    toggleTheme() {
        this.toggleButton.classList.add('loading');
        
        setTimeout(() => {
            this.currentTheme = this.currentTheme === 'dark' ? 'light' : 'dark';
            this.applyTheme(this.currentTheme);
            this.storeTheme(this.currentTheme);
            this.updateButton();
            this.toggleButton.classList.remove('loading');
            
            // Trigger custom event
            this.dispatchThemeChangeEvent();
        }, 150);
    }

    applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        
        // Update meta theme-color for mobile browsers
        this.updateMetaThemeColor(theme);
        
        // Update button tooltip
        this.toggleButton.title = `Switch to ${theme === 'dark' ? 'light' : 'dark'} mode`;
    }

    updateButton() {
        const icon = this.toggleButton.querySelector('.theme-toggle-icon');
        icon.className = `theme-toggle-icon ${this.currentTheme === 'dark' ? 'sun' : 'moon'}`;
        icon.innerHTML = this.currentTheme === 'dark' ? '☀️' : '🌙';
    }

    updateMetaThemeColor(theme) {
        let metaThemeColor = document.querySelector('meta[name="theme-color"]');
        if (!metaThemeColor) {
            metaThemeColor = document.createElement('meta');
            metaThemeColor.name = 'theme-color';
            document.head.appendChild(metaThemeColor);
        }
        
        metaThemeColor.content = theme === 'dark' ? '#1a1a1a' : '#ffffff';
    }

    setupPageTransition() {
        // Add transition class to body
        document.body.classList.add('page-transition');
        
        // Remove transition class after page load
        window.addEventListener('load', () => {
            setTimeout(() => {
                document.body.classList.add('loaded');
            }, 100);
        });
    }

    dispatchThemeChangeEvent() {
        const event = new CustomEvent('themeChanged', {
            detail: {
                theme: this.currentTheme,
                isDark: this.currentTheme === 'dark',
                isLight: this.currentTheme === 'light'
            }
        });
        window.dispatchEvent(event);
    }

    // Public methods
    getCurrentTheme() {
        return this.currentTheme;
    }

    isDarkMode() {
        return this.currentTheme === 'dark';
    }

    isLightMode() {
        return this.currentTheme === 'light';
    }

    setTheme(theme) {
        if (theme === 'dark' || theme === 'light') {
            this.currentTheme = theme;
            this.applyTheme(theme);
            this.storeTheme(theme);
            this.updateButton();
            this.dispatchThemeChangeEvent();
        }
    }

    resetToSystem() {
        localStorage.removeItem('theme');
        this.currentTheme = this.getSystemTheme();
        this.applyTheme(this.currentTheme);
        this.updateButton();
        this.dispatchThemeChangeEvent();
    }
}

// Enhanced Theme Utilities
class ThemeUtils {
    static getContrastColor(backgroundColor) {
        // Simple contrast calculation
        const hex = backgroundColor.replace('#', '');
        const r = parseInt(hex.substr(0, 2), 16);
        const g = parseInt(hex.substr(2, 2), 16);
        const b = parseInt(hex.substr(4, 2), 16);
        const brightness = (r * 299 + g * 587 + b * 114) / 1000;
        return brightness > 128 ? '#000000' : '#ffffff';
    }

    static animateThemeTransition(element, duration = 300) {
        element.style.transition = `all ${duration}ms ease-in-out`;
        return new Promise(resolve => {
            setTimeout(resolve, duration);
        });
    }

    static getThemeColors() {
        const root = document.documentElement;
        const computedStyle = getComputedStyle(root);
        
        return {
            primary: computedStyle.getPropertyValue('--bg-primary').trim(),
            secondary: computedStyle.getPropertyValue('--bg-secondary').trim(),
            text: computedStyle.getPropertyValue('--text-primary').trim(),
            accent: computedStyle.getPropertyValue('--accent-color').trim()
        };
    }
}

// Initialize theme toggle when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.themeToggle = new ThemeToggle();
    
    // Listen for theme changes
    window.addEventListener('themeChanged', (e) => {
        console.log('Theme changed to:', e.detail.theme);
        
        // Update any custom components that need theme awareness
        updateCustomComponents(e.detail.theme);
    });
});

// Function to update custom components
function updateCustomComponents(theme) {
    // Update charts if any
    if (window.updateChartsTheme) {
        window.updateChartsTheme(theme);
    }
    
    // Update maps if any
    if (window.updateMapsTheme) {
        window.updateMapsTheme(theme);
    }
    
    // Update any other theme-aware components
    const themeAwareElements = document.querySelectorAll('[data-theme-aware]');
    themeAwareElements.forEach(element => {
        element.setAttribute('data-current-theme', theme);
    });
}

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { ThemeToggle, ThemeUtils };
}

// Add some helpful CSS classes for JavaScript
const style = document.createElement('style');
style.textContent = `
    .theme-transitioning * {
        transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease !important;
    }
    
    .theme-loading {
        opacity: 0.7;
        pointer-events: none;
    }
`;
document.head.appendChild(style);
