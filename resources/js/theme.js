/**
 * Need Help Emergency Resource Finder - Dynamic Theme Engine
 * Supports 'light', 'dark', and 'system' modes with cross-tab syncing,
 * zero-flicker hydration, keyboard shortcuts, and UI element synchronization.
 */

const THEME_STORAGE_KEY = 'nh_theme';

export const ThemeEngine = {
    STORAGE_KEY: THEME_STORAGE_KEY,

    getStoredTheme() {
        try {
            return localStorage.getItem(THEME_STORAGE_KEY) || 'system';
        } catch (e) {
            return 'system';
        }
    },

    getSystemTheme() {
        return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches
            ? 'dark'
            : 'light';
    },

    getEffectiveTheme(theme) {
        const targetTheme = theme || this.getStoredTheme();
        if (targetTheme === 'system') {
            return this.getSystemTheme();
        }
        return targetTheme === 'dark' ? 'dark' : 'light';
    },

    applyTheme(theme, showToast = false) {
        const activeTheme = theme || this.getStoredTheme();
        const effectiveTheme = this.getEffectiveTheme(activeTheme);
        const root = document.documentElement;

        if (effectiveTheme === 'dark') {
            root.classList.add('dark');
            root.style.colorScheme = 'dark';
        } else {
            root.classList.remove('dark');
            root.style.colorScheme = 'light';
        }

        root.setAttribute('data-theme', activeTheme);
        root.setAttribute('data-effective-theme', effectiveTheme);

        this.syncUIElements(activeTheme, effectiveTheme);

        // Dispatch custom event for reactive components (maps, charts, etc.)
        const event = new CustomEvent('nh:theme-changed', {
            detail: {
                theme: activeTheme,
                effectiveTheme: effectiveTheme,
            },
        });
        document.dispatchEvent(event);

        if (showToast) {
            this.showThemeToast(activeTheme, effectiveTheme);
        }
    },

    setTheme(theme, showToast = false) {
        const validThemes = ['light', 'dark', 'system'];
        const chosen = validThemes.includes(theme) ? theme : 'system';

        try {
            localStorage.setItem(THEME_STORAGE_KEY, chosen);
        } catch (e) {
            console.warn('Unable to persist theme preference in localStorage:', e);
        }

        this.applyTheme(chosen, showToast);
    },

    cycleTheme(showToast = true) {
        const current = this.getStoredTheme();
        let next = 'light';
        if (current === 'light') next = 'dark';
        else if (current === 'dark') next = 'system';
        else next = 'light';

        this.setTheme(next, showToast);
    },

    syncUIElements(activeTheme, effectiveTheme) {
        // Sync all theme dropdown items / buttons with [data-theme-option]
        document.querySelectorAll('[data-theme-option]').forEach((btn) => {
            const optionValue = btn.getAttribute('data-theme-option');
            const isSelected = optionValue === activeTheme;

            if (isSelected) {
                btn.setAttribute('aria-selected', 'true');
                btn.classList.add('active-theme-option');
                const checkmark = btn.querySelector('.theme-check-icon');
                if (checkmark) checkmark.classList.remove('hidden');
            } else {
                btn.setAttribute('aria-selected', 'false');
                btn.classList.remove('active-theme-option');
                const checkmark = btn.querySelector('.theme-check-icon');
                if (checkmark) checkmark.classList.add('hidden');
            }
        });

        // Sync all theme preview icons in triggers [data-theme-current-icon]
        document.querySelectorAll('[data-theme-trigger-icon]').forEach((iconContainer) => {
            const sun = iconContainer.querySelector('.theme-icon-sun');
            const moon = iconContainer.querySelector('.theme-icon-moon');
            const system = iconContainer.querySelector('.theme-icon-system');

            if (sun && moon && system) {
                sun.classList.add('hidden');
                moon.classList.add('hidden');
                system.classList.add('hidden');

                if (activeTheme === 'system') {
                    system.classList.remove('hidden');
                } else if (activeTheme === 'dark') {
                    moon.classList.remove('hidden');
                } else {
                    sun.classList.remove('hidden');
                }
            }
        });

        // Sync theme text labels if any [data-theme-label]
        document.querySelectorAll('[data-theme-label]').forEach((label) => {
            const modeNames = {
                light: 'Light Mode',
                dark: 'Dark Mode',
                system: 'System Auto',
            };
            label.textContent = modeNames[activeTheme] || 'Theme';
        });
    },

    showThemeToast(activeTheme, effectiveTheme) {
        const existingToast = document.getElementById('nh-theme-toast');
        if (existingToast) existingToast.remove();

        const icons = {
            light: '☀️',
            dark: '🌙',
            system: '💻',
        };

        const titles = {
            light: 'Light Mode Activated',
            dark: 'Dark Mode Activated',
            system: `System Mode (${effectiveTheme === 'dark' ? 'Dark' : 'Light'})`,
        };

        const toast = document.createElement('div');
        toast.id = 'nh-theme-toast';
        toast.className =
            'fixed bottom-6 right-6 z-[9999] flex items-center gap-2.5 px-4 py-3 rounded-xl bg-slate-900/90 dark:bg-slate-100/90 text-white dark:text-slate-950 backdrop-blur-md shadow-2xl border border-slate-700/50 dark:border-slate-300/50 text-xs font-bold transition-all duration-300 translate-y-2 opacity-0 pointer-events-none';
        toast.innerHTML = `
            <span class="text-base">${icons[activeTheme] || '🎨'}</span>
            <span>${titles[activeTheme] || 'Theme updated'}</span>
            <span class="text-[10px] opacity-60 ml-1 font-mono">Alt+T</span>
        `;

        document.body.appendChild(toast);

        // Animate in
        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-2', 'opacity-0');
        });

        // Animate out
        setTimeout(() => {
            toast.classList.add('translate-y-2', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 1800);
    },

    init() {
        // Immediate sync on load
        this.applyTheme(this.getStoredTheme(), false);

        // Listen for system theme changes
        if (window.matchMedia) {
            const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            const handleChange = () => {
                if (this.getStoredTheme() === 'system') {
                    this.applyTheme('system', false);
                }
            };
            if (mediaQuery.addEventListener) {
                mediaQuery.addEventListener('change', handleChange);
            } else if (mediaQuery.addListener) {
                mediaQuery.addListener(handleChange);
            }
        }

        // Cross-tab theme synchronization
        window.addEventListener('storage', (e) => {
            if (e.key === THEME_STORAGE_KEY) {
                this.applyTheme(e.newValue || 'system', false);
            }
        });

        // Global Keyboard Shortcut: Alt + T or Option + T
        window.addEventListener('keydown', (e) => {
            // Ignore if typing in input, textarea, or contenteditable
            if (
                ['INPUT', 'TEXTAREA', 'SELECT'].includes(e.target.tagName) ||
                e.target.isContentEditable
            ) {
                return;
            }

            if (e.altKey && (e.key === 't' || e.key === 'T')) {
                e.preventDefault();
                this.cycleTheme(true);
            }
        });

        // Event delegation for theme switches & toggle buttons
        document.addEventListener('click', (e) => {
            const optionBtn = e.target.closest('[data-theme-option]');
            if (optionBtn) {
                e.preventDefault();
                const themeChoice = optionBtn.getAttribute('data-theme-option');
                this.setTheme(themeChoice, true);

                return;
            }

            const cycleBtn = e.target.closest('[data-theme-cycle]');
            if (cycleBtn) {
                e.preventDefault();
                this.cycleTheme(true);
                return;
            }

            // Dropped manual dropdown toggling logic as it is now handled by Alpine.js
        });
    },
};

// Expose globally
window.NeedHelpTheme = ThemeEngine;
window.setTheme = (theme) => ThemeEngine.setTheme(theme, true);

// Initialize as soon as DOM is ready or immediately
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => ThemeEngine.init());
} else {
    ThemeEngine.init();
}

export default ThemeEngine;
