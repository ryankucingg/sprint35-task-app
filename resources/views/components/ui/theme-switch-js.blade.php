<script>
    (function() {
        if (!localStorage.getItem('mrcatz-theme') && localStorage.getItem('theme')) {
            localStorage.setItem('mrcatz-theme', localStorage.getItem('theme'));
            localStorage.removeItem('theme');
        }
        var saved = localStorage.getItem('mrcatz-theme');
        var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        var mode = saved || 'system';
        if (mode !== 'mrcatz-dark' && mode !== 'mrcatz-light' && mode !== 'system') {
            mode = 'system';
            localStorage.setItem('mrcatz-theme', 'system');
        }
        var theme = mode === 'system' ? (prefersDark ? 'mrcatz-dark' : 'mrcatz-light') : mode;
        document.documentElement.setAttribute('data-theme', theme);
    })();

    function getThemeMode() {
        return localStorage.getItem('mrcatz-theme') || 'system';
    }

    function resolveTheme(mode) {
        if (mode === 'system') {
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'mrcatz-dark' : 'mrcatz-light';
        }
        return mode;
    }

    function applyTheme(mode) {
        document.documentElement.setAttribute('data-theme', resolveTheme(mode));
        document.querySelectorAll('.theme-trigger-icon').forEach(function(c) {
            c.querySelectorAll('[data-icon-for]').forEach(function(icon) {
                icon.classList.toggle('hidden', icon.dataset.iconFor !== mode);
            });
        });
        document.querySelectorAll('[data-theme-value]').forEach(function(el) {
            el.classList.toggle('font-semibold', el.dataset.themeValue === mode);
            el.classList.toggle('bg-base-200', el.dataset.themeValue === mode);
        });
    }

    function setTheme(mode) {
        localStorage.setItem('mrcatz-theme', mode);
        applyTheme(mode);
        document.querySelectorAll('.theme-dropdown-menu').forEach(function(el) {
            el.classList.add('hidden');
        });
    }

    function toggleThemeDropdown(btn) {
        var menu = btn.parentElement.querySelector('.theme-dropdown-menu');
        document.querySelectorAll('.theme-dropdown-menu').forEach(function(el) {
            if (el !== menu) el.classList.add('hidden');
        });
        menu.classList.toggle('hidden');
    }

    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function() {
        if (getThemeMode() === 'system') applyTheme('system');
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.theme-dropdown-wrapper')) {
            document.querySelectorAll('.theme-dropdown-menu').forEach(function(el) {
                el.classList.add('hidden');
            });
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        applyTheme(getThemeMode());
    });
</script>
