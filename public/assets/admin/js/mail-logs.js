/**
 * Mail Logs Page JS
 * Delete modal
 */

// ── Delete Modal ──
function openDeleteModal(id, subject) {
    var modal = document.getElementById('deleteModal');
    var form = document.getElementById('deleteForm');
    var nameEl = document.getElementById('deleteItemName');

    if (form) {
        var baseUrl = window.location.pathname.replace(/\/+$/, '');
        form.action = '/admin/mail-logs/' + id;
    }
    if (nameEl) {
        nameEl.textContent = subject || 'Bu kayıt';
    }

    var bsModal = new bootstrap.Modal(modal);
    bsModal.show();
}

