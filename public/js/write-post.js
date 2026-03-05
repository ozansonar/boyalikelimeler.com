(function () {
    'use strict';

    /* -- Tom Select: Category Dropdown -------------------- */
    var categoryEl = document.getElementById('postCategory');
    if (categoryEl) {
        new TomSelect('#postCategory', {
            allowEmptyOption: false,
            sortField: { field: 'text', direction: 'asc' },
            placeholder: 'Kategori arayın veya seçin...',
            controlInput: '<input type="text" autocomplete="off" size="1">',
            render: {
                optgroup_header: function (data) {
                    return '<div class="optgroup-header">' +
                        '<i class="fa-solid fa-folder me-1"></i>' + data.label +
                        '</div>';
                }
            }
        });
    }

    /* -- TinyMCE 7: Content Editor ------------------------- */
    var editorEl = document.getElementById('postEditor');
    if (editorEl && typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: '#postEditor',
            base_url: 'https://cdn.jsdelivr.net/npm/tinymce@7.6.1',
            suffix: '.min',
            license_key: 'gpl',
            skin: 'oxide-dark',
            content_css: 'dark',
            language: 'tr',
            language_url: 'https://cdn.jsdelivr.net/npm/tinymce-i18n@24.11.25/langs7/tr.min.js',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount fullscreen preview code',
            toolbar: [
                'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor',
                'alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link imagegallery media table | blockquote codesample | charmap emoticons | fullscreen code | removeformat'
            ],
            menubar: 'file edit view insert format tools table',
            height: 900,
            placeholder: 'Yazınızı buraya yazın... Hayal gücünüzün sınırı yok.',
            promotion: false,
            branding: false,
            automatic_uploads: true,
            relative_urls: false,
            remove_script_host: false,
            convert_urls: false,
            entity_encoding: 'raw',
            images_upload_handler: window.editorImagesUploadHandler,
            setup: window.editorImagesSetup,
            image_class_list: [{ title: 'Responsive', value: 'img-fluid' }],
            content_style: 'body { font-family: Inter, system-ui, -apple-system, sans-serif; font-size: 14px; color: #F5F5F0; line-height: 1.8; } h1,h2,h3,h4,h5,h6 { font-family: Playfair Display, Georgia, serif; color: #D4AF37; } blockquote { border-left: 3px solid #D4AF37; padding-left: 1rem; color: #C5C8CE; font-style: italic; } a { color: #D4AF37; } img { max-width: 100%; height: auto; border-radius: 0.5rem; } table { border-collapse: collapse; width: 100%; } th, td { border: 1px solid rgba(155,158,163,0.3); padding: 0.5rem; } pre { background: #2A2A2F; border-radius: 0.5rem; padding: 1rem; color: #C5C8CE; } code { background: #2A2A2F; padding: 2px 6px; border-radius: 3px; color: #E2CFA0; font-size: 0.9em; }'
        });
    }

    /* -- Title Character Counter -------------------------- */
    var titleInput = document.getElementById('postTitle');
    var titleCount = document.getElementById('titleCharCount');

    if (titleInput && titleCount) {
        titleInput.addEventListener('input', function () {
            titleCount.textContent = this.value.length;
        });
    }

    /* -- Excerpt Character Counter ------------------------ */
    var excerptInput = document.getElementById('postExcerpt');
    var excerptCount = document.getElementById('excerptCharCount');

    if (excerptInput && excerptCount) {
        excerptInput.addEventListener('input', function () {
            excerptCount.textContent = this.value.length;
        });
    }

    /* -- Cover Image Upload ------------------------------- */
    var coverInput = document.getElementById('coverInput');
    var coverDropZone = document.getElementById('coverDropZone');
    var coverPlaceholder = document.getElementById('coverPlaceholder');
    var coverPreview = document.getElementById('coverPreview');
    var coverPreviewImg = document.getElementById('coverPreviewImg');
    var coverRemoveBtn = document.getElementById('coverRemoveBtn');
    var removeCoverFlag = document.getElementById('removeCoverFlag');

    var MAX_COVER_SIZE = 2 * 1024 * 1024; // 2 MB

    function showCoverPreview(file) {
        if (!file || !file.type.startsWith('image/')) return;
        if (file.size > MAX_COVER_SIZE) {
            if (window.BkModal) window.BkModal.warning('Kapak görseli en fazla 2 MB olabilir.');
            coverInput.value = '';
            return;
        }
        var reader = new FileReader();
        reader.onload = function (e) {
            coverPreviewImg.src = e.target.result;
            coverPlaceholder.classList.add('d-none');
            coverPreview.classList.add('wpost-cover-upload__preview--active');
        };
        reader.readAsDataURL(file);
    }

    function removeCoverImage() {
        coverInput.value = '';
        coverPreviewImg.src = '';
        coverPlaceholder.classList.remove('d-none');
        coverPreview.classList.remove('wpost-cover-upload__preview--active');

        if (removeCoverFlag) {
            removeCoverFlag.value = '1';
        }
    }

    if (coverDropZone) {
        coverDropZone.addEventListener('click', function (e) {
            if (e.target.closest('#coverRemoveBtn')) return;
            coverInput.click();
        });
    }

    if (coverInput) {
        coverInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                showCoverPreview(this.files[0]);
                if (removeCoverFlag) {
                    removeCoverFlag.value = '0';
                }
            }
        });
    }

    if (coverRemoveBtn) {
        coverRemoveBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            removeCoverImage();
        });
    }

    /* -- Drag & Drop -------------------------------------- */
    if (coverDropZone) {
        coverDropZone.addEventListener('dragover', function (e) {
            e.preventDefault();
            this.classList.add('wpost-cover-upload--dragover');
        });

        coverDropZone.addEventListener('dragleave', function () {
            this.classList.remove('wpost-cover-upload--dragover');
        });

        coverDropZone.addEventListener('drop', function (e) {
            e.preventDefault();
            this.classList.remove('wpost-cover-upload--dragover');
            var files = e.dataTransfer.files;
            if (files && files[0]) {
                coverInput.files = files;
                showCoverPreview(files[0]);
                if (removeCoverFlag) {
                    removeCoverFlag.value = '0';
                }
            }
        });
    }

})();
