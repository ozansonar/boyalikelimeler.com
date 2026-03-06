/**
 * Admin Profile Page JS
 * Panel switching, password strength, avatar upload
 */

/* ==================== Panel Switching ==================== */
function switchProfilePanel(btn, panelId) {
    document.querySelectorAll('.stg-nav-item').forEach(function (n) { n.classList.remove('active'); });
    document.querySelectorAll('.stg-panel').forEach(function (p) { p.classList.remove('active'); });

    if (btn) { btn.classList.add('active'); }
    else {
        document.querySelectorAll('.stg-nav-item').forEach(function (n) {
            if (n.getAttribute('href') === '#' + panelId) { n.classList.add('active'); }
        });
    }

    var panel = document.getElementById(panelId);
    if (panel) { panel.classList.add('active'); }
}

document.addEventListener('DOMContentLoaded', function () {

    /* ==================== Password Toggle ==================== */
    document.querySelectorAll('.ap-toggle-pw').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var targetId = btn.dataset.target;
            var input = document.getElementById(targetId);
            if (!input) return;
            var isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            btn.querySelector('i').className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    });

    /* ==================== Password Strength ==================== */
    var newPwInput = document.getElementById('apNewPassword');
    var confirmPwInput = document.getElementById('apConfirmPassword');
    var submitBtn = document.getElementById('passwordSubmitBtn');

    if (newPwInput) {
        newPwInput.addEventListener('input', function () {
            checkPasswordStrength(this.value);
            checkPasswordMatch();
        });
    }

    if (confirmPwInput) {
        confirmPwInput.addEventListener('input', checkPasswordMatch);
    }

    function checkPasswordStrength(pw) {
        var bars = [
            document.getElementById('pwBar1'),
            document.getElementById('pwBar2'),
            document.getElementById('pwBar3'),
            document.getElementById('pwBar4')
        ];
        var textEl = document.getElementById('pwStrengthText');

        var checks = {
            length:  pw.length >= 8,
            lower:   /[a-z]/.test(pw),
            upper:   /[A-Z]/.test(pw),
            number:  /[0-9]/.test(pw),
            special: /[@$!%*?&#._\-]/.test(pw)
        };

        updateRequirement('reqLength', checks.length);
        updateRequirement('reqLower', checks.lower);
        updateRequirement('reqUpper', checks.upper);
        updateRequirement('reqNumber', checks.number);
        updateRequirement('reqSpecial', checks.special);

        var score = Object.values(checks).filter(Boolean).length;

        bars.forEach(function (bar) {
            bar.className = 'ap-pw-meter__bar';
        });

        if (pw.length === 0) {
            textEl.textContent = '';
            textEl.className = 'ap-pw-meter__text';
            return;
        }

        var level = '';
        var color = '';

        if (score <= 2) {
            level = 'Zayıf';
            color = 'weak';
        } else if (score === 3) {
            level = 'Orta';
            color = 'medium';
        } else if (score === 4) {
            level = 'Güçlü';
            color = 'strong';
        } else {
            level = 'Çok Güçlü';
            color = 'very-strong';
        }

        var activeBars = Math.min(score, 4);
        for (var i = 0; i < activeBars; i++) {
            bars[i].classList.add('active', color);
        }

        textEl.textContent = level;
        textEl.className = 'ap-pw-meter__text ap-pw-meter__text--' + color;
    }

    function updateRequirement(id, passed) {
        var el = document.getElementById(id);
        if (!el) return;
        if (passed) {
            el.classList.add('ap-pw-checklist__item--pass');
            el.classList.remove('ap-pw-checklist__item--fail');
            el.querySelector('i').className = 'bi bi-check-circle-fill';
        } else {
            el.classList.remove('ap-pw-checklist__item--pass');
            el.classList.add('ap-pw-checklist__item--fail');
            el.querySelector('i').className = 'bi bi-circle';
        }
    }

    function checkPasswordMatch() {
        var pw = document.getElementById('apNewPassword').value;
        var confirm = document.getElementById('apConfirmPassword').value;
        var matchEl = document.getElementById('pwMatchStatus');
        var currentPw = document.getElementById('apCurrentPassword').value;

        if (confirm.length === 0) {
            matchEl.textContent = '';
            matchEl.className = 'ap-pw-match mt-1';
            updateSubmitButton(false);
            return;
        }

        if (pw === confirm) {
            matchEl.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Şifreler eşleşiyor';
            matchEl.className = 'ap-pw-match mt-1 ap-pw-match--ok';
        } else {
            matchEl.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i> Şifreler eşleşmiyor';
            matchEl.className = 'ap-pw-match mt-1 ap-pw-match--fail';
        }

        var checks = {
            length:  pw.length >= 8,
            lower:   /[a-z]/.test(pw),
            upper:   /[A-Z]/.test(pw),
            number:  /[0-9]/.test(pw),
            special: /[@$!%*?&#._\-]/.test(pw)
        };

        var allPassed = Object.values(checks).every(Boolean);
        updateSubmitButton(allPassed && pw === confirm && currentPw.length > 0);
    }

    function updateSubmitButton(enabled) {
        if (submitBtn) {
            submitBtn.disabled = !enabled;
        }
    }

    var currentPwInput = document.getElementById('apCurrentPassword');
    if (currentPwInput) {
        currentPwInput.addEventListener('input', checkPasswordMatch);
    }

    /* ==================== Avatar Upload (AJAX) ==================== */
    var avatarInput = document.getElementById('avatarInput');
    var avatarFeedback = document.getElementById('avatarFeedback');
    var csrfToken = document.querySelector('meta[name="csrf-token"]');

    if (avatarInput) {
        avatarInput.addEventListener('change', function () {
            var file = this.files[0];
            if (!file) return;

            if (file.size > 2 * 1024 * 1024) {
                showAvatarFeedback('Dosya boyutu 2 MB\'dan büyük olamaz.', 'danger');
                this.value = '';
                return;
            }

            var allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (allowedTypes.indexOf(file.type) === -1) {
                showAvatarFeedback('Sadece JPEG, PNG ve WebP formatları desteklenir.', 'danger');
                this.value = '';
                return;
            }

            var formData = new FormData();
            formData.append('avatar', file);

            showAvatarFeedback('<i class="bi bi-arrow-repeat ap-spin me-1"></i> Yükleniyor...', 'info');

            fetch(avatarInput.dataset.url || '/admin/profile/avatar', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken ? csrfToken.content : '',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success) {
                    showAvatarFeedback('<i class="bi bi-check-circle-fill me-1"></i> ' + data.message, 'success');
                    var preview = document.getElementById('avatarPreview');
                    var initials = document.getElementById('avatarInitials');
                    if (initials) initials.remove();
                    var existingImg = document.getElementById('avatarImg');
                    if (existingImg) {
                        existingImg.src = data.avatar_url + '?t=' + Date.now();
                    } else {
                        var img = document.createElement('img');
                        img.src = data.avatar_url + '?t=' + Date.now();
                        img.alt = 'Avatar';
                        img.className = 'ap-avatar-img';
                        img.id = 'avatarImg';
                        preview.appendChild(img);
                    }

                    if (!document.getElementById('removeAvatarBtn')) {
                        var removeBtn = document.createElement('button');
                        removeBtn.type = 'button';
                        removeBtn.className = 'stg-btn stg-btn-danger';
                        removeBtn.id = 'removeAvatarBtn';
                        removeBtn.innerHTML = '<i class="bi bi-trash3 me-1"></i> Kaldır';
                        var actionsDiv = document.querySelector('.ap-avatar-actions .d-flex');
                        if (actionsDiv) actionsDiv.appendChild(removeBtn);
                        bindRemoveAvatar();
                    }

                    updateSidebarAvatar(data.avatar_url);
                } else {
                    showAvatarFeedback('<i class="bi bi-exclamation-circle-fill me-1"></i> Yükleme başarısız.', 'danger');
                }
            })
            .catch(function () {
                showAvatarFeedback('<i class="bi bi-exclamation-circle-fill me-1"></i> Bir hata oluştu.', 'danger');
            });

            this.value = '';
        });
    }

    function bindRemoveAvatar() {
        var removeBtn = document.getElementById('removeAvatarBtn');
        if (!removeBtn) return;
        removeBtn.addEventListener('click', function () {
            if (!confirm('Profil fotoğrafını kaldırmak istediğinize emin misiniz?')) return;

            fetch('/admin/profile/avatar', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken ? csrfToken.content : '',
                    'Accept': 'application/json'
                }
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success) {
                    showAvatarFeedback('<i class="bi bi-check-circle-fill me-1"></i> ' + data.message, 'success');
                    var preview = document.getElementById('avatarPreview');
                    var img = document.getElementById('avatarImg');
                    if (img) img.remove();

                    if (!document.getElementById('avatarInitials')) {
                        var nameEl = document.querySelector('.ap-avatar-actions h6');
                        var name = nameEl ? nameEl.textContent.trim() : 'U';
                        var initials = name.substring(0, 2).toUpperCase();
                        var span = document.createElement('span');
                        span.className = 'ap-avatar-initials';
                        span.id = 'avatarInitials';
                        span.textContent = initials;
                        preview.appendChild(span);
                    }

                    removeBtn.remove();
                    updateSidebarAvatar(null);
                }
            })
            .catch(function () {
                showAvatarFeedback('<i class="bi bi-exclamation-circle-fill me-1"></i> Bir hata oluştu.', 'danger');
            });
        });
    }

    bindRemoveAvatar();

    function showAvatarFeedback(msg, type) {
        if (!avatarFeedback) return;
        avatarFeedback.innerHTML = msg;
        avatarFeedback.className = 'ap-avatar-feedback mt-2 ap-avatar-feedback--' + type;
    }

    function updateSidebarAvatar(url) {
        var sidebarAvatar = document.querySelector('.sidebar-user-avatar');
        if (!sidebarAvatar) return;
        if (url) {
            sidebarAvatar.innerHTML = '<img src="' + url + '?t=' + Date.now() + '" alt="Avatar" class="ap-sidebar-avatar-img">';
        } else {
            var nameEl = document.querySelector('.ap-avatar-actions h6');
            var name = nameEl ? nameEl.textContent.trim() : 'U';
            sidebarAvatar.textContent = name.substring(0, 2).toUpperCase();
        }
    }

    /* ==================== Hash-based panel activation ==================== */
    var hash = window.location.hash.replace('#', '');
    if (hash && document.getElementById(hash)) {
        switchProfilePanel(null, hash);
    }
});
