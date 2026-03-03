/**
 * Editor Image Gallery — TinyMCE Integration
 *
 * Handles:
 * - images_upload_handler: auto-upload pasted/dropped images to server
 * - Custom toolbar button "imagegallery": opens gallery modal directly (no TinyMCE dialog)
 * - Gallery modal: upload, list, select, delete, insert into editor
 *
 * Dependencies: Bootstrap 5 Modal, Fetch API, CSRF meta tag
 */
(function () {
    'use strict';

    var CSRF = document.querySelector('meta[name="csrf-token"]');
    var csrfToken = CSRF ? CSRF.getAttribute('content') : '';

    var URLS = {
        list:   '/editor/images',
        upload: '/editor/images',
        delete: '/editor/images/'
    };

    // ─── State ──────────────────────────────────────────────
    var selectedImage = null;
    var activeEditor = null;
    var modalInstance = null;

    // ─── DOM Elements ───────────────────────────────────────
    function el(id) { return document.getElementById(id); }

    // ─── TinyMCE Upload Handler (auto-upload pasted/dropped images) ─
    window.editorImagesUploadHandler = function (blobInfo) {
        return new Promise(function (resolve, reject) {
            var formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());

            fetch(URLS.upload, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(function (res) {
                if (!res.ok) {
                    return res.json().then(function (data) {
                        var msg = data.message || 'Yükleme hatası';
                        if (data.errors && data.errors.file) {
                            msg = data.errors.file[0];
                        }
                        throw new Error(msg);
                    });
                }
                return res.json();
            })
            .then(function (data) {
                if (data.success && data.location) {
                    resolve(data.location);
                } else {
                    reject('Yükleme başarısız.');
                }
            })
            .catch(function (err) {
                reject(err.message || 'Yükleme sırasında hata oluştu.');
            });
        });
    };

    // ─── TinyMCE Custom Button Setup ────────────────────────
    // Register a custom toolbar button "imagegallery".
    // Opens our Bootstrap modal DIRECTLY — no TinyMCE dialog opens.
    window.editorImagesSetup = function (editor) {
        editor.ui.registry.addButton('imagegallery', {
            icon: 'image',
            tooltip: 'Görsel Galerisi',
            onAction: function () {
                activeEditor = editor;
                selectedImage = null;

                var insertBtn = el('eigInsertBtn');
                if (insertBtn) insertBtn.disabled = true;

                var infoEl = el('eigSelectedInfo');
                if (infoEl) infoEl.textContent = 'Görsel seçin veya yükleyin';

                // Clear previous selection highlight
                var prev = document.querySelector('.eig-card--selected');
                if (prev) prev.classList.remove('eig-card--selected');

                openGalleryModal();
                loadGallery();
            }
        });
    };

    // ─── Modal ──────────────────────────────────────────────
    function openGalleryModal() {
        var modalEl = el('editorImageGallery');
        if (!modalEl) return;

        if (!modalInstance) {
            modalInstance = new bootstrap.Modal(modalEl);
        }
        modalInstance.show();
    }

    function closeGalleryModal() {
        if (modalInstance) {
            modalInstance.hide();
        }
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
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
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
                var emptyP = empty.querySelector('p');
                if (emptyP) emptyP.textContent = 'Görseller yüklenirken hata oluştu.';
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
            col.className = 'col-6 col-sm-4 col-md-3';

            var card = document.createElement('div');
            card.className = 'eig-card';
            card.dataset.id = img.id;
            card.dataset.url = img.url;
            card.dataset.name = img.name;

            card.innerHTML =
                '<div class="eig-card__thumb">' +
                    '<img src="' + img.thumb_url + '" alt="' + escapeHtml(img.name) + '" loading="lazy">' +
                    '<div class="eig-card__overlay">' +
                        '<button type="button" class="eig-card__delete-btn" title="Sil" data-id="' + img.id + '">' +
                            '<i class="bi bi-trash3"></i>' +
                        '</button>' +
                    '</div>' +
                '</div>' +
                '<div class="eig-card__info">' +
                    '<span class="eig-card__name" title="' + escapeHtml(img.name) + '">' + escapeHtml(truncate(img.name, 20)) + '</span>' +
                    '<span class="eig-card__meta">' + formatSize(img.size) + ' · ' + img.width + 'x' + img.height + '</span>' +
                '</div>';

            card.addEventListener('click', function (e) {
                if (e.target.closest('.eig-card__delete-btn')) return;
                selectImage(card, img);
            });

            var deleteBtn = card.querySelector('.eig-card__delete-btn');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    deleteImage(img.id, col);
                });
            }

            col.appendChild(card);
            grid.appendChild(col);
        });
    }

    // ─── Select Image ───────────────────────────────────────
    function selectImage(cardEl, img) {
        var prev = document.querySelector('.eig-card--selected');
        if (prev) prev.classList.remove('eig-card--selected');

        cardEl.classList.add('eig-card--selected');
        selectedImage = img;

        var insertBtn = el('eigInsertBtn');
        if (insertBtn) insertBtn.disabled = false;

        var infoEl = el('eigSelectedInfo');
        if (infoEl) infoEl.textContent = img.name + ' (' + img.width + 'x' + img.height + ')';
    }

    // ─── Insert Image (via custom button — directly into editor) ─
    document.addEventListener('click', function (e) {
        if (e.target && e.target.closest('#eigInsertBtn')) {
            if (selectedImage && activeEditor) {
                activeEditor.insertContent(
                    '<img src="' + escapeHtml(selectedImage.url) + '"' +
                    ' alt="' + escapeHtml(selectedImage.name) + '"' +
                    ' loading="lazy" />'
                );
                activeEditor = null;
                selectedImage = null;
                closeGalleryModal();
            }
        }
    });

    // ─── Delete Image ───────────────────────────────────────
    function deleteImage(id, colEl) {
        if (!confirm('Bu görseli silmek istediğinize emin misiniz?')) return;

        fetch(URLS.delete + id, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.success) {
                colEl.remove();

                var grid = el('eigGrid');
                var empty = el('eigEmpty');
                if (grid && grid.children.length === 0 && empty) {
                    empty.classList.remove('d-none');
                }

                if (selectedImage && selectedImage.id === id) {
                    selectedImage = null;
                    var insertBtn = el('eigInsertBtn');
                    if (insertBtn) insertBtn.disabled = true;
                    var infoEl = el('eigSelectedInfo');
                    if (infoEl) infoEl.textContent = 'Görsel seçin veya yükleyin';
                }
            }
        })
        .catch(function () {
            alert('Silme işlemi sırasında hata oluştu.');
        });
    }

    // ─── Upload (Dropzone in modal) ─────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        var dropzone = el('eigDropzone');
        var fileInput = el('eigFileInput');
        var placeholder = el('eigPlaceholder');
        var progress = el('eigProgress');
        var errorEl = el('eigError');

        if (!dropzone || !fileInput) return;

        dropzone.addEventListener('click', function () {
            fileInput.click();
        });

        fileInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                uploadFile(this.files[0]);
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
            if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                uploadFile(e.dataTransfer.files[0]);
            }
        });

        function uploadFile(file) {
            if (!file.type.startsWith('image/')) {
                showError('Sadece görsel dosyaları yüklenebilir.');
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                showError('Dosya boyutu en fazla 5 MB olabilir.');
                return;
            }

            hideError();
            if (placeholder) placeholder.classList.add('d-none');
            if (progress) progress.classList.remove('d-none');

            var formData = new FormData();
            formData.append('file', file);

            fetch(URLS.upload, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(function (res) {
                if (!res.ok) {
                    return res.json().then(function (data) {
                        var msg = data.message || 'Yükleme hatası';
                        if (data.errors && data.errors.file) {
                            msg = data.errors.file[0];
                        }
                        throw new Error(msg);
                    });
                }
                return res.json();
            })
            .then(function (data) {
                if (placeholder) placeholder.classList.remove('d-none');
                if (progress) progress.classList.add('d-none');

                if (data.success) {
                    loadGallery();
                }
            })
            .catch(function (err) {
                if (placeholder) placeholder.classList.remove('d-none');
                if (progress) progress.classList.add('d-none');
                showError(err.message || 'Yükleme sırasında hata oluştu.');
            });
        }

        function showError(msg) {
            if (errorEl) {
                errorEl.textContent = msg;
                errorEl.classList.remove('d-none');
            }
        }

        function hideError() {
            if (errorEl) {
                errorEl.textContent = '';
                errorEl.classList.add('d-none');
            }
        }
    });

    // ─── Utility Helpers ────────────────────────────────────
    function escapeHtml(str) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    function truncate(str, len) {
        return str.length > len ? str.substring(0, len) + '…' : str;
    }

    function formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }
})();
