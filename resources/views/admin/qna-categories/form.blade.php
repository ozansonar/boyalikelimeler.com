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
    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4" data-aos="fade-down">
        <div>
            <h1 class="page-title">{{ $isEdit ? $category->name . ' — Düzenle' : 'Yeni Kategori Oluştur' }}</h1>
            <p class="page-subtitle">Söz Meydanı soru/cevap kategorisi</p>
        </div>
        <a href="{{ route('admin.qna.categories.index') }}" class="btn-glass">
            <i class="bi bi-arrow-left me-1"></i>Geri Dön
        </a>
    </div>

    <form method="POST"
          action="{{ $isEdit ? route('admin.qna.categories.update', $category->id) : route('admin.qna.categories.store') }}">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="100">
            <div class="card-body-custom">
                <h5 class="form-section-title"><i class="bi bi-folder me-2"></i>Kategori Bilgileri</h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label-dark" for="name">Kategori Adı <span class="text-danger">*</span></label>
                        <input type="text" class="form-control-dark @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name', $category->name ?? '') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-dark" for="slug">Slug <span class="text-danger">*</span></label>
                        <input type="text" class="form-control-dark @error('slug') is-invalid @enderror"
                               id="slug" name="slug" value="{{ old('slug', $category->slug ?? '') }}" required>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-dark" for="icon">İkon (Font Awesome class) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control-dark @error('icon') is-invalid @enderror"
                               id="icon" name="icon" value="{{ old('icon', $category->icon ?? 'fa-solid fa-comments') }}"
                               placeholder="fa-solid fa-book-open" required>
                        @error('icon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-dark" for="color_class">Renk Class'ı <span class="text-danger">*</span></label>
                        <input type="text" class="form-control-dark @error('color_class') is-invalid @enderror"
                               id="color_class" name="color_class" value="{{ old('color_class', $category->color_class ?? 'qna-cat-card__icon-wrap--default') }}"
                               placeholder="qna-cat-card__icon-wrap--edebiyat" required>
                        @error('color_class')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-dark" for="sort_order">Sıralama</label>
                        <input type="number" class="form-control-dark @error('sort_order') is-invalid @enderror"
                               id="sort_order" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" min="0">
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-dark" for="is_active">Durum</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                   {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label text-clr-secondary" for="is_active">Aktif</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label-dark" for="description">Açıklama</label>
                        <textarea class="form-control-dark @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3">{{ old('description', $category->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="150">
            <div class="card-body-custom d-flex justify-content-between align-items-center">
                <a href="{{ route('admin.qna.categories.index') }}" class="btn-glass">
                    <i class="bi bi-x-lg me-1"></i>İptal
                </a>
                <button type="submit" class="btn-teal">
                    <i class="bi bi-check-lg me-1"></i>{{ $isEdit ? 'Güncelle' : 'Oluştur' }}
                </button>
            </div>
        </div>
    </form>

@endsection
