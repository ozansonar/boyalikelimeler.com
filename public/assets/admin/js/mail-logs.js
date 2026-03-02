/**
 * Mail Logs Page JS
 * Counter animation, delete modal
 */

// ── Counter Animation ──
function animateCounters() {
    document.querySelectorAll('[data-count]').forEach(function (el) {
        var target = parseInt(el.getAttribute('data-count'), 10);
        if (isNaN(target) || target === 0) {
            el.textContent = '0';
            return;
        }

        var start = 0;
        var duration = 1200;
        var startTime = null;

        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            var progress = Math.min((timestamp - startTime) / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.floor(eased * target).toLocaleString('tr-TR');
            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                el.textContent = target.toLocaleString('tr-TR');
            }
        }

        requestAnimationFrame(step);
    });
}

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

// ── Init ──
document.addEventListener('DOMContentLoaded', function () {
    animateCounters();

    // AOS
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 600,
            easing: 'ease-out-cubic',
            once: true,
            offset: 50
        });
    }
});
