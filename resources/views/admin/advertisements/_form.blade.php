@php
    $isEdit = isset($advertisement);
    $ad = $advertisement ?? null;
@endphp

<!-- Reklam Bilgileri -->
<div class="card-dark mb-4" data-aos="fade-up">
    <div class="card-header-custom">
        <div class="form-section-header mb-0">
            <div class="form-section-icon bg-icon-teal"><i class="bi bi-megaphone"></i></div>
            <div>
                <h6 class="mb-0">Reklam Bilgileri</h6>
                <small class="text-muted">Reklam başlığı, görseli ve hedef link bilgilerini girin</small>
            </div>
        </div>
    </div>
    <div class="card-body-custom">
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label">Başlık <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('title') is-invalid @enderror"
                       name="title" value="{{ old('title', $ad->title ?? '') }}"
                       placeholder="Reklam başlığı" required maxlength="200">
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Yönetim panelinde tanımlama amaçlı kullanılır</div>
            </div>
            <div class="col-md-4">
                <label class="form-label">Pozisyon <span class="text-danger">*</span></label>
                <select class="form-select @error('position') is-invalid @enderror" name="position" required>
                    @foreach($positions as $position)
                        <option value="{{ $position->value }}" {{ old('position', $ad->position?->value ?? '') === $position->value ? 'selected' : '' }}>
                            {{ $position->label() }}
                        </option>
                    @endforeach
                </select>
                @error('position')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12">
                <label class="form-label">Reklam Görseli @if(!$isEdit)<span class="text-danger">*</span>@endif</label>
                @if($isEdit && $ad->image)
                    <div class="mb-2">
                        <img src="{{ upload_url($ad->image, 'sm') }}" alt="{{ $ad->title }}" class="rounded img-fluid" loading="lazy">
                    </div>
                @endif
                <input type="file" class="form-control @error('image') is-invalid @enderror"
                       name="image" accept="image/jpeg,image/png,image/webp" {{ !$isEdit ? 'required' : '' }}>
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">JPG, PNG veya WebP — Maksimum 2 MB</div>
            </div>
            <div class="col-md-8">
                <label class="form-label">Hedef Link</label>
                <input type="url" class="form-control @error('link') is-invalid @enderror"
                       name="link" value="{{ old('link', $ad->link ?? '') }}"
                       placeholder="https://ornek.com/kampanya">
                @error('link')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Reklama tıklandığında yönlendirilecek adres</div>
            </div>
            <div class="col-md-4">
                <label class="form-label">Link Açılış</label>
                <select class="form-select" name="link_target">
                    <option value="_blank" {{ old('link_target', $ad->link_target ?? '_blank') === '_blank' ? 'selected' : '' }}>Yeni Sekmede</option>
                    <option value="_self" {{ old('link_target', $ad->link_target ?? '_blank') === '_self' ? 'selected' : '' }}>Aynı Sayfada</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Zamanlama ve Ayarlar -->
<div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="100">
    <div class="card-header-custom">
        <div class="form-section-header mb-0">
            <div class="form-section-icon bg-icon-purple"><i class="bi bi-calendar-event"></i></div>
            <div>
                <h6 class="mb-0">Zamanlama ve Ayarlar</h6>
                <small class="text-muted">Reklamın gösterim süresi ve durum ayarları</small>
            </div>
        </div>
    </div>
    <div class="card-body-custom">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Başlangıç Tarihi</label>
                <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                       name="start_date" value="{{ old('start_date', $ad->start_date?->format('Y-m-d') ?? '') }}">
                @error('start_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Boş bırakılırsa hemen başlar</div>
            </div>
            <div class="col-md-4">
                <label class="form-label">Bitiş Tarihi</label>
                <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                       name="end_date" value="{{ old('end_date', $ad->end_date?->format('Y-m-d') ?? '') }}">
                @error('end_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Boş bırakılırsa süresiz gösterilir</div>
            </div>
            <div class="col-md-2">
                <label class="form-label">Sıralama</label>
                <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                       name="sort_order" value="{{ old('sort_order', $ad->sort_order ?? 0) }}"
                       min="0" max="999" placeholder="0">
            </div>
            <div class="col-md-2">
                <label class="form-label">Durum</label>
                <select class="form-select" name="is_active">
                    <option value="1" {{ old('is_active', $ad->is_active ?? true) ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ !old('is_active', $ad->is_active ?? true) ? 'selected' : '' }}>Pasif</option>
                </select>
            </div>
        </div>
    </div>
</div>

@if($isEdit)
    <!-- İstatistikler (sadece düzenleme) -->
    <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="150">
        <div class="card-header-custom">
            <div class="form-section-header mb-0">
                <div class="form-section-icon bg-icon-blue"><i class="bi bi-bar-chart"></i></div>
                <div>
                    <h6 class="mb-0">İstatistikler</h6>
                    <small class="text-muted">Reklamın performans verileri</small>
                </div>
            </div>
        </div>
        <div class="card-body-custom">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="text-center p-3 rounded-3 bg-dark">
                        <div class="fs-3 fw-bold text-teal">{{ number_format($ad->view_count) }}</div>
                        <small class="text-muted">Görüntülenme</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-3 rounded-3 bg-dark">
                        <div class="fs-3 fw-bold text-teal">{{ number_format($ad->click_count) }}</div>
                        <small class="text-muted">Tıklama</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-3 rounded-3 bg-dark">
                        <div class="fs-3 fw-bold text-teal">
                            {{ $ad->view_count > 0 ? number_format(($ad->click_count / $ad->view_count) * 100, 2) : '0.00' }}%
                        </div>
                        <small class="text-muted">CTR (Tıklama Oranı)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Form Actions -->
<div class="card-dark mb-4">
    <div class="card-body-custom">
        <div class="d-flex justify-content-between">
            <a href="{{ route('admin.advertisements.index') }}" class="btn-glass">
                <i class="bi bi-x-lg me-1"></i>Vazgeç
            </a>
            <button type="submit" class="btn-teal">
                <i class="bi bi-check2 me-1"></i>{{ $isEdit ? 'Değişiklikleri Kaydet' : 'Reklam Oluştur' }}
            </button>
        </div>
    </div>
</div>
