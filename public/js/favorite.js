/* ============================================================
   BOYALI KELİMELER — Favorite / Like Toggle (Vanilla JS)
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    var csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) return;

    var token = csrfToken.getAttribute('content');
    var favoriteButtons = document.querySelectorAll('.js-favorite-btn');

    favoriteButtons.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            if (btn.getAttribute('data-login-required') === 'true') {
                if (window.BkModal) {
                    window.BkModal.warning('Bu işlem için giriş yapmanız gerekiyor.');
                }
                return;
            }

            if (btn.disabled) return;
            btn.disabled = true;

            var type = btn.getAttribute('data-type');
            var id = btn.getAttribute('data-id');

            fetch('/favori/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ type: type, id: id })
            })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                if (data.success) {
                    var icon = btn.querySelector('i');
                    var text = btn.querySelector('.js-favorite-text');
                    var count = btn.querySelector('.js-favorite-count');

                    if (data.is_favorited) {
                        btn.classList.add('cdetail-actions__btn--liked', 'blogd-actions__like-btn--liked');
                        if (icon) {
                            icon.classList.remove('fa-regular');
                            icon.classList.add('fa-solid');
                        }
                        if (text) text.textContent = 'Beğenildi';

                        btn.classList.add('js-favorite-pulse');
                        setTimeout(function () {
                            btn.classList.remove('js-favorite-pulse');
                        }, 600);
                    } else {
                        btn.classList.remove('cdetail-actions__btn--liked', 'blogd-actions__like-btn--liked');
                        if (icon) {
                            icon.classList.remove('fa-solid');
                            icon.classList.add('fa-regular');
                        }
                        if (text) text.textContent = 'Beğen';
                    }

                    if (count) {
                        count.textContent = data.count;
                    }
                } else if (data.message && window.BkModal) {
                    window.BkModal.danger(data.message);
                }
            })
            .catch(function () {
                if (window.BkModal) {
                    window.BkModal.danger('Bir hata oluştu, lütfen tekrar deneyin.');
                }
            })
            .finally(function () {
                btn.disabled = false;
            });
        });
    });

    /* -- Copy Link (universal) -------------------------------- */
    var copyButtons = document.querySelectorAll('.cdetail-actions__btn--copy, .blogd-share__btn--copy');
    copyButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var url = this.getAttribute('data-url');
            if (navigator.clipboard && url) {
                navigator.clipboard.writeText(url).then(function () {
                    var originalHtml = btn.innerHTML;
                    btn.innerHTML = '<i class="fa-solid fa-check"></i>';
                    setTimeout(function () {
                        btn.innerHTML = originalHtml;
                    }, 2000);
                });
            }
        });
    });
});
