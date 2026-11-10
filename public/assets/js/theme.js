/* HostelDock theme toggle — light/dark with localStorage + system preference */
(function () {
    var STORAGE_KEY = 'hosteldock-theme';
    var root = document.documentElement;

    function apply(theme) {
        root.setAttribute('data-bs-theme', theme);
        document.querySelectorAll('.js-theme-toggle').forEach(function (btn) {
            btn.querySelectorAll('.theme-icon-light').forEach(function (icon) {
                icon.classList.toggle('d-none', theme === 'dark');
            });
            btn.querySelectorAll('.theme-icon-dark').forEach(function (icon) {
                icon.classList.toggle('d-none', theme !== 'dark');
            });
        });
    }

    function current() {
        return root.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light';
    }

    var stored = null;
    try {
        stored = localStorage.getItem(STORAGE_KEY);
    } catch (e) {}

    var theme = (stored === 'dark' || stored === 'light')
        ? stored
        : (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

    apply(theme);

    document.addEventListener('click', function (e) {
        var btn = e.target && e.target.closest ? e.target.closest('.js-theme-toggle') : null;
        if (!btn) return;
        var next = current() === 'dark' ? 'light' : 'dark';
        try {
            localStorage.setItem(STORAGE_KEY, next);
        } catch (err) {}
        apply(next);
    });

    if (window.matchMedia) {
        var mq = window.matchMedia('(prefers-color-scheme: dark)');
        var listener = function (e) {
            try {
                if (localStorage.getItem(STORAGE_KEY)) return;
            } catch (err) {}
            apply(e.matches ? 'dark' : 'light');
        };
        if (mq.addEventListener) {
            mq.addEventListener('change', listener);
        } else if (mq.addListener) {
            mq.addListener(listener);
        }
    }

    window.HostelDockTheme = { apply: apply, current: current };
})();