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

                <!-- Kapak Görseli -->
                <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-header-custom">
                        <div class="form-section-header mb-0">
                            <div class="form-section-icon bg-icon-pink"><i class="bi bi-image"></i></div>
                            <div>
                                <h6 class="mb-0">Kapak Görseli</h6>
                                <small class="text-muted">JPG, PNG veya WebP — Maks. 5MB</small>
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

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tinymce@7.6.1/tinymce.min.js"></script>
    <script>
    tinymce.init({
        selector: '#bodyEditor',
        height: 500,
        menubar: false,
        skin: 'oxide-dark',
        content_css: 'dark',
        plugins: 'lists link image code fullscreen',
        toolbar: 'undo redo | blocks | bold italic underline | bullist numlist | link image | code fullscreen',
        branding: false,
        promotion: false,
    });
    </script>
@endpush
