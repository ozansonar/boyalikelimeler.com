(function () {
    'use strict';

    var activeMessageId = null;
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    /* ==================== HELPERS ==================== */
    function apiRequest(url, method, data) {
        var options = {
            method: method || 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        };
        if (data) options.body = JSON.stringify(data);
        return fetch(url, options).then(function (r) { return r.json(); });
    }

    function showToast(message, type) {
        if (window.showToast && window.showToast !== showToast) {
            window.showToast(message, type);
            return;
        }
        type = type || 'success';
        var existing = document.querySelector('.ca-toast');
        if (existing) existing.remove();
        var icons = { success: 'bi-check-circle-fill', danger: 'bi-x-circle-fill', warning: 'bi-exclamation-triangle-fill', info: 'bi-info-circle-fill' };
        var colors = { success: 'text-neon-green', danger: 'text-neon-red', warning: 'text-neon-orange', info: 'text-neon-blue' };
        var toast = document.createElement('div');
        toast.className = 'ca-toast ca-toast-' + type;
        toast.innerHTML = '<i class="bi ' + (icons[type] || icons.info) + ' ' + (colors[type] || '') + '"></i><span>' + message + '</span><button onclick="this.parentElement.remove()"><i class="bi bi-x"></i></button>';
        document.body.appendChild(toast);
        requestAnimationFrame(function () { toast.classList.add('show'); });
        setTimeout(function () { toast.classList.remove('show'); setTimeout(function () { toast.remove(); }, 350); }, 3500);
    }

    /* ==================== OPEN MESSAGE ==================== */
    window.openMessage = function (id) {
        activeMessageId = id;

        var item = document.querySelector('.msg-item[data-id="' + id + '"]');
        document.querySelectorAll('.msg-item').forEach(function (i) { i.classList.remove('active'); });
        if (item) {
            item.classList.add('active');
            item.classList.remove('unread');
        }

        apiRequest('/admin/contacts/' + id).then(function (data) {
            if (data.error) { showToast(data.error, 'danger'); return; }

            document.getElementById('detailSubject').textContent = data.subject_label;
            document.getElementById('detailSender').textContent = data.name;
            document.getElementById('detailEmail').textContent = '<' + data.email + '>';
            document.getElementById('detailDate').textContent = data.created_at;
            document.getElementById('detailText').innerHTML = data.message;

            var avatar = document.getElementById('detailAvatar');
            avatar.textContent = data.initials;
            avatar.className = 'msg-detail-sender-avatar msg-avatar--' + data.subject_color;

            // Reply section
            var replySection = document.getElementById('detailReplySection');
            if (data.reply_body) {
                replySection.classList.remove('d-none');
                document.getElementById('detailReplyMeta').textContent = data.replied_by + ' — ' + data.replied_at;
                document.getElementById('detailReplyBody').innerHTML = data.reply_body.replace(/\n/g, '<br>');
            } else {
                replySection.classList.add('d-none');
            }

            document.getElementById('quickReplyText').value = '';
            document.getElementById('msgDetailContent').classList.remove('d-none');
            document.getElementById('msgDetailEmpty').classList.add('d-none');
            document.getElementById('msgListPanel').classList.add('detail-open');
        });
    };

    window.closeDetail = function () {
        document.getElementById('msgListPanel').classList.remove('detail-open');
        document.querySelectorAll('.msg-item').forEach(function (i) { i.classList.remove('active'); });
        document.getElementById('msgDetailContent').classList.add('d-none');
        document.getElementById('msgDetailEmpty').classList.remove('d-none');
        activeMessageId = null;
    };

    /* ==================== STAR ==================== */
    window.toggleStar = function (btn, id) {
        apiRequest('/admin/contacts/' + id + '/star', 'PATCH').then(function (data) {
            if (data.success) {
                btn.classList.toggle('active');
                var icon = btn.querySelector('i');
                icon.className = data.is_starred ? 'bi bi-star-fill' : 'bi bi-star';
            }
        });
    };

    /* ==================== ARCHIVE ==================== */
    window.archiveMsg = function (id) {
        apiRequest('/admin/contacts/' + id + '/archive', 'PATCH').then(function (data) {
            if (data.success) {
                var item = document.querySelector('.msg-item[data-id="' + id + '"]');
                if (item) {
                    item.classList.add('msg-item--archiving');
                    setTimeout(function () { item.remove(); }, 350);
                }
                if (activeMessageId === id) closeDetail();
                showToast(data.message, 'info');
            }
        });
    };

    window.archiveDetail = function () {
        if (activeMessageId) archiveMsg(activeMessageId);
    };

    /* ==================== DELETE ==================== */
    window.deleteMsg = function (id) {
        apiRequest('/admin/contacts/' + id, 'DELETE').then(function (data) {
            if (data.success) {
                var item = document.querySelector('.msg-item[data-id="' + id + '"]');
                if (item) {
                    item.classList.add('msg-item--removing');
                    setTimeout(function () { item.remove(); }, 350);
                }
                if (activeMessageId === id) closeDetail();
                showToast(data.message, 'success');
            }
        });
    };

    window.deleteDetail = function () {
        if (activeMessageId) deleteMsg(activeMessageId);
    };

    /* ==================== MARK ALL READ ==================== */
    var markAllBtn = document.getElementById('markAllReadBtn');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function () {
            apiRequest('/admin/contacts/mark-all-read', 'PATCH').then(function (data) {
                if (data.success) {
                    document.querySelectorAll('.msg-item.unread').forEach(function (el) { el.classList.remove('unread'); });
                    showToast(data.message, 'success');
                }
            });
        });
    }

    /* ==================== SEND REPLY ==================== */
    var sendReplyBtn = document.getElementById('sendReplyBtn');
    if (sendReplyBtn) {
        sendReplyBtn.addEventListener('click', function () {
            if (!activeMessageId) return;
            var textarea = document.getElementById('quickReplyText');
            var text = textarea.value.trim();
            if (!text) { showToast('Lütfen yanıtınızı yazın', 'warning'); return; }

            sendReplyBtn.disabled = true;
            sendReplyBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Gönderiliyor...';

            apiRequest('/admin/contacts/' + activeMessageId + '/reply', 'POST', { reply_body: text })
                .then(function (data) {
                    if (data.success) {
                        textarea.value = '';
                        showToast(data.message, 'success');

                        // Show reply in detail
                        var replySection = document.getElementById('detailReplySection');
                        replySection.classList.remove('d-none');
                        document.getElementById('detailReplyMeta').textContent = data.replied_by + ' — ' + data.replied_at;
                        document.getElementById('detailReplyBody').innerHTML = text.replace(/\n/g, '<br>');

                        // Add reply icon to list item
                        var item = document.querySelector('.msg-item[data-id="' + activeMessageId + '"]');
                        if (item) {
                            var subject = item.querySelector('.msg-subject');
                            if (subject && !subject.querySelector('.bi-reply-fill')) {
                                subject.insertAdjacentHTML('beforeend', ' <i class="bi bi-reply-fill text-neon-green ms-1" title="Yanıtlandı"></i>');
                            }
                        }
                    } else if (data.errors) {
                        var firstError = Object.values(data.errors)[0];
                        showToast(Array.isArray(firstError) ? firstError[0] : firstError, 'danger');
                    }
                })
                .finally(function () {
                    sendReplyBtn.disabled = false;
                    sendReplyBtn.innerHTML = '<i class="bi bi-send me-1"></i> Gönder';
                });
        });
    }

})();
