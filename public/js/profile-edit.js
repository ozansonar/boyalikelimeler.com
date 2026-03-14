/**
 * Profile Edit Page JS
 * - Section navigation
 * - Bio character counter
 * - Cover/Avatar photo AJAX upload
 * - Interest checkbox toggle
 * - Password toggle
 * - Save bar visibility
 */
(function () {
    'use strict';

    var csrfToken = document.querySelector('meta[name="csrf-token"]');

    /* -------------------------------------------------------
       Section Navigation
    ------------------------------------------------------- */
    var navItems = document.querySelectorAll('.pedit-nav__item');
    var panels = document.querySelectorAll('.pedit-section-panel');

    navItems.forEach(function (item) {
        item.addEventListener('click', function () {
            navItems.forEach(function (n) { n.classList.remove('pedit-nav__item--active'); });
            panels.forEach(function (p) { p.classList.add('d-none'); });

            this.classList.add('pedit-nav__item--active');
            var target = this.getAttribute('data-target');
            var panel = document.getElementById(target);
            if (panel) panel.classList.remove('d-none');
        });
    });

    /* -------------------------------------------------------
       Bio Character Counter
    ------------------------------------------------------- */
    var bioInput = document.getElementById('pf_bio');
    var bioCount = document.getElementById('bioCharCount');
    if (bioInput && bioCount) {
        bioCount.textContent = bioInput.value.length;
        bioInput.addEventListener('input', function () {
            bioCount.textContent = this.value.length;
        });
    }

    /* -------------------------------------------------------
       Cover Photo Upload (AJAX)
    ------------------------------------------------------- */
    var MAX_FILE_SIZE = 1024 * 1024; // 1 MB

    var coverInput = document.getElementById('coverInput');
    var coverImg = document.getElementById('coverPreviewImg');
    if (coverInput && coverImg) {
        coverInput.addEventListener('change', function () {
            var file = this.files[0];
            if (!file) return;

            if (file.size > MAX_FILE_SIZE) {
                if (window.BkModal) window.BkModal.danger('Kapak resmi en fazla 1 MB olabilir.');
                this.value = '';
                return;
            }

            /* Preview */
            var reader = new FileReader();
            reader.onload = function (e) { coverImg.src = e.target.result; };
            reader.readAsDataURL(file);

            /* AJAX upload */
            var formData = new FormData();
            formData.append('cover', file);

            fetch('/profil/kapak', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken ? csrfToken.content : '',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(function (r) {
                if (!r.ok) return r.json().then(function (d) { throw d; });
                return r.json();
            })
            .then(function (data) {
                if (data.success && data.url) {
                    coverImg.src = data.url;
                    if (window.BkModal) window.BkModal.success('Kapak fotoğrafı güncellendi.');
                } else if (data.message) {
                    if (window.BkModal) window.BkModal.danger(data.message);
                }
            })
            .catch(function (err) {
                var msg = 'Kapak fotoğrafı yüklenirken bir hata oluştu.';
                if (err && err.errors && err.errors.cover) msg = err.errors.cover[0];
                else if (err && err.message) msg = err.message;
                if (window.BkModal) window.BkModal.danger(msg);
            });
        });
    }

    /* -------------------------------------------------------
       Avatar Photo Upload (AJAX)
    ------------------------------------------------------- */
    var avatarInput = document.getElementById('avatarInput');
    var avatarImg = document.getElementById('avatarPreviewImg');
    if (avatarInput && avatarImg) {
        avatarInput.addEventListener('change', function () {
            var file = this.files[0];
            if (!file) return;

            if (file.size > MAX_FILE_SIZE) {
                if (window.BkModal) window.BkModal.danger('Profil resmi en fazla 1 MB olabilir.');
                this.value = '';
                return;
            }

            /* Preview */
            var reader = new FileReader();
            reader.onload = function (e) { avatarImg.src = e.target.result; };
            reader.readAsDataURL(file);

            /* AJAX upload */
            var formData = new FormData();
            formData.append('avatar', file);

            fetch('/profil/avatar', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken ? csrfToken.content : '',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(function (r) {
                if (!r.ok) return r.json().then(function (d) { throw d; });
                return r.json();
            })
            .then(function (data) {
                if (data.success && data.url) {
                    avatarImg.src = data.url;
                    if (window.BkModal) window.BkModal.success('Profil fotoğrafı güncellendi.');
                } else if (data.message) {
                    if (window.BkModal) window.BkModal.danger(data.message);
                }
            })
            .catch(function (err) {
                var msg = 'Profil fotoğrafı yüklenirken bir hata oluştu.';
                if (err && err.errors && err.errors.avatar) msg = err.errors.avatar[0];
                else if (err && err.message) msg = err.message;
                if (window.BkModal) window.BkModal.danger(msg);
            });
        });
    }

    /* -------------------------------------------------------
       Username Availability Check (AJAX)
    ------------------------------------------------------- */
    var usernameInput = document.getElementById('pf_username');
    var usernameStatus = document.getElementById('usernameStatus');
    var usernameTimer = null;

    if (usernameInput && usernameStatus) {
        var currentUsername = usernameInput.value.trim();

        usernameInput.addEventListener('input', function () {
            clearTimeout(usernameTimer);
            var val = usernameInput.value.trim();

            usernameStatus.classList.add('d-none');
            usernameStatus.textContent = '';

            if (val.length < 3) return;
            if (!/^[a-zA-Z0-9_]+$/.test(val)) {
                usernameStatus.classList.remove('d-none');
                usernameStatus.className = 'pedit-form__username-status pedit-form__username-status--error';
                usernameStatus.innerHTML = '<i class="fa-solid fa-xmark me-1"></i>Sadece İngilizce harf, rakam ve alt çizgi kullanılabilir.';
                return;
            }

            if (val === currentUsername) return;

            usernameTimer = setTimeout(function () {
                var userId = document.querySelector('meta[name="user-id"]');
                var body = { username: val };
                if (userId) body.ignore_id = userId.content;

                fetch('/check-username', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken ? csrfToken.content : '',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(body)
                })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    usernameStatus.classList.remove('d-none');
                    if (data.available) {
                        usernameStatus.className = 'pedit-form__username-status pedit-form__username-status--success';
                        usernameStatus.innerHTML = '<i class="fa-solid fa-check me-1"></i>Bu kullanıcı adı kullanılabilir.';
                    } else {
                        usernameStatus.className = 'pedit-form__username-status pedit-form__username-status--error';
                        usernameStatus.innerHTML = '<i class="fa-solid fa-xmark me-1"></i>Bu kullanıcı adı zaten kullanılıyor.';
                    }
                })
                .catch(function () {});
            }, 500);
        });
    }

    /* -------------------------------------------------------
       Interest Checkbox Toggle
    ------------------------------------------------------- */
    document.querySelectorAll('.pedit-interest__item input').forEach(function (cb) {
        cb.addEventListener('change', function () {
            this.closest('.pedit-interest__item').classList.toggle('pedit-interest__item--active', this.checked);
        });
    });

    /* -------------------------------------------------------
       Password Toggle
    ------------------------------------------------------- */
    document.querySelectorAll('.pedit-form__eye').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = this.getAttribute('data-target');
            var input = document.getElementById(id);
            var icon = this.querySelector('i');
            if (!input) return;
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });

    /* -------------------------------------------------------
       Show Save Bar on Change
    ------------------------------------------------------- */
    var form = document.getElementById('profileEditForm');
    var saveBar = document.getElementById('saveBar');
    if (form && saveBar) {
        form.addEventListener('change', function () {
            saveBar.classList.add('pedit-form__save-bar--visible');
        });
        form.addEventListener('input', function () {
            saveBar.classList.add('pedit-form__save-bar--visible');
        });
    }

})();
