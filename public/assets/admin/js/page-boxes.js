/* ============================================================
   PAGE BOXES — Dynamic box add/remove/collapse
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

            // Reindex all input/select/textarea names
            item.querySelectorAll('[name]').forEach(function (el) {
                el.name = el.name.replace(/\[\d+\]/, '[' + idx + ']');
            });
        });

        if (noMsg) {
            noMsg.style.display = items.length ? 'none' : '';
        }
    }

    function createBoxHtml(index) {
        return '<div class="pb-box-item" data-index="' + index + '">' +
            '<div class="pb-box-header">' +
                '<span class="pb-box-number">#<span class="pb-box-num-val">' + (index + 1) + '</span></span>' +
                '<span class="pb-box-title-preview">Yeni Kutu</span>' +
                '<div class="pb-box-actions">' +
                    '<button type="button" class="pb-box-toggle" title="Aç/Kapat"><i class="bi bi-chevron-up"></i></button>' +
                    '<button type="button" class="pb-box-remove" title="Kutuyu Sil"><i class="bi bi-trash"></i></button>' +
                '</div>' +
            '</div>' +
            '<div class="pb-box-body">' +
                '<div class="row g-3">' +
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
                    '<div class="col-12">' +
                        '<label class="form-label">Kutu Görseli</label>' +
                        '<input type="file" class="form-control" name="box_images[' + index + ']" accept="image/png,image/jpeg,image/webp">' +
                        '<div class="form-text">PNG, JPG, WebP | Maks. 1 MB</div>' +
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

    /* Delegate events */
    container.addEventListener('click', function (e) {
        var target = e.target.closest('button');
        if (!target) return;

        /* Remove */
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

    /* Live title preview */
    container.addEventListener('input', function (e) {
        if (e.target.classList.contains('pb-box-title-input')) {
            var item = e.target.closest('.pb-box-item');
            var preview = item.querySelector('.pb-box-title-preview');
            if (preview) {
                preview.textContent = e.target.value || 'Yeni Kutu';
            }
        }
    });

    /* Image preview */
    container.addEventListener('change', function (e) {
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
                div.innerHTML = '<img src="' + ev.target.result + '" alt="" class="img-fluid rounded" loading="lazy">';
                wrapper.insertBefore(div, e.target);
            };
            reader.readAsDataURL(file);
        }
    });

})();
