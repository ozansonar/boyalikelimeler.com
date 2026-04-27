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
            onItemAdd: function () { this.blur(); },
            render: {
                optgroup_header: function (data) {
                    return '<div class="optgroup-header">' +
                        '<i class="fa-solid fa-folder me-1"></i>' + data.label +
                        '</div>';
                }
            }
        });
    }

    /* -- Tom Select: Work Type Dropdown --------------------- */
    var workTypeEl = document.getElementById('workType');
    if (workTypeEl) {
        new TomSelect('#workType', {
            allowEmptyOption: false,
            placeholder: 'Eser türü seçin...',
            controlInput: '<input type="text" autocomplete="off" size="1">',
            onItemAdd: function () { this.blur(); }
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
                'undo redo | pasteContent | blocks fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor',
                'alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link imagegallery media table | blockquote codesample | charmap emoticons | fullscreen code | removeformat'
            ],
            menubar: 'file edit view insert format tools table',
            height: 900,
            placeholder: 'Yazınızı buraya yazın... Hayal gücünüzün sınırı yok.',
            contextmenu: false,
            promotion: false,
            branding: false,
            automatic_uploads: true,
            relative_urls: false,
            remove_script_host: false,
            convert_urls: false,
            entity_encoding: 'raw',
            valid_children: '+div[img]',
            extended_valid_elements: 'div[class]',
            object_resizing: true,
            image_advtab: true,
            image_caption: true,
            image_title: true,
            image_description: true,
            image_dimensions: true,
            editimage_toolbar: 'imageoptions',
            images_upload_handler: window.editorImagesUploadHandler,
            setup: function (editor) {
                if (typeof window.editorImagesSetup === 'function') {
                    window.editorImagesSetup(editor);
                }

                /* ── Paste Button ──────────────────────────── */
                editor.ui.registry.addButton('pasteContent', {
                    icon: 'paste',
                    tooltip: 'Yapıştır',
                    onAction: function () {
                        if (navigator.clipboard && navigator.clipboard.readText) {
                            navigator.clipboard.readText().then(function (text) {
                                if (text) {
                                    var safe = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                                    var html = safe.replace(/\n/g, '<br>');
                                    editor.insertContent(html);
                                }
                            }).catch(function () {
                                editor.notificationManager.open({
                                    text: 'Yapıştırmak için Ctrl+V (veya Cmd+V) kullanın.',
                                    type: 'info',
                                    timeout: 3000
                                });
                            });
                        } else {
                            editor.notificationManager.open({
                                text: 'Yapıştırmak için Ctrl+V (veya Cmd+V) kullanın.',
                                type: 'info',
                                timeout: 3000
                            });
                        }
                    }
                });

                /* ── Image Size Buttons ─────────────────────── */
                var IMG_SIZES = [
                    { name: 'imgw20',  label: 'XS',  cls: 'img-w-20',  pct: '20%'  },
                    { name: 'imgw40',  label: 'S',   cls: 'img-w-40',  pct: '40%'  },
                    { name: 'imgw60',  label: 'M',   cls: 'img-w-60',  pct: '60%'  },
                    { name: 'imgw80',  label: 'L',   cls: 'img-w-80',  pct: '80%'  },
                    { name: 'imgw100', label: 'XL',  cls: 'img-w-100', pct: '100%' }
                ];

                var ALL_W_CLASSES = IMG_SIZES.map(function (s) { return s.cls; });

                function getImgNode() {
                    var node = editor.selection.getNode();
                    return node && node.nodeName === 'IMG' ? node : null;
                }

                function setImgWidth(img, cls) {
                    ALL_W_CLASSES.forEach(function (c) { editor.dom.removeClass(img, c); });
                    editor.dom.addClass(img, cls);
                    editor.undoManager.add();
                    editor.nodeChanged();
                }

                function hasImgWidth(img, cls) {
                    return editor.dom.hasClass(img, cls);
                }

                IMG_SIZES.forEach(function (size) {
                    editor.ui.registry.addToggleButton(size.name, {
                        text: size.label,
                        tooltip: 'Boyut: ' + size.pct,
                        onAction: function () {
                            var img = getImgNode();
                            if (img) setImgWidth(img, size.cls);
                        },
                        onSetup: function (api) {
                            var handler = function () {
                                var img = getImgNode();
                                api.setActive(img ? hasImgWidth(img, size.cls) : false);
                            };
                            editor.on('NodeChange', handler);
                            return function () { editor.off('NodeChange', handler); };
                        }
                    });
                });

                /* ── Image Align Buttons ────────────────────── */
                var IMG_ALIGNS = [
                    { name: 'imgAlignLeft',   icon: 'align-left',   cls: 'img-align-left',   tip: 'Sola Yasla' },
                    { name: 'imgAlignCenter', icon: 'align-center', cls: 'img-align-center', tip: 'Ortala'      },
                    { name: 'imgAlignRight',  icon: 'align-right',  cls: 'img-align-right',  tip: 'Sağa Yasla'  }
                ];

                var ALL_A_CLASSES = IMG_ALIGNS.map(function (a) { return a.cls; });

                function setImgAlign(img, cls) {
                    ALL_A_CLASSES.forEach(function (c) { editor.dom.removeClass(img, c); });
                    editor.dom.addClass(img, cls);
                    editor.undoManager.add();
                    editor.nodeChanged();
                }

                IMG_ALIGNS.forEach(function (align) {
                    editor.ui.registry.addToggleButton(align.name, {
                        icon: align.icon,
                        tooltip: align.tip,
                        onAction: function () {
                            var img = getImgNode();
                            if (img) setImgAlign(img, align.cls);
                        },
                        onSetup: function (api) {
                            var handler = function () {
                                var img = getImgNode();
                                api.setActive(img ? editor.dom.hasClass(img, align.cls) : false);
                            };
                            editor.on('NodeChange', handler);
                            return function () { editor.off('NodeChange', handler); };
                        }
                    });
                });

                /* ── Context Toolbar: Image (appears on image click) ── */
                editor.ui.registry.addContextToolbar('imagetools', {
                    predicate: function (node) {
                        return node.nodeName === 'IMG' && !node.closest('.img-grid');
                    },
                    items: 'imgw20 imgw40 imgw60 imgw80 imgw100 | imgAlignLeft imgAlignCenter imgAlignRight',
                    position: 'node',
                    scope: 'node'
                });

                /* ── Grid Column Buttons ───────────────────── */
                var GRID_COLS = [
                    { name: 'gridCol2', label: '2', cls: 'img-grid-2', tip: '2 Sütun' },
                    { name: 'gridCol3', label: '3', cls: 'img-grid-3', tip: '3 Sütun' },
                    { name: 'gridCol4', label: '4', cls: 'img-grid-4', tip: '4 Sütun' }
                ];
                var ALL_GRID_CLASSES = GRID_COLS.map(function (g) { return g.cls; });

                function getGridNode() {
                    var node = editor.selection.getNode();
                    if (node.classList && node.classList.contains('img-grid')) return node;
                    var parent = node.closest ? node.closest('.img-grid') : null;
                    return parent;
                }

                GRID_COLS.forEach(function (col) {
                    editor.ui.registry.addToggleButton(col.name, {
                        text: col.label,
                        tooltip: col.tip,
                        onAction: function () {
                            var grid = getGridNode();
                            if (grid) {
                                ALL_GRID_CLASSES.forEach(function (c) { editor.dom.removeClass(grid, c); });
                                editor.dom.addClass(grid, col.cls);
                                editor.undoManager.add();
                                editor.nodeChanged();
                            }
                        },
                        onSetup: function (api) {
                            var handler = function () {
                                var grid = getGridNode();
                                api.setActive(grid ? editor.dom.hasClass(grid, col.cls) : false);
                            };
                            editor.on('NodeChange', handler);
                            return function () { editor.off('NodeChange', handler); };
                        }
                    });
                });

                /* Remove Grid button */
                editor.ui.registry.addButton('gridRemove', {
                    icon: 'remove',
                    tooltip: 'Gridi Kaldır (görselleri ayır)',
                    onAction: function () {
                        var grid = getGridNode();
                        if (grid) {
                            var images = grid.querySelectorAll('img');
                            var frag = document.createDocumentFragment();
                            images.forEach(function (img) {
                                var p = document.createElement('p');
                                p.appendChild(img.cloneNode(true));
                                frag.appendChild(p);
                            });
                            grid.parentNode.replaceChild(frag, grid);
                            editor.undoManager.add();
                            editor.nodeChanged();
                        }
                    }
                });

                /* ── Context Toolbar: Grid (appears when inside grid) ── */
                editor.ui.registry.addContextToolbar('gridtools', {
                    predicate: function (node) {
                        if (node.classList && node.classList.contains('img-grid')) return true;
                        return node.closest ? !!node.closest('.img-grid') : false;
                    },
                    items: 'gridCol2 gridCol3 gridCol4 | gridRemove',
                    position: 'node',
                    scope: 'node'
                });
            },
            image_class_list: [
                { title: 'Tam Genişlik (XL)', value: 'img-fluid img-w-100' },
                { title: 'Büyük (L — 80%)', value: 'img-fluid img-w-80' },
                { title: 'Orta (M — 60%)', value: 'img-fluid img-w-60' },
                { title: 'Küçük (S — 40%)', value: 'img-fluid img-w-40' },
                { title: 'Çok Küçük (XS — 20%)', value: 'img-fluid img-w-20' }
            ],
            content_style: 'body { font-family: Inter, system-ui, -apple-system, sans-serif; font-size: 14px; color: #F5F5F0; line-height: 1.8; } h1,h2,h3,h4,h5,h6 { font-family: Playfair Display, Georgia, serif; color: #D4AF37; } blockquote { border-left: 3px solid #D4AF37; padding-left: 1rem; color: #C5C8CE; font-style: italic; } a { color: #D4AF37; } img { max-width: 100%; height: auto; border-radius: 0.5rem; cursor: pointer; } img.img-w-20 { max-width: 20%; } img.img-w-40 { max-width: 40%; } img.img-w-60 { max-width: 60%; } img.img-w-80 { max-width: 80%; } img.img-w-100 { max-width: 100%; } img.img-align-left { float: left; margin: 0 1rem 1rem 0; } img.img-align-right { float: right; margin: 0 0 1rem 1rem; } img.img-align-center { display: block; margin: 1rem auto; } .img-grid { display: grid; gap: 0.75rem; margin: 1rem 0; } .img-grid img { width: 100%; height: 100%; object-fit: cover; border-radius: 0.5rem; } .img-grid-2 { grid-template-columns: repeat(2, 1fr); } .img-grid-3 { grid-template-columns: repeat(3, 1fr); } .img-grid-4 { grid-template-columns: repeat(4, 1fr); } figure { margin: 1rem 0; } figure.align-left { float: left; margin: 0 1rem 1rem 0; max-width: 50%; } figure.align-right { float: right; margin: 0 0 1rem 1rem; max-width: 50%; } figure.align-center { display: block; margin: 1rem auto; text-align: center; } figcaption { font-size: 0.85em; color: #9B9EA3; text-align: center; margin-top: 0.5rem; font-style: italic; } table { border-collapse: collapse; width: 100%; } th, td { border: 1px solid rgba(155,158,163,0.3); padding: 0.5rem; } pre { background: #2A2A2F; border-radius: 0.5rem; padding: 1rem; color: #C5C8CE; } code { background: #2A2A2F; padding: 2px 6px; border-radius: 3px; color: #E2CFA0; font-size: 0.9em; }'
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
