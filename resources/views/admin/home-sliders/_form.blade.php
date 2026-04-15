@php
    $isEdit = isset($slider);
@endphp

<div class="card-dark mb-4" data-aos="fade-up">
    <div class="card-header-custom">
        <div class="form-section-header mb-0">
            <div class="form-section-icon bg-icon-teal"><i class="bi bi-sliders"></i></div>
            <div>
                <h6 class="mb-0">Slide Bilgileri</h6>
                <small class="text-muted">Slider'da görünecek rozet, başlık ve açıklama bilgilerini girin</small>
            </div>
        </div>
    </div>
    <div class="card-body-custom">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Rozet İkon <small class="text-muted">(Font Awesome class)</small></label>
                <input type="text" class="form-control @error('badge_icon') is-invalid @enderror"
                       name="badge_icon" value="{{ old('badge_icon', $slider->badge_icon ?? 'fa-solid fa-feather-pointed') }}"
                       placeholder="fa-solid fa-feather-pointed">
                @error('badge_icon')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Örn: fa-solid fa-feather-pointed, fa-solid fa-palette, fa-solid fa-users</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Rozet Metni <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('badge_text') is-invalid @enderror"
                       name="badge_text" value="{{ old('badge_text', $slider->badge_text ?? '') }}"
                       placeholder="Edebiyat" required maxlength="100">
                @error('badge_text')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12">
                <label class="form-label">Başlık <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('title') is-invalid @enderror"
                       name="title" value="{{ old('title', $slider->title ?? '') }}"
                       placeholder="Kelimelerin Gücü" required maxlength="255">
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12">
                <label class="form-label">Açıklama <span class="text-danger">*</span></label>
                <textarea class="form-control @error('description') is-invalid @enderror"
                          name="description" rows="3" required maxlength="500"
                          placeholder="Bir kelime dünyayı değiştirebilir...">{{ old('description', $slider->description ?? '') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Maksimum 500 karakter</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Buton Metni <small class="text-muted">(opsiyonel)</small></label>
                <input type="text" class="form-control @error('button_text') is-invalid @enderror"
                       name="button_text" value="{{ old('button_text', $slider->button_text ?? '') }}"
                       placeholder="Keşfet" maxlength="100">
                @error('button_text')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Boş bırakılırsa buton gösterilmez</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Buton Linki <small class="text-muted">(opsiyonel)</small></label>
                <input type="url" class="form-control @error('button_link') is-invalid @enderror"
                       name="button_link" value="{{ old('button_link', $slider->button_link ?? '') }}"
                       placeholder="https://example.com" maxlength="500">
                @error('button_link')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Tam URL girin (https://...)</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Link Hedefi</label>
                <select class="form-select @error('button_target') is-invalid @enderror" name="button_target">
                    <option value="_self" {{ old('button_target', $slider->button_target ?? '_self') === '_self' ? 'selected' : '' }}>Aynı sekmede aç (_self)</option>
                    <option value="_blank" {{ old('button_target', $slider->button_target ?? '_self') === '_blank' ? 'selected' : '' }}>Yeni sekmede aç (_blank)</option>
                </select>
                @error('button_target')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Sıralama</label>
                <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                       name="sort_order" value="{{ old('sort_order', $slider->sort_order ?? 0) }}"
                       min="0" max="999" placeholder="0">
                <div class="form-text">Düşük değer = Daha önce gösterilir</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Durum</label>
                <select class="form-select" name="is_active">
                    <option value="1" {{ old('is_active', $slider->is_active ?? true) ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ !old('is_active', $slider->is_active ?? true) ? 'selected' : '' }}>Pasif</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Preview -->
<div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="100">
    <div class="card-header-custom">
        <div class="form-section-header mb-0">
            <div class="form-section-icon bg-icon-purple"><i class="bi bi-eye"></i></div>
            <div>
                <h6 class="mb-0">Önizleme</h6>
                <small class="text-muted">Slider'da nasıl görüneceğinin yaklaşık önizlemesi</small>
            </div>
        </div>
    </div>
    <div class="card-body-custom">
        <div class="p-4 rounded-3 text-center" id="sliderPreview">
            <span class="badge bg-dark text-warning mb-2" id="previewBadge">
                <i id="previewIcon" class="{{ old('badge_icon', $slider->badge_icon ?? 'fa-solid fa-feather-pointed') }} me-1"></i>
                <span id="previewBadgeText">{{ old('badge_text', $slider->badge_text ?? 'Rozet') }}</span>
            </span>
            <h3 class="text-white mb-2" id="previewTitle">{{ old('title', $slider->title ?? 'Başlık') }}</h3>
            <p class="text-white-50" id="previewDesc">{{ old('description', $slider->description ?? 'Açıklama metni buraya gelecek...') }}</p>
            <a href="#" class="btn btn-warning btn-sm mt-2 d-none" id="previewButton" onclick="return false;">
                <span id="previewButtonText">{{ old('button_text', $slider->button_text ?? 'Keşfet') }}</span>
            </a>
        </div>
    </div>
</div>

<!-- Form Actions -->
<div class="card-dark mb-4">
    <div class="card-body-custom">
        <div class="d-flex justify-content-between">
            <a href="{{ route('admin.home-sliders.index') }}" class="btn-glass">
                <i class="bi bi-x-lg me-1"></i>Vazgeç
            </a>
            <button type="submit" class="btn-teal">
                <i class="bi bi-check2 me-1"></i>{{ $isEdit ? 'Değişiklikleri Kaydet' : 'Slide Oluştur' }}
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var badgeIconInput = document.querySelector('input[name="badge_icon"]');
    var badgeTextInput = document.querySelector('input[name="badge_text"]');
    var titleInput = document.querySelector('input[name="title"]');
    var descInput = document.querySelector('textarea[name="description"]');
    var buttonTextInput = document.querySelector('input[name="button_text"]');
    var buttonLinkInput = document.querySelector('input[name="button_link"]');

    function updatePreview() {
        var icon = document.getElementById('previewIcon');
        icon.className = (badgeIconInput.value || 'fa-solid fa-feather-pointed') + ' me-1';
        document.getElementById('previewBadgeText').textContent = badgeTextInput.value || 'Rozet';
        document.getElementById('previewTitle').textContent = titleInput.value || 'Başlık';
        document.getElementById('previewDesc').textContent = descInput.value || 'Açıklama metni buraya gelecek...';

        var previewButton = document.getElementById('previewButton');
        var previewButtonText = document.getElementById('previewButtonText');
        var btnText = (buttonTextInput.value || '').trim();
        var btnLink = (buttonLinkInput.value || '').trim();

        if (btnText && btnLink) {
            previewButton.classList.remove('d-none');
            previewButtonText.textContent = btnText;
        } else {
            previewButton.classList.add('d-none');
        }
    }

    badgeIconInput.addEventListener('input', updatePreview);
    badgeTextInput.addEventListener('input', updatePreview);
    titleInput.addEventListener('input', updatePreview);
    descInput.addEventListener('input', updatePreview);
    buttonTextInput.addEventListener('input', updatePreview);
    buttonLinkInput.addEventListener('input', updatePreview);

    updatePreview();
});
</script>
@endpush
