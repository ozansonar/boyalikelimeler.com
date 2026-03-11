'use strict';

document.addEventListener('DOMContentLoaded', function () {

    // =====================================================
    // Soru Sor Modal — AJAX Submit
    // =====================================================
    const submitQuestionBtn = document.getElementById('submitQuestionBtn');
    if (submitQuestionBtn) {
        submitQuestionBtn.addEventListener('click', function () {
            const form = document.getElementById('askQuestionForm');
            if (!form) return;

            const title = form.querySelector('[name="title"]');
            const body = form.querySelector('[name="body"]');
            const categoryId = form.querySelector('[name="qna_category_id"]');
            const token = form.querySelector('[name="_token"]');

            if (!title.value.trim() || title.value.trim().length < 10) {
                showQnaToast('Soru başlığı en az 10 karakter olmalıdır.', 'error');
                title.focus();
                return;
            }

            if (!body.value.trim() || body.value.trim().length < 20) {
                showQnaToast('Soru detayı en az 20 karakter olmalıdır.', 'error');
                body.focus();
                return;
            }

            submitQuestionBtn.disabled = true;
            submitQuestionBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Gönderiliyor...';

            fetch('/soz-meydani/soru-sor', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token.value,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    title: title.value.trim(),
                    body: body.value.trim(),
                    qna_category_id: categoryId.value,
                }),
            })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.success) {
                        showQnaToast(data.message, 'success');
                        form.reset();
                        var modal = bootstrap.Modal.getInstance(document.getElementById('askQuestionModal'));
                        if (modal) modal.hide();
                    } else {
                        var msg = data.message || 'Bir hata oluştu.';
                        if (data.errors) {
                            var errorMessages = Object.values(data.errors).flat();
                            msg = errorMessages.join(' ');
                        }
                        showQnaToast(msg, 'error');
                    }
                })
                .catch(function () {
                    showQnaToast('Bir hata oluştu. Lütfen tekrar deneyin.', 'error');
                })
                .finally(function () {
                    submitQuestionBtn.disabled = false;
                    submitQuestionBtn.innerHTML = '<i class="fa-solid fa-paper-plane me-2"></i>Soruyu Gönder';
                });
        });
    }

    // =====================================================
    // Cevap Yaz — AJAX Submit
    // =====================================================
    const submitAnswerBtn = document.getElementById('submitAnswerBtn');
    if (submitAnswerBtn) {
        submitAnswerBtn.addEventListener('click', function () {
            const form = document.getElementById('writeAnswerForm');
            if (!form) return;

            const body = form.querySelector('[name="body"]');
            const questionId = form.querySelector('[name="question_id"]');
            const token = form.querySelector('[name="_token"]');

            if (!body.value.trim() || body.value.trim().length < 20) {
                showQnaToast('Cevap en az 20 karakter olmalıdır.', 'error');
                body.focus();
                return;
            }

            submitAnswerBtn.disabled = true;
            submitAnswerBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Gönderiliyor...';

            fetch('/soz-meydani/cevap-yaz/' + questionId.value, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token.value,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    body: body.value.trim(),
                }),
            })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.success) {
                        showQnaToast(data.message, 'success');
                        body.value = '';
                    } else {
                        var msg = data.message || 'Bir hata oluştu.';
                        if (data.errors) {
                            var errorMessages = Object.values(data.errors).flat();
                            msg = errorMessages.join(' ');
                        }
                        showQnaToast(msg, 'error');
                    }
                })
                .catch(function () {
                    showQnaToast('Bir hata oluştu. Lütfen tekrar deneyin.', 'error');
                })
                .finally(function () {
                    submitAnswerBtn.disabled = false;
                    submitAnswerBtn.innerHTML = '<i class="fa-solid fa-paper-plane me-2"></i>Cevabı Gönder';
                });
        });
    }

    // =====================================================
    // Beğeni Toggle — AJAX
    // =====================================================
    document.querySelectorAll('.qna-like-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var type = btn.dataset.type;
            var id = btn.dataset.id;
            var csrfToken = document.querySelector('meta[name="csrf-token"]');

            if (!csrfToken) {
                showQnaToast('Beğenmek için giriş yapmalısınız.', 'error');
                return;
            }

            fetch('/soz-meydani/begen', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ type: type, id: id }),
            })
                .then(function (res) {
                    if (res.status === 401) {
                        showQnaToast('Beğenmek için giriş yapmalısınız.', 'error');
                        throw new Error('Unauthorized');
                    }
                    return res.json();
                })
                .then(function (data) {
                    if (data.success) {
                        var icon = btn.querySelector('i');
                        var countEl = btn.querySelector('.qna-like-count');
                        if (countEl) countEl.textContent = data.count;

                        if (data.liked) {
                            icon.classList.remove('fa-regular');
                            icon.classList.add('fa-solid');
                        } else {
                            icon.classList.remove('fa-solid');
                            icon.classList.add('fa-regular');
                        }
                    }
                })
                .catch(function (err) {
                    if (err.message !== 'Unauthorized') {
                        showQnaToast('Bir hata oluştu.', 'error');
                    }
                });
        });
    });

    // =====================================================
    // Paylaş Butonu
    // =====================================================
    document.querySelectorAll('.qna-share-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (navigator.share) {
                navigator.share({
                    title: document.title,
                    url: window.location.href,
                });
            } else {
                navigator.clipboard.writeText(window.location.href).then(function () {
                    showQnaToast('Bağlantı panoya kopyalandı.', 'success');
                });
            }
        });
    });

    // =====================================================
    // Arama ve Filtre
    // =====================================================
    var sortSelect = document.getElementById('qnaSortSelect');
    if (sortSelect) {
        sortSelect.addEventListener('change', function () {
            var url = new URL(window.location.href);
            url.searchParams.set('sort', this.value);
            url.searchParams.delete('page');
            window.location.href = url.toString();
        });
    }

    var searchInput = document.getElementById('qnaSearchInput');
    if (searchInput) {
        var searchTimeout = null;
        searchInput.addEventListener('keyup', function (e) {
            if (e.key === 'Enter') {
                var url = new URL(window.location.href);
                if (this.value.trim()) {
                    url.searchParams.set('search', this.value.trim());
                } else {
                    url.searchParams.delete('search');
                }
                url.searchParams.delete('page');
                window.location.href = url.toString();
            }
        });
    }

    // =====================================================
    // Toast Helper — Global BkModal kullanır
    // =====================================================
    function showQnaToast(message, type) {
        var modalType = type === 'success' ? 'success' : 'danger';
        window.BkModal.show(modalType, message);
    }

});
