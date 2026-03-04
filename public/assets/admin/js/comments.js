(function () {
    'use strict';

    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var activeCommentId = null;

    // ─── AJAX helper ───
    function ajaxAction(url, method, successMsg) {
        fetch(url, {
            method: method,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                showToast(data.message || successMsg, 'success');
                setTimeout(function () { window.location.reload(); }, 800);
            } else {
                showToast(data.message || 'Bir hata oluştu.', 'danger');
            }
        })
        .catch(function () {
            showToast('Bir hata oluştu. Lütfen tekrar deneyin.', 'danger');
        });
    }

    function openBsModal(id) {
        var el = document.getElementById(id);
        if (el) {
            var modal = new bootstrap.Modal(el);
            modal.show();
        }
    }

    function closeBsModal(id) {
        var el = document.getElementById(id);
        if (el) {
            var modal = bootstrap.Modal.getInstance(el);
            if (modal) modal.hide();
        }
    }

    // ─── Approve ───
    window.openApproveModal = function (id, name) {
        activeCommentId = id;
        var nameEl = document.getElementById('approveCommentName');
        if (nameEl) nameEl.textContent = name;
        openBsModal('approveCommentModal');
    };

    var approveBtn = document.getElementById('approveCommentBtn');
    if (approveBtn) {
        approveBtn.addEventListener('click', function () {
            if (!activeCommentId) return;
            closeBsModal('approveCommentModal');
            ajaxAction('/admin/comments/' + activeCommentId + '/approve', 'PATCH', 'Yorum başarıyla onaylandı.');
            activeCommentId = null;
        });
    }

    // ─── Reject ───
    window.openRejectModal = function (id, name) {
        activeCommentId = id;
        var nameEl = document.getElementById('rejectCommentName');
        if (nameEl) nameEl.textContent = name;
        openBsModal('rejectCommentModal');
    };

    var rejectBtn = document.getElementById('rejectCommentBtn');
    if (rejectBtn) {
        rejectBtn.addEventListener('click', function () {
            if (!activeCommentId) return;
            closeBsModal('rejectCommentModal');
            ajaxAction('/admin/comments/' + activeCommentId + '/reject', 'PATCH', 'Yorum reddedildi.');
            activeCommentId = null;
        });
    }

    // ─── Delete ───
    window.openDeleteCommentModal = function (id, name) {
        activeCommentId = id;
        var nameEl = document.getElementById('deleteCommentName');
        if (nameEl) nameEl.textContent = name;
        openBsModal('deleteCommentModal');
    };

    var deleteBtn = document.getElementById('deleteCommentBtn');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function () {
            if (!activeCommentId) return;
            closeBsModal('deleteCommentModal');
            ajaxAction('/admin/comments/' + activeCommentId, 'DELETE', 'Yorum başarıyla silindi.');
            activeCommentId = null;
        });
    }

})();
