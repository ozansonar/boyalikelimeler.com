(function () {
    'use strict';

    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var activeItemId = null;
    var activeItemType = null;

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

    function getBaseUrl(type) {
        if (type === 'question') return '/admin/soz-meydani/sorular/';
        if (type === 'answer') return '/admin/soz-meydani/cevaplar/';
        return '/admin/soz-meydani/kategoriler/';
    }

    // ─── Approve ───
    window.openQnaApproveModal = function (id, name, type) {
        activeItemId = id;
        activeItemType = type;
        var nameEl = document.getElementById('qnaApproveName');
        if (nameEl) nameEl.textContent = name;
        openBsModal('qnaApproveModal');
    };

    var approveBtn = document.getElementById('qnaApproveBtn');
    if (approveBtn) {
        approveBtn.addEventListener('click', function () {
            if (!activeItemId) return;
            closeBsModal('qnaApproveModal');
            ajaxAction(getBaseUrl(activeItemType) + activeItemId + '/onayla', 'PATCH', 'Başarıyla onaylandı.');
            activeItemId = null;
        });
    }

    // ─── Reject ───
    window.openQnaRejectModal = function (id, name, type) {
        activeItemId = id;
        activeItemType = type;
        var nameEl = document.getElementById('qnaRejectName');
        if (nameEl) nameEl.textContent = name;
        openBsModal('qnaRejectModal');
    };

    var rejectBtn = document.getElementById('qnaRejectBtn');
    if (rejectBtn) {
        rejectBtn.addEventListener('click', function () {
            if (!activeItemId) return;
            closeBsModal('qnaRejectModal');
            ajaxAction(getBaseUrl(activeItemType) + activeItemId + '/reddet', 'PATCH', 'Başarıyla reddedildi.');
            activeItemId = null;
        });
    }

    // ─── Delete (QnA items) ───
    window.openQnaDeleteModal = function (id, name, type) {
        activeItemId = id;
        activeItemType = type;
        var nameEl = document.getElementById('qnaDeleteName');
        if (nameEl) nameEl.textContent = name;
        openBsModal('qnaDeleteModal');
    };

    var deleteQnaBtn = document.getElementById('qnaDeleteBtn');
    if (deleteQnaBtn) {
        deleteQnaBtn.addEventListener('click', function () {
            if (!activeItemId) return;
            closeBsModal('qnaDeleteModal');
            ajaxAction(getBaseUrl(activeItemType) + activeItemId, 'DELETE', 'Başarıyla silindi.');
            activeItemId = null;
        });
    }

    // ─── Delete (Category) ───
    window.openDeleteModal = function (id, name) {
        activeItemId = id;
        activeItemType = 'category';
        var nameEl = document.getElementById('deleteItemName');
        if (nameEl) nameEl.textContent = name;
        openBsModal('deleteModal');
    };

    var deleteCatBtn = document.getElementById('deleteBtn');
    if (deleteCatBtn) {
        deleteCatBtn.addEventListener('click', function () {
            if (!activeItemId) return;
            closeBsModal('deleteModal');
            ajaxAction('/admin/soz-meydani/kategoriler/' + activeItemId, 'DELETE', 'Kategori başarıyla silindi.');
            activeItemId = null;
        });
    }

})();
