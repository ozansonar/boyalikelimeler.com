@extends('layouts.front')

@section('title', $pageTitle . ' — Boyalı Kelimeler')
@section('meta_description', 'Boyalı Kelimeler\'de eserinizi paylaşın. Şiir, hikaye, deneme ve daha fazlası.')
@section('canonical', $work ? route('myposts.edit', $work) : route('myposts.create'))
@section('robots', 'noindex, nofollow')

@push('styles')
    <!-- Tom Select CSS -->
    <link href="{{ asset('vendor/tom-select/2.4.3/css/tom-select.min.css') }}" rel="stylesheet">
    <style>
        .tox-tbtn[data-mce-name="pasteContent"] .tox-icon {
            color: #D4AF37 !important;
        }
        .tox-tbtn[data-mce-name="pasteContent"]:hover {
            background-color: rgba(212, 175, 55, 0.15) !important;
        }
    </style>
@endpush

@section('content')

    <section class="wpost-section" aria-label="{{ $pageTitle }}">
        <div class="container">

            {{-- Page Header --}}
            <div class="wpost-header">
                <div class="wpost-header__left">
                    <a href="{{ route('myposts.index') }}" class="wpost-header__back">
                        <i class="fa-solid fa-arrow-left me-2"></i>Eserlerime Dön
                    </a>
                    <h1 class="wpost-header__title">
                        <i class="fa-solid fa-feather-pointed me-2"></i>{{ $pageTitle }}
                    </h1>
                </div>
                <div class="wpost-header__actions">
                    @if($work?->status === \App\Enums\LiteraryWorkStatus::Approved)
                        <button type="button" class="wpost-btn wpost-btn--warning" onclick="openUnpublishModal({{ $work->id }}, '{{ addslashes($work->title) }}')">
                            <i class="fa-solid fa-eye-slash me-1"></i>Yayından Kaldır
                        </button>
                    @endif
                    <a href="{{ route('myposts.index') }}" class="wpost-btn wpost-btn--ghost">
                        <i class="fa-solid fa-xmark me-1"></i>İptal
                    </a>
                    <button type="submit" form="writePostForm" class="wpost-btn wpost-btn--primary">
                        <i class="fa-solid fa-paper-plane me-1"></i>{{ $work ? 'Eseri Güncelle' : 'Eseri Gönder' }}
                    </button>
                </div>
            </div>

            {{-- Revision Note --}}
            @if($work?->status === \App\Enums\LiteraryWorkStatus::RevisionRequested)
                @php $latestRevision = $work->revisions->first(); @endphp
                @if($latestRevision)
                    <div class="wpost-revision" role="alert">
                        <div class="wpost-revision__header">
                            <div class="wpost-revision__icon">
                                <i class="fa-solid fa-pen-ruler"></i>
                            </div>
                            <h6 class="wpost-revision__title">Revize Talebi</h6>
                        </div>
                        <p class="wpost-revision__editor">
                            <i class="fa-solid fa-user-pen me-1"></i>Editör: {{ $latestRevision->admin?->name ?? 'Admin' }}
                        </p>
                        <p class="wpost-revision__reason">{{ $latestRevision->reason }}</p>
                    </div>
                @endif
            @endif

            {{-- Form --}}
            <form id="writePostForm"
                  action="{{ $work ? route('myposts.update', $work) : route('myposts.store') }}"
                  method="POST"
                  enctype="multipart/form-data"
                  novalidate>
                @csrf
                @if($work)
                    @method('PUT')
                @endif

                <div class="row g-4">

                    {{-- Left: Main Content --}}
                    <div class="col-lg-8">

                        {{-- Title --}}
                        <div class="wpost-card">
                            <h3 class="wpost-card__title">
                                <i class="fa-solid fa-heading me-2"></i>Eser Başlığı
                            </h3>
                            <div class="wpost-form__group">
                                <input type="text"
                                       class="wpost-form__input"
                                       id="postTitle"
                                       name="title"
                                       value="{{ old('title', $work?->title) }}"
                                       placeholder="Eserinize etkileyici bir başlık verin..."
                                       required
                                       maxlength="200">
                                <div class="wpost-form__char-count">
                                    <span id="titleCharCount">{{ mb_strlen(old('title', $work?->title ?? '')) }}</span> / 200
                                </div>
                            </div>
                        </div>

                        {{-- Content Editor --}}
                        <div class="wpost-card">
                            <h3 class="wpost-card__title">
                                <i class="fa-solid fa-pen-nib me-2"></i>Eser Detayı
                            </h3>
                            <div class="wpost-editor-wrap">
                                <textarea id="postEditor" name="body">{{ old('body', $work?->body) }}</textarea>
                            </div>
                            <p class="wpost-form__hint">
                                <i class="fa-solid fa-circle-info me-1"></i>
                                Eserinizi zengin metin editörü ile biçimlendirebilirsiniz. Başlık, kalın, italik, liste, link ve görsel ekleyebilirsiniz.
                            </p>
                        </div>

                        {{-- Author Note (only for revision) --}}
                        @if($work?->status === \App\Enums\LiteraryWorkStatus::RevisionRequested)
                            <div class="wpost-card">
                                <h3 class="wpost-card__title">
                                    <i class="fa-solid fa-comment me-2"></i>Editöre Not
                                </h3>
                                <div class="wpost-form__group">
                                    <textarea class="wpost-form__input wpost-form__textarea"
                                              name="author_note"
                                              placeholder="Yaptığınız değişiklikleri kısaca açıklayın (isteğe bağlı)..."
                                              rows="3"
                                              maxlength="1000">{{ old('author_note') }}</textarea>
                                    <div class="wpost-form__char-count">
                                        <span id="noteCharCount">{{ mb_strlen(old('author_note', '')) }}</span> / 1000
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>

                    {{-- Right: Sidebar --}}
                    <div class="col-lg-4">

                        {{-- Category --}}
                        <div class="wpost-card">
                            <h3 class="wpost-card__title">
                                <i class="fa-solid fa-folder-open me-2"></i>Kategori
                            </h3>
                            <div class="wpost-form__group">
                                <label class="wpost-form__label" for="postCategory">
                                    <i class="fa-solid fa-tag me-1"></i>Eser Kategorisi
                                </label>
                                <select id="postCategory"
                                        name="literary_category_id"
                                        placeholder="Kategori arayın veya seçin..."
                                        required>
                                    <option value="">Kategori seçiniz</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                            @selected(old('literary_category_id', $work?->literary_category_id) == $category->id)>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Work Type --}}
                        <div class="wpost-card">
                            <h3 class="wpost-card__title">
                                <i class="fa-solid fa-layer-group me-2"></i>Eser Türü
                            </h3>
                            <div class="wpost-form__group">
                                <label class="wpost-form__label" for="workType">
                                    <i class="fa-solid fa-shapes me-1"></i>Eser Türü
                                </label>
                                <select id="workType"
                                        name="work_type"
                                        placeholder="Eser türü seçin..."
                                        required>
                                    <option value="">Eser türü seçiniz</option>
                                    @foreach(\App\Enums\LiteraryWorkType::cases() as $type)
                                        <option value="{{ $type->value }}"
                                            @selected(old('work_type', $work?->work_type?->value) === $type->value)>
                                            {{ $type->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Cover Image --}}
                        <div class="wpost-card">
                            <h3 class="wpost-card__title">
                                <i class="fa-solid fa-image me-2"></i>Kapak Görseli
                            </h3>
                            <div class="wpost-cover-upload" id="coverDropZone">
                                <div class="wpost-cover-upload__placeholder @if($work?->cover_image) d-none @endif" id="coverPlaceholder">
                                    <i class="fa-solid fa-cloud-arrow-up"></i>
                                    <span>Görsel yüklemek için tıklayın<br>veya sürükleyip bırakın</span>
                                    <small>JPG, PNG veya WebP — Maks. 2MB</small>
                                </div>
                                <div class="wpost-cover-upload__preview @if($work?->cover_image) wpost-cover-upload__preview--active @endif" id="coverPreview">
                                    <img src="{{ $work?->cover_image ? upload_url($work->cover_image) : '' }}"
                                         alt="Kapak görseli önizleme"
                                         id="coverPreviewImg"
                                         loading="lazy">
                                    <button type="button" class="wpost-cover-upload__remove" id="coverRemoveBtn" aria-label="Görseli kaldır">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                                <input type="file"
                                       class="wpost-file-input"
                                       id="coverInput"
                                       name="cover_image"
                                       accept="image/jpeg,image/png,image/webp"
                                       aria-label="Kapak görseli seç">
                            </div>
                            @if($work?->cover_image)
                                <input type="hidden" name="remove_cover" id="removeCoverFlag" value="0">
                            @endif
                        </div>

                        {{-- Publish Settings --}}
                        <div class="wpost-card">
                            <h3 class="wpost-card__title">
                                <i class="fa-solid fa-gear me-2"></i>Ayarlar
                            </h3>

                            <div class="wpost-form__group">
                                <label class="wpost-form__label" for="postExcerpt">
                                    <i class="fa-solid fa-align-left me-1"></i>Kısa Açıklama
                                </label>
                                <textarea class="wpost-form__input wpost-form__textarea"
                                          id="postExcerpt"
                                          name="excerpt"
                                          placeholder="Eserinizin kısa özetini girin..."
                                          rows="3"
                                          maxlength="300">{{ old('excerpt', $work?->excerpt) }}</textarea>
                                <div class="wpost-form__char-count">
                                    <span id="excerptCharCount">{{ mb_strlen(old('excerpt', $work?->excerpt ?? '')) }}</span> / 300
                                </div>
                            </div>
                        </div>

                        {{-- Submit (Mobile sticky) --}}
                        <div class="wpost-submit-bar">
                            <button type="submit" form="writePostForm" class="wpost-btn wpost-btn--primary wpost-btn--block">
                                <i class="fa-solid fa-paper-plane me-1"></i>{{ $work ? 'Eseri Güncelle' : 'Eseri Gönder' }}
                            </button>
                            <p class="wpost-submit-bar__note">
                                <i class="fa-solid fa-shield-halved me-1"></i>
                                Eseriniz editör onayından sonra yayınlanacaktır.
                            </p>
                        </div>

                    </div>

                </div>
            </form>

        </div>
    </section>

    {{-- Unpublish Confirmation Modal (shared partial) --}}
    @if($work?->status === \App\Enums\LiteraryWorkStatus::Approved)
        @include('front.myposts._unpublish-modal')
    @endif

    {{-- Editor Image Gallery Modal --}}
    <x-editor-image-gallery />

@endsection

@push('scripts')
    <!-- Tom Select -->
    <script src="{{ asset('vendor/tom-select/2.4.3/js/tom-select.complete.min.js') }}"></script>
    <!-- TinyMCE 7 -->
    <script src="{{ asset('vendor/tinymce/7.6.1/tinymce.min.js') }}"></script>
    <!-- Editor Image Gallery JS (must load before write-post.js) -->
    <script src="{{ asset('js/editor-image-gallery.js') }}?v={{ filemtime(public_path('js/editor-image-gallery.js')) }}"></script>
    <!-- Write Post JS -->
    <script src="{{ asset('js/write-post.js') }}?v={{ filemtime(public_path('js/write-post.js')) }}"></script>
    @if($work?->status === \App\Enums\LiteraryWorkStatus::Approved)
        <script src="{{ asset('js/myposts.js') }}?v={{ filemtime(public_path('js/myposts.js')) }}"></script>
    @endif
@endpush
