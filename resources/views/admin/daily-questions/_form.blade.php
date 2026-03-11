@php
    $isEdit = isset($question);
    $currentQuestion = $question ?? null;
@endphp

<!-- Soru Bilgileri -->
<div class="card-dark mb-4" data-aos="fade-up">
    <div class="card-header-custom">
        <div class="form-section-header mb-0">
            <div class="form-section-icon bg-icon-teal"><i class="bi bi-question-circle"></i></div>
            <div>
                <h6 class="mb-0">Soru Bilgileri</h6>
                <small class="text-muted">Günün sorusunu ve yayın ayarlarını belirleyin</small>
            </div>
        </div>
    </div>
    <div class="card-body-custom">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">Soru Metni <span class="text-danger">*</span></label>
                <textarea class="form-control @error('question_text') is-invalid @enderror"
                          name="question_text" rows="3" required maxlength="500"
                          placeholder="Örn: Bugün kendinizi bir renk olarak tanımlasanız hangi renk olurdunuz?">{{ old('question_text', $currentQuestion->question_text ?? '') }}</textarea>
                @error('question_text')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

<!-- Zamanlama ve Durum -->
<div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="100">
    <div class="card-header-custom">
        <div class="form-section-header mb-0">
            <div class="form-section-icon bg-icon-purple"><i class="bi bi-calendar-event"></i></div>
            <div>
                <h6 class="mb-0">Yayın Ayarları</h6>
                <small class="text-muted">Sorunun yayın tarihi ve durumu</small>
            </div>
        </div>
    </div>
    <div class="card-body-custom">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Yayın Tarihi <span class="text-danger">*</span></label>
                <input type="date" class="form-control @error('published_at') is-invalid @enderror"
                       name="published_at" required
                       value="{{ old('published_at', $currentQuestion?->published_at?->format('Y-m-d') ?? now()->format('Y-m-d')) }}">
                @error('published_at')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Soru bu tarihte otomatik aktif olur</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Durum <span class="text-danger">*</span></label>
                <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                    <option value="draft" {{ old('status', $currentQuestion->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Taslak</option>
                    <option value="published" {{ old('status', $currentQuestion->status ?? '') === 'published' ? 'selected' : '' }}>Yayında</option>
                    <option value="archived" {{ old('status', $currentQuestion->status ?? '') === 'archived' ? 'selected' : '' }}>Arşiv</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Sadece "Yayında" durumundaki sorular anasayfada görünür</div>
            </div>
        </div>
    </div>
</div>

<!-- Form Actions -->
<div class="card-dark mb-4">
    <div class="card-body-custom">
        <div class="d-flex justify-content-between">
            <a href="{{ route('admin.daily-questions.index') }}" class="btn-glass">
                <i class="bi bi-x-lg me-1"></i>Vazgeç
            </a>
            <button type="submit" class="btn-teal">
                <i class="bi bi-check2 me-1"></i>{{ $isEdit ? 'Değişiklikleri Kaydet' : 'Soru Oluştur' }}
            </button>
        </div>
    </div>
</div>
