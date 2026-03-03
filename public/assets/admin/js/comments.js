(function () {
    'use strict';

    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

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
                if (typeof showToast === 'function') showToast(data.message || successMsg, 'success');
                setTimeout(function () { window.location.reload(); }, 800);
            } else {
                if (typeof showToast === 'function') showToast(data.message || 'Bir hata oluştu.', 'danger');
            }
        })
        .catch(function () {
            if (typeof showToast === 'function') showToast('Bir hata oluştu.', 'danger');
        });
    }

    window.approveComment = function (id) {
        if (!confirm('Bu yorumu onaylamak istediğinize emin misiniz?')) return;
        ajaxAction('/admin/comments/' + id + '/approve', 'PATCH', 'Yorum onaylandı.');
    };

    window.rejectComment = function (id) {
        if (!confirm('Bu yorumu reddetmek istediğinize emin misiniz?')) return;
        ajaxAction('/admin/comments/' + id + '/reject', 'PATCH', 'Yorum reddedildi.');
    };

    window.deleteComment = function (id, name) {
        openDeleteModal(id, name, '/admin/comments/' + id);
    };

})();
