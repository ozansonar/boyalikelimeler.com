@php
    $isEdit = isset($category);
@endphp

<div class="card-dark mb-4" data-aos="fade-up">
    <div class="card-header-custom">
        <div class="form-section-header mb-0">
            <div class="form-section-icon bg-icon-teal"><i class="bi bi-book"></i></div>
            <div>
                <h6 class="mb-0">Edebiyat Kategorisi Bilgileri</h6>
                <small class="text-muted">Kategori adı, açıklama ve durumunu belirleyin</small>
            </div>
        </div>
    </div>
    <div class="card-body-custom">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Kategori Adı <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror"
                       name="name" value="{{ old('name', $category->name ?? '') }}"
                       placeholder="Örn: Şiir, Öykü, Deneme..." required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label">Sıralama</label>
                <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                       name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}"
                       min="0" max="999" placeholder="0">
                <div class="form-text">Düşük değer = Daha üstte</div>
            </div>
            <div class="col-md-3">
                <label class="form-label">Durum</label>
                <select class="form-select" name="is_active">
                    <option value="1" {{ old('is_active', $category->is_active ?? true) ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ !old('is_active', $category->is_active ?? true) ? 'selected' : '' }}>Pasif</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Açıklama</label>
                <textarea class="form-control @error('description') is-invalid @enderror"
                          name="description" rows="3"
                          placeholder="Kategori hakkında kısa açıklama...">{{ old('description', $category->description ?? '') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Maksimum 500 karakter</div>
            </div>
        </div>
    </div>
</div>

<!-- Form Actions -->
<div class="card-dark mb-4">
    <div class="card-body-custom">
        <div class="d-flex justify-content-between">
            <a href="{{ route('admin.literary-categories.index') }}" class="btn-glass">
                <i class="bi bi-x-lg me-1"></i>Vazgeç
            </a>
            <button type="submit" class="btn-teal">
                <i class="bi bi-check2 me-1"></i>{{ $isEdit ? 'Değişiklikleri Kaydet' : 'Kategori Oluştur' }}
            </button>
        </div>
    </div>
</div>
