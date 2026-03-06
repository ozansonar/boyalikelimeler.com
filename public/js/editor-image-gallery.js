/**
 * Editor Image Gallery — TinyMCE Integration
 *
 * - images_upload_handler: auto-upload pasted/dropped images
 * - Custom toolbar button "imagegallery": opens gallery modal
 * - Multi-select support (click to toggle selection)
 * - Gallery modal: upload (multi-file), list, select, delete, insert
 */
(function () {
    'use strict';

    var CSRF = document.querySelector('meta[name="csrf-token"]');
    var csrfToken = CSRF ? CSRF.getAttribute('content') : '';

    // Context user ID: allows admin to manage images on behalf of another user
    var contextUserId = window.editorImageContextUserId || 0;
    var ctxParam = contextUserId ? '?context_user_id=' + contextUserId : '';

    var URLS = {
        list:   '/editor/images' + ctxParam,
        upload: '/editor/images',
        delete: '/editor/images/'
    };

    var selectedImages = [];
    var activeEditor = null;
    var modalInstance = null;

    function el(id) { return document.getElementById(id); }

    // ─── TinyMCE Upload Handler ─────────────────────────────
    window.editorImagesUploadHandler = function (blobInfo) {
        return new Promise(function (resolve, reject) {
            var formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());
            if (contextUserId) formData.append('context_user_id', contextUserId);

            fetch(URLS.upload, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: formData
            })
            .then(function (res) {
                if (!res.ok) {
                    return res.json().then(function (data) {
                        var msg = data.message || 'Yükleme hatası';
                        if (data.errors && data.errors.file) msg = data.errors.file[0];
                        throw new Error(msg);
                    });
                }
                return res.json();
            })
            .then(function (data) {
                if (data.success && data.location) resolve(data.location);
                else reject('Yükleme başarısız.');
            })
            .catch(function (err) { reject(err.message || 'Yükleme hatası.'); });
        });
    };

    // ─── TinyMCE Custom Button ──────────────────────────────
    window.editorImagesSetup = function (editor) {
        editor.ui.registry.addButton('imagegallery', {
            icon: 'image',
            tooltip: 'Görsel Galerisi',
            onAction: function () {
                activeEditor = editor;
                clearSelection();
                openGalleryModal();
                loadGallery();
            }
        });
    };

    // ─── Selection ──────────────────────────────────────────
    function clearSelection() {
        selectedImages = [];
        document.querySelectorAll('.eig-card--selected').forEach(function (c) {
            c.classList.remove('eig-card--selected');
        });
        updateSelectionUI();
    }

    function toggleSelect(cardEl, img) {
        var idx = selectedImages.findIndex(function (s) { return s.id === img.id; });
        if (idx > -1) {
            selectedImages.splice(idx, 1);
            cardEl.classList.remove('eig-card--selected');
        } else {
            selectedImages.push(img);
            cardEl.classList.add('eig-card--selected');
        }
        updateSelectionUI();
    }

    function updateSelectionUI() {
        var insertBtn = el('eigInsertBtn');
        var infoEl = el('eigSelectedInfo');
        var count = selectedImages.length;

        if (insertBtn) {
            insertBtn.disabled = count === 0;
            insertBtn.innerHTML = count > 1
                ? '<i class="bi bi-check-lg me-1"></i>' + count + ' görsel ekle'
                : '<i class="bi bi-check-lg me-1"></i>Ekle';
        }
        if (infoEl) {
            infoEl.textContent = count === 0
                ? 'Görsel seçin veya yükleyin'
                : count + ' görsel seçildi';
        }
    }

    // ─── Modal ──────────────────────────────────────────────
    function openGalleryModal() {
        var modalEl = el('editorImageGallery');
        if (!modalEl) return;
        if (!modalInstance) modalInstance = new bootstrap.Modal(modalEl);
        modalInstance.show();
    }

    function closeGalleryModal() {
        if (modalInstance) modalInstance.hide();
    }

    // ─── Load Gallery ───────────────────────────────────────
    function loadGallery() {
        var loading = el('eigLoading');
        var empty = el('eigEmpty');
        var grid = el('eigGrid');

        if (loading) loading.classList.remove('d-none');
        if (empty) empty.classList.add('d-none');
        if (grid) grid.innerHTML = '';

        fetch(URLS.list, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (loading) loading.classList.add('d-none');
            if (!data.images || data.images.length === 0) {
                if (empty) empty.classList.remove('d-none');
                return;
            }
            renderGallery(data.images);
        })
        .catch(function () {
            if (loading) loading.classList.add('d-none');
            if (empty) {
                empty.classList.remove('d-none');
                var p = empty.querySelector('p');
                if (p) p.textContent = 'Görseller yüklenirken hata oluştu.';
            }
        });
    }

    // ─── Render Gallery ─────────────────────────────────────
    function renderGallery(images) {
        var grid = el('eigGrid');
        if (!grid) return;
        grid.innerHTML = '';

        images.forEach(function (img) {
            var col = document.createElement('div');
            col.className = 'col-4 col-sm-3 col-md-2';

            var card = document.createElement('div');
            card.className = 'eig-card';
            card.dataset.id = img.id;

            card.innerHTML =
                '<div class="eig-card__thumb">' +
                    '<img src="' + img.thumb_url + '" alt="' + esc(img.name) + '" loading="lazy">' +
                    '<div class="eig-card__check"><i class="bi bi-check-lg"></i></div>' +
                    '<button type="button" class="eig-card__del" title="Sil" data-id="' + img.id + '">' +
                        '<i class="bi bi-x-lg"></i>' +
                    '</button>' +
                '</div>' +
                '<div class="eig-card__name" title="' + esc(img.name) + '">' + esc(trunc(img.name, 14)) + '</div>';

            card.addEventListener('click', function (e) {
                if (e.target.closest('.eig-card__del')) return;
                toggleSelect(card, img);
            });

            var delBtn = card.querySelector('.eig-card__del');
            if (delBtn) {
                delBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    deleteImage(img.id, col);
                });
            }

            col.appendChild(card);
            grid.appendChild(col);
        });
    }

    // ─── Get page title for alt text ───────────────────────
    function getPageTitle() {
        var titleEl = document.getElementById('title') || document.getElementById('postTitle');
        return (titleEl && titleEl.value && titleEl.value.trim()) ? titleEl.value.trim() : '';
    }

    // ─── Insert Images ──────────────────────────────────────
    document.addEventListener('click', function (e) {
        if (!e.target || !e.target.closest('#eigInsertBtn')) return;
        if (selectedImages.length === 0 || !activeEditor) return;

        var altText = getPageTitle();

        var html = selectedImages.map(function (img) {
            var alt = altText || img.name;
            return '<img src="' + esc(img.url) + '" alt="' + esc(alt) + '" class="img-fluid img-w-100" loading="lazy" />';
        }).join('\n');

        activeEditor.insertContent(html);
        activeEditor = null;
        clearSelection();
        closeGalleryModal();
    });

    // ─── Delete Image ───────────────────────────────────────
    function deleteImage(id, colEl) {
        if (!confirm('Bu görseli silmek istediğinize emin misiniz?')) return;

        fetch(URLS.delete + id + ctxParam, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (!data.success) return;
            colEl.remove();

            var grid = el('eigGrid');
            var empty = el('eigEmpty');
            if (grid && grid.children.length === 0 && empty) empty.classList.remove('d-none');

            // Remove from selection if selected
            selectedImages = selectedImages.filter(function (s) { return s.id !== id; });
            updateSelectionUI();
        })
        .catch(function () { alert('Silme hatası.'); });
    }

    // ─── Upload ─────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        var dropzone = el('eigDropzone');
        var fileInput = el('eigFileInput');
        var placeholder = el('eigPlaceholder');
        var progress = el('eigProgress');
        var errorEl = el('eigError');

        if (!dropzone || !fileInput) return;

        dropzone.addEventListener('click', function () { fileInput.click(); });

        fileInput.addEventListener('change', function () {
            if (this.files && this.files.length > 0) {
                uploadFiles(this.files);
                this.value = '';
            }
        });

        dropzone.addEventListener('dragover', function (e) {
            e.preventDefault();
            dropzone.classList.add('eig-upload__dropzone--dragover');
        });
        dropzone.addEventListener('dragleave', function () {
            dropzone.classList.remove('eig-upload__dropzone--dragover');
        });
        dropzone.addEventListener('drop', function (e) {
            e.preventDefault();
            dropzone.classList.remove('eig-upload__dropzone--dragover');
            if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                uploadFiles(e.dataTransfer.files);
            }
        });

        function uploadFiles(files) {
            hideError();
            var queue = [];
            for (var i = 0; i < files.length; i++) {
                var f = files[i];
                if (!f.type.startsWith('image/')) {
                    showError('Sadece görsel dosyaları yüklenebilir.');
                    return;
                }
                if (f.size > 1024 * 1024) {
                    showError('"' + f.name + '" 1 MB sınırını aşıyor.');
                    return;
                }
                queue.push(f);
            }

            if (placeholder) placeholder.classList.add('d-none');
            if (progress) progress.classList.remove('d-none');

            var uploaded = 0;
            queue.forEach(function (file) {
                var fd = new FormData();
                fd.append('file', file);
                if (contextUserId) fd.append('context_user_id', contextUserId);

                fetch(URLS.upload, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: fd
                })
                .then(function (res) {
                    if (!res.ok) {
                        return res.json().then(function (d) {
                            var msg = d.message || 'Yükleme hatası';
                            if (d.errors && d.errors.file) msg = d.errors.file[0];
                            throw new Error(msg);
                        });
                    }
                    return res.json();
                })
                .then(function () {
                    uploaded++;
                    if (uploaded === queue.length) {
                        if (placeholder) placeholder.classList.remove('d-none');
                        if (progress) progress.classList.add('d-none');
                        loadGallery();
                    }
                })
                .catch(function (err) {
                    uploaded++;
                    if (uploaded === queue.length) {
                        if (placeholder) placeholder.classList.remove('d-none');
                        if (progress) progress.classList.add('d-none');
                    }
                    showError(err.message || 'Yükleme hatası.');
                });
            });
        }

        function showError(msg) {
            if (errorEl) { errorEl.textContent = msg; errorEl.classList.remove('d-none'); }
        }
        function hideError() {
            if (errorEl) { errorEl.textContent = ''; errorEl.classList.add('d-none'); }
        }
    });

    // ─── Helpers ────────────────────────────────────────────
    function esc(str) {
        var d = document.createElement('div');
        d.appendChild(document.createTextNode(str));
        return d.innerHTML;
    }
    function trunc(str, len) {
        return str.length > len ? str.substring(0, len) + '…' : str;
    }
    function formatSize(b) {
        if (b < 1024) return b + ' B';
        if (b < 1048576) return (b / 1024).toFixed(1) + ' KB';
        return (b / 1048576).toFixed(1) + ' MB';
    }
})();
