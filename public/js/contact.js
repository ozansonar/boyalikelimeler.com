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
            showAlert('Lütfen tüm alanları doldurun.', 'danger');
            return;
        }

        if (message.length < 10) {
            showAlert('Mesajınız en az 10 karakter olmalıdır.', 'danger');
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
                showAlert(data.message, 'success');
                form.reset();
            } else if (data.errors) {
                var firstError = Object.values(data.errors)[0];
                showAlert(Array.isArray(firstError) ? firstError[0] : firstError, 'danger');
            } else {
                showAlert(data.message || 'Bir hata oluştu.', 'danger');
            }
        })
        .catch(function () {
            showAlert('Bir hata oluştu. Lütfen tekrar deneyin.', 'danger');
        })
        .finally(function () {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHtml;
        });
    });

    function showAlert(message, type) {
        var existing = form.parentElement.querySelector('.contact-alert');
        if (existing) existing.remove();

        var iconClass = type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation';
        var alert = document.createElement('div');
        alert.className = 'alert alert-' + type + ' alert-dismissible fade show contact-alert mt-3';
        alert.setAttribute('role', 'alert');
        alert.innerHTML = '<i class="fa-solid ' + iconClass + ' me-2"></i>' + message +
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>';

        form.parentElement.insertBefore(alert, form);

        if (type === 'success') {
            setTimeout(function () {
                if (alert.parentElement) {
                    alert.classList.remove('show');
                    setTimeout(function () { alert.remove(); }, 300);
                }
            }, 5000);
        }
    }
})();
