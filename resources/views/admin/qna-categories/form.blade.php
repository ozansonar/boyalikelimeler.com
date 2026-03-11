@extends('layouts.admin')

@php $isEdit = isset($category); @endphp

@section('title', ($isEdit ? 'Kategori Düzenle' : 'Yeni Kategori') . ' — Söz Meydanı — Admin')

@section('content')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3" data-aos="fade-down" data-aos-duration="400">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link"><i class="bi bi-house me-1"></i>Ana Sayfa</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.qna.categories.index') }}" class="breadcrumb-link">Söz Meydanı Kategorileri</a>
            </li>
            <li class="breadcrumb-item active text-teal">{{ $isEdit ? 'Düzenle' : 'Yeni Kategori' }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header d-flex align-items-start align-items-sm-center justify-content-between flex-column flex-sm-row gap-3 mb-4" data-aos="fade-down">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.qna.categories.index') }}" class="btn-glass" title="Geri Dön">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="page-title mb-0">{{ $isEdit ? $category->name . ' — Düzenle' : 'Yeni Kategori Oluştur' }}</h1>
                <p class="page-subtitle mb-0">Söz Meydanı soru/cevap kategorisi</p>
            </div>
        </div>
    </div>

    <form method="POST"
          action="{{ $isEdit ? route('admin.qna.categories.update', $category->id) : route('admin.qna.categories.store') }}">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <!-- Kategori Bilgileri -->
        <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="100">
            <div class="card-header-custom">
                <div class="form-section-header mb-0">
                    <div class="form-section-icon bg-icon-teal"><i class="bi bi-folder"></i></div>
                    <div>
                        <h6 class="mb-0">Kategori Bilgileri</h6>
                        <small class="text-muted">Kategorinin temel bilgilerini girin</small>
                    </div>
                </div>
            </div>
            <div class="card-body-custom">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="name">Kategori Adı <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name', $category->name ?? '') }}"
                               placeholder="Kategori adını yazın..." required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Kategorinin görünen adı</div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="slug">Slug <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror"
                               id="slug" name="slug" value="{{ old('slug', $category->slug ?? '') }}"
                               placeholder="otomatik-olusturulur" required>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">URL'de kullanılacak kısa ad (otomatik oluşturulur)</div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="icon">İkon (Font Awesome class) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('icon') is-invalid @enderror"
                               id="icon" name="icon" value="{{ old('icon', $category->icon ?? 'fa-solid fa-comments') }}"
                               placeholder="fa-solid fa-book-open" required>
                        @error('icon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Font Awesome ikon class'ı (ör: fa-solid fa-book-open)</div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="color_class">Renk Teması <span class="text-danger">*</span></label>
                        @php
                            $colorOptions = [
                                'qna-cat-card__icon-wrap--edebiyat'  => ['label' => 'Altın (Edebiyat)', 'color' => '#D4AF37'],
                                'qna-cat-card__icon-wrap--psikoloji' => ['label' => 'Mor (Psikoloji)', 'color' => '#a78bfa'],
                                'qna-cat-card__icon-wrap--aile'      => ['label' => 'Kırmızı (Aile)', 'color' => '#fb7185'],
                                'qna-cat-card__icon-wrap--yazi'      => ['label' => 'Mavi (Yazı)', 'color' => '#93c5fd'],
                                'qna-cat-card__icon-wrap--gorsel'    => ['label' => 'Pembe (Görsel)', 'color' => '#f472b6'],
                                'qna-cat-card__icon-wrap--sohbet'    => ['label' => 'Turuncu (Sohbet)', 'color' => '#fbbf24'],
                                'qna-cat-card__icon-wrap--serbest'   => ['label' => 'Yeşil (Serbest)', 'color' => '#5dd39e'],
                                'qna-cat-card__icon-wrap--astroloji' => ['label' => 'Cyan (Astroloji)', 'color' => '#67e8f9'],
                            ];
                            $currentColor = old('color_class', $category->color_class ?? 'qna-cat-card__icon-wrap--edebiyat');
                        @endphp
                        <select class="form-select @error('color_class') is-invalid @enderror"
                                id="color_class" name="color_class" required>
                            @foreach($colorOptions as $value => $option)
                                <option value="{{ $value }}" @selected($currentColor === $value)>
                                    {{ $option['label'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('color_class')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Kategori kartındaki ikon renk teması
                            <span id="colorPreview" class="color-preview-dot"></span>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="sort_order">Sıralama</label>
                        <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                               id="sort_order" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}"
                               min="0" placeholder="0">
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Düşük değer = Daha üstte görünür</div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label mb-3">Durum</label>
                        <div class="ca-toggle-item">
                            <div class="ca-toggle-info">
                                <span>Aktif</span>
                                <small>Kategori sitede görünür olur</small>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                       {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="description">Açıklama</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3"
                                  placeholder="Kategori hakkında kısa bir açıklama yazın...">{{ old('description', $category->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Opsiyonel — Kategori listeleme sayfasında gösterilir</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="150">
            <div class="card-body-custom">
                <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center justify-content-between gap-3">
                    <a href="{{ route('admin.qna.categories.index') }}" class="btn-glass">
                        <i class="bi bi-x-lg me-1"></i>Vazgeç
                    </a>
                    <button type="submit" class="btn-teal">
                        <i class="bi bi-check-lg me-1"></i>{{ $isEdit ? 'Güncelle' : 'Oluştur' }}
                    </button>
                </div>
            </div>
        </div>
    </form>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const colorSelect = document.getElementById('color_class');
        const colorPreview = document.getElementById('colorPreview');
        const colorMap = {
            'qna-cat-card__icon-wrap--edebiyat': '#D4AF37',
            'qna-cat-card__icon-wrap--psikoloji': '#a78bfa',
            'qna-cat-card__icon-wrap--aile': '#fb7185',
            'qna-cat-card__icon-wrap--yazi': '#93c5fd',
            'qna-cat-card__icon-wrap--gorsel': '#f472b6',
            'qna-cat-card__icon-wrap--sohbet': '#fbbf24',
            'qna-cat-card__icon-wrap--serbest': '#5dd39e',
            'qna-cat-card__icon-wrap--astroloji': '#67e8f9'
        };

        colorPreview.style.backgroundColor = colorMap[colorSelect.value] || '#D4AF37';

        colorSelect.addEventListener('change', function () {
            colorPreview.style.backgroundColor = colorMap[this.value] || '#D4AF37';
        });
    });
</script>
@endpush
