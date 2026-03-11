@php
    $isEdit = isset($poll);
    $currentPoll = $poll ?? null;
    $existingOptions = $isEdit ? $currentPoll->options->pluck('option_text')->toArray() : [];
@endphp

<!-- Anket Bilgileri -->
<div class="card-dark mb-4" data-aos="fade-up">
    <div class="card-header-custom">
        <div class="form-section-header mb-0">
            <div class="form-section-icon bg-icon-teal"><i class="bi bi-bar-chart"></i></div>
            <div>
                <h6 class="mb-0">Anket Bilgileri</h6>
                <small class="text-muted">Anket sorusu ve şıklarını belirleyin</small>
            </div>
        </div>
    </div>
    <div class="card-body-custom">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">Anket Sorusu <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('question') is-invalid @enderror"
                       name="question" value="{{ old('question', $currentPoll->question ?? '') }}"
                       placeholder="Örn: En çok hangi edebiyat türünü seviyorsunuz?" required maxlength="300">
                @error('question')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
                <label class="form-label">Şıklar <span class="text-danger">*</span> <small class="text-muted">(En az 2, en fazla 5)</small></label>
                <div id="optionsContainer">
                    @php
                        $optionValues = old('options', $existingOptions);
                        if (empty($optionValues)) {
                            $optionValues = ['', ''];
                        }
                    @endphp
                    @foreach($optionValues as $index => $optionText)
                        <div class="input-group mb-2 poll-option-row">
                            <span class="input-group-text">{{ $index + 1 }}</span>
                            <input type="text" class="form-control @error('options.' . $index) is-invalid @enderror"
                                   name="options[]" value="{{ $optionText }}"
                                   placeholder="Şık metni" required maxlength="200">
                            @if($index >= 2)
                                <button type="button" class="btn btn-outline-danger btn-remove-option" title="Kaldır">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            @endif
                            @error('options.' . $index)
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn-glass btn-sm mt-1" id="addOptionBtn">
                    <i class="bi bi-plus-lg me-1"></i>Şık Ekle
                </button>
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
                <small class="text-muted">Anketin gösterim süresi ve durum ayarları</small>
            </div>
        </div>
    </div>
    <div class="card-body-custom">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Başlangıç Tarihi</label>
                <input type="date" class="form-control @error('starts_at') is-invalid @enderror"
                       name="starts_at" value="{{ old('starts_at', $currentPoll?->starts_at?->format('Y-m-d') ?? '') }}">
                @error('starts_at')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Boş bırakılırsa hemen başlar</div>
            </div>
            <div class="col-md-4">
                <label class="form-label">Bitiş Tarihi</label>
                <input type="date" class="form-control @error('ends_at') is-invalid @enderror"
                       name="ends_at" value="{{ old('ends_at', $currentPoll?->ends_at?->format('Y-m-d') ?? '') }}">
                @error('ends_at')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Boş bırakılırsa süresiz gösterilir</div>
            </div>
            <div class="col-md-4">
                <label class="form-label">Durum</label>
                <select class="form-select" name="is_active">
                    <option value="1" {{ old('is_active', $currentPoll->is_active ?? false) ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ !old('is_active', $currentPoll->is_active ?? false) ? 'selected' : '' }}>Pasif</option>
                </select>
                <div class="form-text">Aktif yapıldığında diğer anketler otomatik pasife alınır</div>
            </div>
        </div>
    </div>
</div>

<!-- Form Actions -->
<div class="card-dark mb-4">
    <div class="card-body-custom">
        <div class="d-flex justify-content-between">
            <a href="{{ route('admin.polls.index') }}" class="btn-glass">
                <i class="bi bi-x-lg me-1"></i>Vazgeç
            </a>
            <button type="submit" class="btn-teal">
                <i class="bi bi-check2 me-1"></i>{{ $isEdit ? 'Değişiklikleri Kaydet' : 'Anket Oluştur' }}
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    var container = document.getElementById('optionsContainer');
    var addBtn = document.getElementById('addOptionBtn');
    var maxOptions = 5;

    function updateNumbers() {
        var rows = container.querySelectorAll('.poll-option-row');
        rows.forEach(function(row, i) {
            row.querySelector('.input-group-text').textContent = i + 1;
        });
        addBtn.disabled = rows.length >= maxOptions;
    }

    function removeOption(e) {
        var btn = e.target.closest('.btn-remove-option');
        if (!btn) return;
        var rows = container.querySelectorAll('.poll-option-row');
        if (rows.length <= 2) return;
        btn.closest('.poll-option-row').remove();
        updateNumbers();
    }

    container.addEventListener('click', removeOption);

    addBtn.addEventListener('click', function() {
        var rows = container.querySelectorAll('.poll-option-row');
        if (rows.length >= maxOptions) return;

        var div = document.createElement('div');
        div.className = 'input-group mb-2 poll-option-row';
        div.innerHTML = '<span class="input-group-text">' + (rows.length + 1) + '</span>' +
            '<input type="text" class="form-control" name="options[]" placeholder="Şık metni" required maxlength="200">' +
            '<button type="button" class="btn btn-outline-danger btn-remove-option" title="Kaldır"><i class="bi bi-x-lg"></i></button>';
        container.appendChild(div);
        updateNumbers();
        div.querySelector('input').focus();
    });

    updateNumbers();
})();
</script>
@endpush
