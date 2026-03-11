(function () {
    'use strict';

    var form = document.getElementById('contactForm');
    var submitBtn = document.getElementById('contactSubmitBtn');

    if (!form || !submitBtn) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        var fullname = form.querySelector('#fullname').value.trim();
        var email = form.querySelector('#email').value.trim();
        var subject = form.querySelector('#subject').value;
        var message = form.querySelector('#message').value.trim();

        // Basic validation
        if (!fullname || !email || !subject || !message) {
            if (window.BkModal) window.BkModal.warning('Lütfen tüm alanları doldurun.');
            return;
        }

        if (message.length < 10) {
            if (window.BkModal) window.BkModal.warning('Mesajınız en az 10 karakter olmalıdır.');
            return;
        }

        // Disable button
        var originalHtml = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Gönderiliyor...';

        var token = form.querySelector('input[name="_token"]').value;
        var formData = new FormData();
        formData.append('_token', token);
        formData.append('fullname', fullname);
        formData.append('email', email);
        formData.append('subject', subject);
        formData.append('message', message);

        var recaptchaInput = form.querySelector('[name="g-recaptcha-response"]');
        if (recaptchaInput && recaptchaInput.value) {
            formData.append('g-recaptcha-response', recaptchaInput.value);
        }

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(function (response) { return response.json(); })
        .then(function (data) {
            if (data.success) {
                if (window.BkModal) window.BkModal.success(data.message);
                form.reset();
            } else if (data.errors) {
                var errorList = [];
                Object.values(data.errors).forEach(function (errs) {
                    if (Array.isArray(errs)) {
                        errs.forEach(function (e) { errorList.push(e); });
                    } else {
                        errorList.push(errs);
                    }
                });
                if (window.BkModal) window.BkModal.danger(errorList);
            } else {
                if (window.BkModal) window.BkModal.danger(data.message || 'Bir hata oluştu.');
            }
        })
        .catch(function () {
            if (window.BkModal) window.BkModal.danger('Bir hata oluştu. Lütfen tekrar deneyin.');
        })
        .finally(function () {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHtml;
            if (typeof grecaptcha !== 'undefined') grecaptcha.reset();
        });
    });

})();
