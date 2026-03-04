/* ============================================================
   PAGE BOXES — Dynamic box add/remove/collapse + video support
   ============================================================ */

(function () {
    'use strict';

    var container = document.getElementById('boxesContainer');
    var addBtn = document.getElementById('addBoxBtn');
    var noMsg = document.getElementById('noBoxesMessage');

    if (!container || !addBtn) return;

    function getBoxCount() {
        return container.querySelectorAll('.pb-box-item').length;
    }

    function reindex() {
        var items = container.querySelectorAll('.pb-box-item');
        items.forEach(function (item, idx) {
            item.setAttribute('data-index', idx);
            var numEl = item.querySelector('.pb-box-num-val');
            if (numEl) numEl.textContent = idx + 1;

            item.querySelectorAll('[name]').forEach(function (el) {
                el.name = el.name.replace(/\[\d+\]/, '[' + idx + ']');
            });
        });

        if (noMsg) {
            noMsg.style.display = items.length ? 'none' : '';
        }
    }

    /* ---- YouTube helpers ---- */
    function extractYoutubeId(url) {
        if (!url) return null;
        var patterns = [
            /youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/,
            /youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/,
            /youtu\.be\/([a-zA-Z0-9_-]{11})/,
            /youtube\.com\/shorts\/([a-zA-Z0-9_-]{11})/
        ];
        for (var i = 0; i < patterns.length; i++) {
            var m = url.match(patterns[i]);
            if (m) return m[1];
        }
        return null;
    }

    function renderVideoPreview(wrapper, videoId) {
        var existing = wrapper.querySelector('.pb-video-preview');
        if (existing) existing.remove();

        if (!videoId) return;

        var div = document.createElement('div');
        div.className = 'pb-video-preview mt-2';
        div.innerHTML = '<div class="ratio ratio-16x9">' +
            '<iframe src="https://www.youtube.com/embed/' + videoId + '" allowfullscreen loading="lazy"></iframe>' +
            '</div>';
        wrapper.appendChild(div);
    }

    /* ---- Toggle type fields ---- */
    function toggleTypeFields(boxItem, type) {
        var imgField = boxItem.querySelector('.pb-field-image');
        var vidField = boxItem.querySelector('.pb-field-video');
        var badge = boxItem.querySelector('.pb-box-type-badge');

        if (imgField) imgField.style.display = type === 'image' ? '' : 'none';
        if (vidField) vidField.style.display = type === 'video' ? '' : 'none';

        boxItem.setAttribute('data-type', type);

        if (badge) {
            badge.className = 'pb-box-type-badge pb-box-type-badge--' + type;
            badge.innerHTML = type === 'video'
                ? '<i class="bi bi-youtube"></i> Video'
                : '<i class="bi bi-image"></i> Görsel';
        }

        // Update radio active states
        boxItem.querySelectorAll('.pb-type-option').forEach(function (opt) {
            var radio = opt.querySelector('.pb-type-radio');
            opt.classList.toggle('active', radio && radio.value === type);
        });
    }

    function createBoxHtml(index) {
        return '<div class="pb-box-item" data-index="' + index + '" data-type="image">' +
            '<div class="pb-box-header">' +
                '<span class="pb-box-number">#<span class="pb-box-num-val">' + (index + 1) + '</span></span>' +
                '<span class="pb-box-type-badge pb-box-type-badge--image"><i class="bi bi-image"></i> Görsel</span>' +
                '<span class="pb-box-title-preview">Yeni Kutu</span>' +
                '<div class="pb-box-actions">' +
                    '<button type="button" class="pb-box-toggle" title="Aç/Kapat"><i class="bi bi-chevron-up"></i></button>' +
                    '<button type="button" class="pb-box-remove" title="Kutuyu Sil"><i class="bi bi-trash"></i></button>' +
                '</div>' +
            '</div>' +
            '<div class="pb-box-body">' +
                '<div class="row g-3">' +
                    '<!-- Tip Seçici -->' +
                    '<div class="col-12">' +
                        '<label class="form-label">Kutu Tipi</label>' +
                        '<div class="pb-box-type-selector">' +
                            '<label class="pb-type-option active">' +
                                '<input type="radio" name="boxes[' + index + '][type]" value="image" class="pb-type-radio" checked>' +
                                '<i class="bi bi-image"></i> Görsel Kutu' +
                            '</label>' +
                            '<label class="pb-type-option">' +
                                '<input type="radio" name="boxes[' + index + '][type]" value="video" class="pb-type-radio">' +
                                '<i class="bi bi-youtube"></i> Video Kutu' +
                            '</label>' +
                        '</div>' +
                    '</div>' +
                    '<div class="col-12">' +
                        '<label class="form-label">Kutu Başlığı <span class="text-danger">*</span></label>' +
                        '<input type="text" class="form-control pb-box-title-input" name="boxes[' + index + '][title]" placeholder="Kutu başlığı" required>' +
                    '</div>' +
                    '<div class="col-md-8">' +
                        '<label class="form-label">Bağlantı (URL)</label>' +
                        '<input type="url" class="form-control" name="boxes[' + index + '][link]" placeholder="https://ornek.com">' +
                    '</div>' +
                    '<div class="col-md-4">' +
                        '<label class="form-label">Link Hedefi</label>' +
                        '<select class="form-select" name="boxes[' + index + '][link_target]">' +
                            '<option value="_blank">Yeni Sekme</option>' +
                            '<option value="_self">Aynı Sekme</option>' +
                        '</select>' +
                    '</div>' +
                    '<div class="col-md-4">' +
                        '<label class="form-label">Masaüstü Boyut</label>' +
                        '<select class="form-select" name="boxes[' + index + '][col_desktop]">' +
                            '<option value="2">2/12 — Çok Küçük</option>' +
                            '<option value="3">3/12 — Küçük</option>' +
                            '<option value="4" selected>4/12 — Orta</option>' +
                            '<option value="6">6/12 — Yarım</option>' +
                            '<option value="12">12/12 — Tam</option>' +
                        '</select>' +
                    '</div>' +
                    '<div class="col-md-4">' +
                        '<label class="form-label">Tablet Boyut</label>' +
                        '<select class="form-select" name="boxes[' + index + '][col_tablet]">' +
                            '<option value="4">4/12 — Küçük</option>' +
                            '<option value="6" selected>6/12 — Yarım</option>' +
                            '<option value="12">12/12 — Tam</option>' +
                        '</select>' +
                    '</div>' +
                    '<div class="col-md-4">' +
                        '<label class="form-label">Mobil Boyut</label>' +
                        '<select class="form-select" name="boxes[' + index + '][col_mobile]">' +
                            '<option value="6">6/12 — Yarım</option>' +
                            '<option value="12" selected>12/12 — Tam</option>' +
                        '</select>' +
                    '</div>' +
                    '<!-- Görsel Alanı -->' +
                    '<div class="col-12 pb-field-image">' +
                        '<label class="form-label">Kutu Görseli</label>' +
                        '<input type="file" class="form-control" name="box_images[' + index + ']" accept="image/png,image/jpeg,image/webp">' +
                        '<div class="form-text">PNG, JPG, WebP | Maks. 1 MB</div>' +
                    '</div>' +
                    '<!-- Video Alanı -->' +
                    '<div class="col-12 pb-field-video" style="display:none">' +
                        '<label class="form-label">YouTube URL <span class="text-danger">*</span></label>' +
                        '<div class="input-group">' +
                            '<span class="input-group-text"><i class="bi bi-youtube"></i></span>' +
                            '<input type="url" class="form-control pb-video-url-input" name="boxes[' + index + '][video_url]" placeholder="https://www.youtube.com/watch?v=...">' +
                        '</div>' +
                        '<div class="form-text">youtube.com/watch?v=, youtu.be/, youtube.com/shorts/ desteklenir</div>' +
                    '</div>' +
                    '<div class="col-12">' +
                        '<label class="form-label">Kutu Açıklaması</label>' +
                        '<textarea class="form-control" name="boxes[' + index + '][description]" rows="3" placeholder="Kutu içeriği / açıklama metni..."></textarea>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>';
    }

    /* Add Box */
    addBtn.addEventListener('click', function () {
        if (getBoxCount() >= 20) {
            if (typeof showToast === 'function') {
                showToast('En fazla 20 kutu eklenebilir.', 'error');
            }
            return;
        }

        var idx = getBoxCount();
        var temp = document.createElement('div');
        temp.innerHTML = createBoxHtml(idx);
        var newBox = temp.firstChild;
        container.appendChild(newBox);
        reindex();

        newBox.querySelector('.pb-box-title-input').focus();
        newBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });

    /* Delegate click events */
    container.addEventListener('click', function (e) {
        var target = e.target.closest('button');
        if (!target) return;

        /* Remove image */
        if (target.classList.contains('pb-box-img-remove')) {
            var imgPreview = target.closest('.pb-box-img-preview');
            var colWrap = target.closest('.col-12');
            var boxBody = target.closest('.pb-box-body');
            if (imgPreview) imgPreview.remove();
            if (colWrap) {
                var fileInput = colWrap.querySelector('input[type="file"]');
                if (fileInput) fileInput.value = '';
            }
            if (boxBody) {
                var hiddenImg = boxBody.querySelector('input[name*="existing_image"]');
                if (hiddenImg) hiddenImg.value = '';
            }
            return;
        }

        /* Remove box */
        if (target.classList.contains('pb-box-remove')) {
            var item = target.closest('.pb-box-item');
            if (item) {
                item.style.opacity = '0';
                item.style.transform = 'scale(0.95)';
                setTimeout(function () {
                    item.remove();
                    reindex();
                }, 200);
            }
        }

        /* Toggle collapse */
        if (target.classList.contains('pb-box-toggle')) {
            var boxItem = target.closest('.pb-box-item');
            var body = boxItem.querySelector('.pb-box-body');
            var icon = target.querySelector('i');
            if (body) {
                var isHidden = body.style.display === 'none';
                body.style.display = isHidden ? '' : 'none';
                icon.className = isHidden ? 'bi bi-chevron-up' : 'bi bi-chevron-down';
                boxItem.classList.toggle('pb-box-collapsed', !isHidden);
            }
        }
    });

    /* Delegate change events */
    container.addEventListener('change', function (e) {
        /* Type radio toggle */
        if (e.target.classList.contains('pb-type-radio')) {
            var boxItem = e.target.closest('.pb-box-item');
            toggleTypeFields(boxItem, e.target.value);
            return;
        }

        /* Image file preview */
        if (e.target.type === 'file' && e.target.accept) {
            var file = e.target.files[0];
            if (!file) return;

            if (file.size > 1024 * 1024) {
                if (typeof showToast === 'function') {
                    showToast('Görsel en fazla 1 MB olmalıdır.', 'error');
                }
                e.target.value = '';
                return;
            }

            var wrapper = e.target.closest('.col-12');
            var existing = wrapper.querySelector('.pb-box-img-preview');
            if (existing) existing.remove();

            var reader = new FileReader();
            reader.onload = function (ev) {
                var div = document.createElement('div');
                div.className = 'pb-box-img-preview mb-2';
                div.innerHTML = '<img src="' + ev.target.result + '" alt="" class="img-fluid rounded" loading="lazy">' +
                    '<button type="button" class="pb-box-img-remove" title="Görseli Kaldır"><i class="bi bi-x-lg"></i></button>';
                wrapper.insertBefore(div, e.target);
            };
            reader.readAsDataURL(file);
        }
    });

    /* Delegate input events */
    var videoDebounce = null;
    container.addEventListener('input', function (e) {
        /* Live title preview */
        if (e.target.classList.contains('pb-box-title-input')) {
            var item = e.target.closest('.pb-box-item');
            var preview = item.querySelector('.pb-box-title-preview');
            if (preview) {
                preview.textContent = e.target.value || 'Yeni Kutu';
            }
        }

        /* YouTube URL preview with debounce */
        if (e.target.classList.contains('pb-video-url-input')) {
            clearTimeout(videoDebounce);
            var input = e.target;
            videoDebounce = setTimeout(function () {
                var fieldWrap = input.closest('.pb-field-video');
                var videoId = extractYoutubeId(input.value);
                renderVideoPreview(fieldWrap, videoId);
            }, 500);
        }
    });

})();
