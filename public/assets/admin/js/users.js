/* ============================================================
   USERS LIST — Admin User Management JS
   ============================================================ */

function openDeleteModal(userId, userName) {
    document.getElementById('deleteUserName').textContent = userName;
    document.getElementById('deleteForm').action = '/admin/users/' + userId;
    var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function changePerPage(value) {
    var url = new URL(window.location.href);
    url.searchParams.set('per_page', value);
    url.searchParams.delete('page');
    window.location.href = url.toString();
}

/* Counter Animation */
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    var counters = document.querySelectorAll('[data-count]');

    counters.forEach(function (el) {
        var target = parseInt(el.getAttribute('data-count'), 10);
        if (isNaN(target) || target === 0) {
            el.textContent = '0';
            return;
        }

        var duration = 1200;
        var start = 0;
        var startTime = null;

        function animate(timestamp) {
            if (!startTime) startTime = timestamp;
            var progress = Math.min((timestamp - startTime) / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3);
            var current = Math.floor(eased * target);
            el.textContent = current.toLocaleString('tr-TR');

            if (progress < 1) {
                requestAnimationFrame(animate);
            } else {
                el.textContent = target.toLocaleString('tr-TR');
            }
        }

        requestAnimationFrame(animate);
    });

});
