/**
 * My Posts Page JS
 * - Delete modal handling
 * - Unpublish modal handling
 * - Search submit on enter
 */
(function () {
    'use strict';

    /* -------------------------------------------------------
       Delete Modal
    ------------------------------------------------------- */
    window.openDeleteModal = function (id, title) {
        var modal = document.getElementById('deleteConfirmModal');
        var form = document.getElementById('deleteForm');
        var nameEl = document.getElementById('deleteItemName');

        if (nameEl) nameEl.textContent = title || '';
        if (form) form.action = '/yazilarim/' + id;
        if (modal) {
            var bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        }
    };

    /* -------------------------------------------------------
       Unpublish Modal
    ------------------------------------------------------- */
    window.openUnpublishModal = function (id, title) {
        var modal = document.getElementById('unpublishConfirmModal');
        var form = document.getElementById('unpublishForm');
        var nameEl = document.getElementById('unpublishItemName');

        if (nameEl) nameEl.textContent = title || '';
        if (form) form.action = '/yazilarim/' + id + '/yayindan-kaldir';
        if (modal) {
            var bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        }
    };

    /* -------------------------------------------------------
       Search on Enter
    ------------------------------------------------------- */
    var searchInput = document.getElementById('postSearch');
    if (searchInput) {
        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                this.closest('form').submit();
            }
        });
    }

})();
