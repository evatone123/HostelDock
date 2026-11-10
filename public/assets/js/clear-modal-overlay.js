(function () {
    'use strict';

    window.clearStuckModalOverlay = function (modalId) {
        const modalEl = modalId ? document.getElementById(modalId) : null;

        if (modalEl && window.bootstrap && window.bootstrap.Modal) {
            const instance = window.bootstrap.Modal.getInstance(modalEl);

            if (instance) {
                instance.hide();
            }
        }

        document.querySelectorAll('.modal-backdrop').forEach((backdrop) => backdrop.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    };
})();
