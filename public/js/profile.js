/**
 * Profile Page JS
 * - Profile tabs switching
 * - Writer Application Modal (multi-step)
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
       Writer Application Modal — Multi-step
    ------------------------------------------------------- */
    var modal = document.getElementById('writerApplicationModal');
    if (!modal) return;

    var currentStep = 1;
    var totalSteps = 3;

    var panels = [
        document.getElementById('step1'),
        document.getElementById('step2'),
        document.getElementById('step3')
    ];
    var stepDots = document.querySelectorAll('.writer-modal__step');
    var btnBack = document.getElementById('btnBack');
    var btnNext = document.getElementById('btnNext');
    var btnSubmit = document.getElementById('btnSubmit');
    var stepLabel = document.getElementById('currentStepLabel');

    if (!panels[0] || !btnBack || !btnNext || !btnSubmit) return;

    function goTo(step) {
        panels.forEach(function (p) { if (p) p.classList.add('d-none'); });
        stepDots.forEach(function (d) {
            d.classList.remove('writer-modal__step--active', 'writer-modal__step--done');
        });

        if (panels[step - 1]) panels[step - 1].classList.remove('d-none');
        currentStep = step;

        stepDots.forEach(function (d, i) {
            var n = i + 1;
            if (n < step) d.classList.add('writer-modal__step--done');
            if (n === step) d.classList.add('writer-modal__step--active');
        });

        btnBack.classList.toggle('d-none', step === 1);
        btnNext.classList.toggle('d-none', step === totalSteps);
        btnSubmit.classList.toggle('d-none', step !== totalSteps);

        if (stepLabel) stepLabel.textContent = 'Adım ' + step + ' / ' + totalSteps;
    }

    btnBack.addEventListener('click', function () {
        if (currentStep > 1) goTo(currentStep - 1);
    });

    btnNext.addEventListener('click', function () {
        if (currentStep < totalSteps) goTo(currentStep + 1);
    });

    btnSubmit.addEventListener('click', function () {
        var form = document.getElementById('writerApplicationForm');
        if (!form) return;

        var formData = new FormData(form);
        var csrfToken = document.querySelector('meta[name="csrf-token"]');

        fetch('/yazar-basvuru', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken ? csrfToken.content : '',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(function (response) { return response.json(); })
        .then(function (data) {
            if (data.success) {
                var bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) bsModal.hide();
                if (window.BkModal) window.BkModal.success('Başvurunuz başarıyla gönderildi! E-posta ile bilgilendirileceksiniz.');
            } else {
                if (window.BkModal) window.BkModal.danger(data.message || 'Bir hata oluştu. Lütfen tekrar deneyin.');
            }
        })
        .catch(function () {
            if (window.BkModal) window.BkModal.danger('Bağlantı hatası. Lütfen tekrar deneyin.');
        });
    });

    /* Reset on close */
    modal.addEventListener('hidden.bs.modal', function () {
        goTo(1);
    });

})();
