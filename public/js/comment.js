(function ($) {
    'use strict';

    var form = document.getElementById('commentForm');
    var submitBtn = document.getElementById('commentSubmitBtn');
    var ratingContainer = document.getElementById('cmtRating');
    var ratingInput = document.getElementById('cmt_rating');
    var bodyTextarea = document.getElementById('cmt_body');
    var charCount = document.getElementById('cmtCharCount');

    if (!form || !submitBtn) return;

    // ─── Star Rating ───
    if (ratingContainer && ratingInput) {
        var stars = ratingContainer.querySelectorAll('.cmt-rating__star');

        stars.forEach(function (star) {
            star.addEventListener('click', function () {
                var val = parseInt(this.getAttribute('data-value'), 10);
                ratingInput.value = val;
                updateStars(val);
            });

            star.addEventListener('mouseenter', function () {
                var val = parseInt(this.getAttribute('data-value'), 10);
                highlightStars(val);
            });
        });

        ratingContainer.addEventListener('mouseleave', function () {
            var current = parseInt(ratingInput.value, 10) || 0;
            updateStars(current);
        });
    }

    function updateStars(value) {
        var stars = ratingContainer.querySelectorAll('.cmt-rating__star i');
        stars.forEach(function (icon, idx) {
            if (idx < value) {
                icon.className = 'fa-solid fa-star';
            } else {
                icon.className = 'fa-regular fa-star';
            }
        });
    }

    function highlightStars(value) {
        var stars = ratingContainer.querySelectorAll('.cmt-rating__star i');
        stars.forEach(function (icon, idx) {
            if (idx < value) {
                icon.className = 'fa-solid fa-star';
            } else {
                icon.className = 'fa-regular fa-star';
            }
        });
    }

    // ─── Character Counter ───
    if (bodyTextarea && charCount) {
        bodyTextarea.addEventListener('input', function () {
            charCount.textContent = this.value.length;
        });
    }

    // ─── jQuery Validation Engine Init ───
    var $form = $('#commentForm');

    $form.validationEngine('attach', {
        promptPosition: 'bottomLeft',
        scroll: false,
        showOneMessage: true,
        focusFirstField: true,
        autoHidePrompt: true,
        autoHideDelay: 4000,
        onValidationComplete: function (theForm, valid) {
            if (valid) {
                submitComment();
            }
            return false;
        }
    });

    // ─── Submit Comment via Fetch ───
    function submitComment() {
        var originalHtml = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Gönderiliyor...';

        var token = form.querySelector('input[name="_token"]').value;
        var formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(function (response) { return response.json(); })
        .then(function (data) {
            if (data.success) {
                if (window.BkModal) {
                    window.BkModal.success(data.message);
                } else {
                    alert(data.message);
                }
                form.reset();
                ratingInput.value = '';
                updateStars(0);
                if (charCount) charCount.textContent = '0';
                $form.validationEngine('hideAll');
            } else if (data.errors) {
                var errorList = [];
                Object.values(data.errors).forEach(function (errs) {
                    if (Array.isArray(errs)) {
                        errs.forEach(function (e) { errorList.push(e); });
                    } else {
                        errorList.push(errs);
                    }
                });
                if (window.BkModal) {
                    window.BkModal.danger(errorList);
                } else {
                    alert(errorList.join('\n'));
                }
            } else {
                var msg = data.message || 'Bir hata oluştu.';
                if (window.BkModal) {
                    window.BkModal.danger(msg);
                } else {
                    alert(msg);
                }
            }
        })
        .catch(function () {
            var msg = 'Bir hata oluştu. Lütfen tekrar deneyin.';
            if (window.BkModal) {
                window.BkModal.danger(msg);
            } else {
                alert(msg);
            }
        })
        .finally(function () {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHtml;
            if (typeof grecaptcha !== 'undefined') grecaptcha.reset();
        });
    }

})(jQuery);
