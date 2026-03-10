@extends('layouts.admin')

@section('title', $template->description . ' — Mail Şablonu Düzenle')

@section('content')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3" data-aos="fade-down" data-aos-duration="400">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link"><i class="bi bi-house"></i> Ana Sayfa</a></li>
            <li><a href="{{ route('admin.mail-templates.index') }}" class="breadcrumb-link">Mail Şablonları</a></li>
            <li class="breadcrumb-item active text-teal">{{ $template->description }}</li>
        </ol>
    </nav>

    <x-admin.page-header title="{{ $template->description }}" subtitle="Şablon konu ve gövdesini düzenleyin — değişkenleri istediğiniz yere yerleştirin" />

    <form action="{{ route('admin.mail-templates.update', $template) }}" method="POST" id="templateForm">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Left Column: Form -->
            <div class="col-lg-8" data-aos="fade-up" data-aos-delay="50">
                <div class="cl-table-card p-4">
                    <!-- Subject -->
                    <div class="mb-4">
                        <label for="subject" class="form-label fw-semibold">Konu (Subject)</label>
                        <input type="text" name="subject" id="subject" class="form-control @error('subject') is-invalid @enderror"
                               value="{{ old('subject', $template->subject) }}" placeholder="{{ $template->default_subject }}">
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted mt-1 d-block">Varsayılan: {{ $template->default_subject }}</small>
                    </div>

                    <!-- Body -->
                    <div class="mb-4">
                        <label for="body" class="form-label fw-semibold">Gövde (Body)</label>
                        <textarea name="body" id="body" class="form-control @error('body') is-invalid @enderror"
                                  rows="20">{{ old('body', $template->body) }}</textarea>
                        @error('body')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Durum</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="is_active" id="is_active_1" value="1"
                                       {{ old('is_active', $template->is_active ? '1' : '0') === '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active_1">Aktif</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="is_active" id="is_active_0" value="0"
                                       {{ old('is_active', $template->is_active ? '1' : '0') === '0' ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active_0">Pasif</label>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="{{ route('admin.mail-templates.reset', $template) }}" class="btn btn-outline-warning btn-sm"
                           onclick="return confirm('Bu şablon varsayılan değerlere sıfırlanacak. Emin misiniz?')">
                            <i class="bi bi-arrow-counterclockwise"></i> Varsayılana Dön
                        </a>
                        <button type="submit" class="btn btn-teal">
                            <i class="bi bi-check-lg"></i> Kaydet
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Column: Variables -->
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="cl-table-card p-4 mb-4">
                    <h6 class="fw-semibold mb-3"><i class="bi bi-braces"></i> Kullanılabilir Değişkenler</h6>
                    <p class="text-muted small mb-3">Aşağıdaki değişkenleri konu veya gövde içinde kullanabilirsiniz. Tıklayarak editöre ekleyin.</p>

                    <div class="d-flex flex-wrap gap-2">
                        @foreach($template->variables as $variable)
                            <button type="button" class="btn btn-sm btn-outline-info mt-variable-btn"
                                    data-variable="{{ $variable['key'] }}" title="{{ $variable['label'] }}">
                                {{ $variable['key'] }}
                            </button>
                        @endforeach
                    </div>

                    <hr class="my-3">
                    <h6 class="fw-semibold mb-2 small">Değişken Açıklamaları</h6>
                    <ul class="list-unstyled small text-muted mb-0">
                        @foreach($template->variables as $variable)
                            <li class="mb-1">
                                <code>{{ $variable['key'] }}</code> — {{ $variable['label'] }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="cl-table-card p-4">
                    <h6 class="fw-semibold mb-3"><i class="bi bi-info-circle"></i> Bilgi</h6>
                    <ul class="list-unstyled small text-muted mb-0">
                        <li class="mb-2"><strong>Şablon Key:</strong> <code>{{ $template->key }}</code></li>
                        <li class="mb-2"><strong>Mailable:</strong> <code>{{ class_basename($template->mailable_class) }}</code></li>
                        <li class="mb-2"><strong>Son Güncelleme:</strong> {{ $template->updated_at?->format('d.m.Y H:i') ?? '-' }}</li>
                        <li class="mb-2">
                            <strong>Özelleştirilmiş:</strong>
                            @if($template->hasCustomSubject() || $template->hasCustomBody())
                                <span class="badge bg-warning text-dark">Evet</span>
                            @else
                                <span class="badge bg-dark">Hayır</span>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </form>

@endsection

@push('styles')
<style>
    .tox-tinymce { border-radius: 8px !important; }
    .tox .tox-edit-area::before { border: none !important; }
    .mt-variable-btn:hover { transform: scale(1.05); }
</style>
@endpush

@push('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    tinymce.init({
        selector: '#body',
        height: 500,
        menubar: true,
        skin: 'oxide-dark',
        content_css: 'dark',
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'charmap',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'table', 'preview', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | link | code | help',
        content_style: 'body { font-family: Inter, sans-serif; font-size: 14px; color: #f5f5f0; background: #1a1a1e; } a.button { display: inline-block; padding: 10px 24px; background: #D4AF37; color: #0f0f12; text-decoration: none; border-radius: 6px; font-weight: 600; } blockquote { border-left: 3px solid #D4AF37; padding-left: 12px; margin: 12px 0; color: #9B9EA3; }',
        setup: function(editor) {
            editor.on('change', function() {
                editor.save();
            });
        },
        language: 'tr',
        branding: false,
        promotion: false,
        license_key: 'gpl'
    });

    // Variable insert buttons
    document.querySelectorAll('.mt-variable-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var variable = this.getAttribute('data-variable');
            var editor = tinymce.get('body');
            if (editor) {
                editor.insertContent(variable);
                editor.focus();
            }
        });
    });
});
</script>
@endpush
