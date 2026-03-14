/**
 * Profile Page JS
 * - Profile tabs switching
 * - Writer Application Modal (single-step)
 */
(function () {
    'use strict';

    /* -------------------------------------------------------
       Profile Tabs
    ------------------------------------------------------- */
    document.querySelectorAll('.profile-tabs__btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var activeBtn = document.querySelector('.profile-tabs__btn--active');
            if (activeBtn) activeBtn.classList.remove('profile-tabs__btn--active');
            this.classList.add('profile-tabs__btn--active');
        });
    });

    /* -------------------------------------------------------
       Writer Application Modal
    ------------------------------------------------------- */
    var modal = document.getElementById('writerApplicationModal');
    if (!modal) return;

    var btnSubmit = document.getElementById('btnSubmit');
    var motivationInput = document.getElementById('wf_motivation');
    var charCount = document.getElementById('motivationCharCount');

    if (!btnSubmit || !motivationInput) return;

    /* Character counter */
    if (charCount) {
        charCount.textContent = motivationInput.value.length;
        motivationInput.addEventListener('input', function () {
            charCount.textContent = this.value.length;
        });
    }

    /* Submit */
    btnSubmit.addEventListener('click', function () {
        var form = document.getElementById('writerApplicationForm');
        if (!form) return;

        var val = motivationInput.value.trim();
        if (val.length < 50) {
            if (window.BkModal) window.BkModal.danger('Motivasyon metni en az 50 karakter olmalıdır.');
            motivationInput.focus();
            return;
        }

        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Gönderiliyor...';

        var csrfToken = document.querySelector('meta[name="csrf-token"]');

        fetch('/yazar-basvuru', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken ? csrfToken.content : '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ motivation: val })
        })
        .then(function (response) { return response.json(); })
        .then(function (data) {
            if (data.success) {
                var bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) bsModal.hide();
                if (window.BkModal) {
                    window.BkModal.success(data.message || 'Başvurunuz başarıyla gönderildi!');
                }
                setTimeout(function () { location.reload(); }, 2000);
            } else {
                if (window.BkModal) window.BkModal.danger(data.message || 'Bir hata oluştu.');
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '<i class="fa-solid fa-paper-plane me-2"></i>Başvuruyu Gönder';
            }
        })
        .catch(function () {
            if (window.BkModal) window.BkModal.danger('Bağlantı hatası. Lütfen tekrar deneyin.');
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="fa-solid fa-paper-plane me-2"></i>Başvuruyu Gönder';
        });
    });

    /* Reset on close */
    modal.addEventListener('hidden.bs.modal', function () {
        motivationInput.value = '';
        if (charCount) charCount.textContent = '0';
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = '<i class="fa-solid fa-paper-plane me-2"></i>Başvuruyu Gönder';
    });

})();
