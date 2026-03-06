@extends('layouts.admin')

@section('title', 'Eser Düzenle — ' . $work->title)

@section('content')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb breadcrumb-reset fs-13">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link"><i class="bi bi-house me-1"></i>Ana Sayfa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.literary-works.index') }}" class="breadcrumb-link">Edebiyat Eserleri</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.literary-works.show', $work->id) }}" class="breadcrumb-link">{{ Str::limit($work->title, 20) }}</a></li>
            <li class="breadcrumb-item active text-teal">Düzenle</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header d-flex align-items-start align-items-sm-center justify-content-between flex-column flex-sm-row gap-3 mb-4" data-aos="fade-down">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.literary-works.show', $work->id) }}" class="btn-glass" title="Geri Dön"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h1 class="page-title mb-0">Eser Düzenle</h1>
                <p class="page-subtitle mb-0">{{ $work->title }}</p>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <span class="usr-status-badge {{ $work->status->badgeClass() }}">{{ $work->status->label() }}</span>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.literary-works.update', $work) }}" enctype="multipart/form-data" novalidate>
        @csrf
        @method('PUT')

        <div class="row g-4">

            <!-- Sol: İçerik -->
            <div class="col-lg-8">

                <!-- Başlık -->
                <div class="card-dark mb-4" data-aos="fade-up">
                    <div class="card-header-custom">
                        <div class="form-section-header mb-0">
                            <div class="form-section-icon bg-icon-teal"><i class="bi bi-fonts"></i></div>
                            <div>
                                <h6 class="mb-0">Eser Başlığı</h6>
                                <small class="text-muted">Başlık ve kısa açıklama</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body-custom">
                        <div class="mb-3">
                            <label class="form-label" for="title">Başlık <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $work->title) }}" required maxlength="200">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-0">
                            <label class="form-label" for="excerpt">Kısa Açıklama</label>
                            <textarea class="form-control @error('excerpt') is-invalid @enderror" id="excerpt" name="excerpt" rows="3" maxlength="300" placeholder="Eser hakkında kısa bir özet...">{{ old('excerpt', $work->excerpt) }}</textarea>
                            @error('excerpt')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- İçerik -->
                <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="50">
                    <div class="card-header-custom">
                        <div class="form-section-header mb-0">
                            <div class="form-section-icon bg-icon-blue"><i class="bi bi-journal-text"></i></div>
                            <div>
                                <h6 class="mb-0">Eser İçeriği</h6>
                                <small class="text-muted">Eser metnini düzenleyin</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body-custom">
                        <textarea id="bodyEditor" name="body" class="@error('body') is-invalid @enderror">{{ old('body', $work->body) }}</textarea>
                        @error('body')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

            </div>

            <!-- Sağ: Ayarlar -->
            <div class="col-lg-4">

                <!-- Kategori -->
                <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="50">
                    <div class="card-header-custom">
                        <div class="form-section-header mb-0">
                            <div class="form-section-icon bg-icon-orange"><i class="bi bi-folder"></i></div>
                            <div>
                                <h6 class="mb-0">Kategori</h6>
                                <small class="text-muted">Eserin kategorisini seçin</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body-custom">
                        <select class="form-select @error('literary_category_id') is-invalid @enderror" name="literary_category_id" required>
                            <option value="">Kategori seçiniz</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('literary_category_id', $work->literary_category_id) == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('literary_category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Eser Türü -->
                <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="75">
                    <div class="card-header-custom">
                        <div class="form-section-header mb-0">
                            <div class="form-section-icon bg-icon-teal"><i class="bi bi-bookmark"></i></div>
                            <div>
                                <h6 class="mb-0">Eser Türü</h6>
                                <small class="text-muted">Yazılı veya görsel eser</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body-custom">
                        <select class="form-select @error('work_type') is-invalid @enderror" name="work_type" required>
                            <option value="">Tür seçiniz</option>
                            @foreach(\App\Enums\LiteraryWorkType::cases() as $type)
                                <option value="{{ $type->value }}" @selected(old('work_type', $work->work_type?->value) === $type->value)>{{ $type->label() }}</option>
                            @endforeach
                        </select>
                        @error('work_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Kapak Görseli -->
                <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-header-custom">
                        <div class="form-section-header mb-0">
                            <div class="form-section-icon bg-icon-pink"><i class="bi bi-image"></i></div>
                            <div>
                                <h6 class="mb-0">Kapak Görseli</h6>
                                <small class="text-muted">JPG, PNG veya WebP — Maks. 2MB</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body-custom">
                        @if($work->cover_image)
                            <div class="mb-3">
                                <img src="/uploads/{{ $work->cover_image }}" alt="{{ $work->title }}" class="img-fluid rounded mb-2" loading="lazy">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remove_cover" value="1" id="removeCover">
                                    <label class="form-check-label text-muted small" for="removeCover">Mevcut görseli kaldır</label>
                                </div>
                            </div>
                        @endif
                        <input type="file" class="form-control @error('cover_image') is-invalid @enderror" name="cover_image" accept="image/jpeg,image/png,image/webp">
                        @error('cover_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Kaydet -->
                <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="150">
                    <div class="card-body-custom">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn-teal">
                                <i class="bi bi-check-circle me-1"></i>Değişiklikleri Kaydet
                            </button>
                            <a href="{{ route('admin.literary-works.show', $work->id) }}" class="btn-glass text-center">
                                <i class="bi bi-x me-1"></i>İptal
                            </a>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </form>

    {{-- Editor Image Gallery Modal --}}
    <x-editor-image-gallery />

@endsection

@push('scripts')
    <script src="{{ asset('vendor/tinymce/7.6.1/tinymce.min.js') }}"></script>
    <script>window.editorImageContextUserId = {{ $work->user_id }};</script>
    <script src="{{ asset('js/editor-image-gallery.js') }}"></script>
    <script>
    tinymce.init({
        selector: '#bodyEditor',
        height: 500,
        menubar: false,
        skin: 'oxide-dark',
        content_css: 'dark',
        plugins: 'lists link image code fullscreen',
        toolbar: 'undo redo | blocks | bold italic underline | bullist numlist | link imagegallery | code fullscreen',
        branding: false,
        promotion: false,
        automatic_uploads: true,
        relative_urls: false,
        remove_script_host: false,
        convert_urls: false,
        entity_encoding: 'raw',
        valid_children: '+div[img]',
        extended_valid_elements: 'div[class]',
        images_upload_handler: window.editorImagesUploadHandler,
        setup: function (editor) {
            if (typeof window.editorImagesSetup === 'function') {
                window.editorImagesSetup(editor);
            }

            var IMG_SIZES = [
                { name: 'imgw20',  label: 'XS',  cls: 'img-w-20'  },
                { name: 'imgw40',  label: 'S',   cls: 'img-w-40'  },
                { name: 'imgw60',  label: 'M',   cls: 'img-w-60'  },
                { name: 'imgw80',  label: 'L',   cls: 'img-w-80'  },
                { name: 'imgw100', label: 'XL',  cls: 'img-w-100' }
            ];
            var ALL_W = IMG_SIZES.map(function (s) { return s.cls; });
            function getImg() { var n = editor.selection.getNode(); return n && n.nodeName === 'IMG' ? n : null; }
            IMG_SIZES.forEach(function (s) {
                editor.ui.registry.addToggleButton(s.name, {
                    text: s.label, tooltip: 'Boyut: ' + s.label,
                    onAction: function () { var img = getImg(); if (img) { ALL_W.forEach(function (c) { editor.dom.removeClass(img, c); }); editor.dom.addClass(img, s.cls); editor.undoManager.add(); editor.nodeChanged(); } },
                    onSetup: function (api) { var h = function () { var img = getImg(); api.setActive(img ? editor.dom.hasClass(img, s.cls) : false); }; editor.on('NodeChange', h); return function () { editor.off('NodeChange', h); }; }
                });
            });

            var IMG_ALIGNS = [
                { name: 'imgAlignLeft',   icon: 'align-left',   cls: 'img-align-left'   },
                { name: 'imgAlignCenter', icon: 'align-center', cls: 'img-align-center' },
                { name: 'imgAlignRight',  icon: 'align-right',  cls: 'img-align-right'  }
            ];
            var ALL_A = IMG_ALIGNS.map(function (a) { return a.cls; });
            IMG_ALIGNS.forEach(function (a) {
                editor.ui.registry.addToggleButton(a.name, {
                    icon: a.icon, tooltip: a.name.replace('imgAlign', ''),
                    onAction: function () { var img = getImg(); if (img) { ALL_A.forEach(function (c) { editor.dom.removeClass(img, c); }); editor.dom.addClass(img, a.cls); editor.undoManager.add(); editor.nodeChanged(); } },
                    onSetup: function (api) { var h = function () { var img = getImg(); api.setActive(img ? editor.dom.hasClass(img, a.cls) : false); }; editor.on('NodeChange', h); return function () { editor.off('NodeChange', h); }; }
                });
            });

            editor.ui.registry.addContextToolbar('imagetools', {
                predicate: function (node) { return node.nodeName === 'IMG' && !node.closest('.img-grid'); },
                items: 'imgw20 imgw40 imgw60 imgw80 imgw100 | imgAlignLeft imgAlignCenter imgAlignRight',
                position: 'node', scope: 'node'
            });

            // Grid column buttons
            var GRID_COLS = [
                { name: 'gridCol2', label: '2', cls: 'img-grid-2' },
                { name: 'gridCol3', label: '3', cls: 'img-grid-3' },
                { name: 'gridCol4', label: '4', cls: 'img-grid-4' }
            ];
            var ALL_G = GRID_COLS.map(function (g) { return g.cls; });
            function getGrid() { var n = editor.selection.getNode(); if (n.classList && n.classList.contains('img-grid')) return n; return n.closest ? n.closest('.img-grid') : null; }
            GRID_COLS.forEach(function (g) {
                editor.ui.registry.addToggleButton(g.name, {
                    text: g.label, tooltip: g.label + ' Sütun',
                    onAction: function () { var grid = getGrid(); if (grid) { ALL_G.forEach(function (c) { editor.dom.removeClass(grid, c); }); editor.dom.addClass(grid, g.cls); editor.undoManager.add(); editor.nodeChanged(); } },
                    onSetup: function (api) { var h = function () { var grid = getGrid(); api.setActive(grid ? editor.dom.hasClass(grid, g.cls) : false); }; editor.on('NodeChange', h); return function () { editor.off('NodeChange', h); }; }
                });
            });
            editor.ui.registry.addButton('gridRemove', {
                icon: 'remove', tooltip: 'Gridi Kaldır',
                onAction: function () { var grid = getGrid(); if (grid) { var imgs = grid.querySelectorAll('img'); var frag = document.createDocumentFragment(); imgs.forEach(function (img) { var p = document.createElement('p'); p.appendChild(img.cloneNode(true)); frag.appendChild(p); }); grid.parentNode.replaceChild(frag, grid); editor.undoManager.add(); editor.nodeChanged(); } }
            });
            editor.ui.registry.addContextToolbar('gridtools', {
                predicate: function (node) { if (node.classList && node.classList.contains('img-grid')) return true; return node.closest ? !!node.closest('.img-grid') : false; },
                items: 'gridCol2 gridCol3 gridCol4 | gridRemove',
                position: 'node', scope: 'node'
            });
        },
        image_class_list: [
            { title: 'Tam Genişlik (XL)', value: 'img-fluid img-w-100' },
            { title: 'Büyük (L — 80%)', value: 'img-fluid img-w-80' },
            { title: 'Orta (M — 60%)', value: 'img-fluid img-w-60' },
            { title: 'Küçük (S — 40%)', value: 'img-fluid img-w-40' },
            { title: 'Çok Küçük (XS — 20%)', value: 'img-fluid img-w-20' }
        ],
        content_style: 'img { max-width: 100%; height: auto; border-radius: 0.5rem; cursor: pointer; } img.img-w-20 { max-width: 20%; } img.img-w-40 { max-width: 40%; } img.img-w-60 { max-width: 60%; } img.img-w-80 { max-width: 80%; } img.img-w-100 { max-width: 100%; } img.img-align-left { float: left; margin: 0 1rem 1rem 0; } img.img-align-right { float: right; margin: 0 0 1rem 1rem; } img.img-align-center { display: block; margin: 1rem auto; } .img-grid { display: grid; gap: 0.75rem; margin: 1rem 0; } .img-grid img { width: 100%; height: 100%; object-fit: cover; border-radius: 0.5rem; } .img-grid-2 { grid-template-columns: repeat(2, 1fr); } .img-grid-3 { grid-template-columns: repeat(3, 1fr); } .img-grid-4 { grid-template-columns: repeat(4, 1fr); }',
    });
    </script>
@endpush
