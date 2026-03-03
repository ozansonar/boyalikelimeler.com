@extends('layouts.front')

@section('title', $pageTitle . ' — Boyalı Kelimeler')
@section('meta_description', 'Boyalı Kelimeler\'de yazınızı paylaşın. Şiir, hikaye, deneme ve daha fazlası.')
@section('canonical', $post ? route('myposts.edit', $post) : route('myposts.create'))

@push('styles')
    <!-- Tom Select CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.min.css" rel="stylesheet">
@endpush

@section('content')

    <section class="wpost-section" aria-label="{{ $pageTitle }}">
        <div class="container">

            {{-- Page Header --}}
            <div class="wpost-header">
                <div class="wpost-header__left">
                    <a href="{{ route('myposts.index') }}" class="wpost-header__back">
                        <i class="fa-solid fa-arrow-left me-2"></i>Yazılarıma Dön
                    </a>
                    <h1 class="wpost-header__title">
                        <i class="fa-solid fa-feather-pointed me-2"></i>{{ $pageTitle }}
                    </h1>
                </div>
                <div class="wpost-header__actions">
                    <a href="{{ route('myposts.index') }}" class="wpost-btn wpost-btn--ghost">
                        <i class="fa-solid fa-xmark me-1"></i>İptal
                    </a>
                    <button type="submit" form="writePostForm" class="wpost-btn wpost-btn--primary">
                        <i class="fa-solid fa-paper-plane me-1"></i>{{ $post ? 'Yazıyı Güncelle' : 'Yazıyı Gönder' }}
                    </button>
                </div>
            </div>

            {{-- Validation Errors --}}
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-2"></i>
                    <strong>Lütfen aşağıdaki hataları düzeltin:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
                </div>
            @endif

            {{-- Form --}}
            <form id="writePostForm"
                  action="{{ $post ? route('myposts.update', $post) : route('myposts.store') }}"
                  method="POST"
                  enctype="multipart/form-data"
                  novalidate>
                @csrf
                @if($post)
                    @method('PUT')
                @endif

                <div class="row g-4">

                    {{-- Left: Main Content --}}
                    <div class="col-lg-8">

                        {{-- Title --}}
                        <div class="wpost-card">
                            <h3 class="wpost-card__title">
                                <i class="fa-solid fa-heading me-2"></i>Yazı Başlığı
                            </h3>
                            <div class="wpost-form__group">
                                <input type="text"
                                       class="wpost-form__input"
                                       id="postTitle"
                                       name="title"
                                       value="{{ old('title', $post?->title) }}"
                                       placeholder="Yazınıza etkileyici bir başlık verin..."
                                       required
                                       maxlength="200">
                                <div class="wpost-form__char-count">
                                    <span id="titleCharCount">{{ mb_strlen(old('title', $post?->title ?? '')) }}</span> / 200
                                </div>
                            </div>
                        </div>

                        {{-- Content Editor --}}
                        <div class="wpost-card">
                            <h3 class="wpost-card__title">
                                <i class="fa-solid fa-pen-nib me-2"></i>Yazı Detayı
                            </h3>
                            <div class="wpost-editor-wrap">
                                <textarea id="postEditor" name="body">{{ old('body', $post?->body) }}</textarea>
                            </div>
                            <p class="wpost-form__hint">
                                <i class="fa-solid fa-circle-info me-1"></i>
                                Yazınızı zengin metin editörü ile biçimlendirebilirsiniz. Başlık, kalın, italik, liste, link ve görsel ekleyebilirsiniz.
                            </p>
                        </div>

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
                                    <i class="fa-solid fa-tag me-1"></i>Yazı Kategorisi
                                </label>
                                <select id="postCategory"
                                        name="category_id"
                                        placeholder="Kategori arayın veya seçin..."
                                        required>
                                    <option value="">Kategori seçiniz</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                            @selected(old('category_id', $post?->category_id) == $category->id)>
                                            {{ $category->name }}
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
                                <div class="wpost-cover-upload__placeholder" id="coverPlaceholder"
                                     @if($post?->cover_image) style="display:none" @endif>
                                    <i class="fa-solid fa-cloud-arrow-up"></i>
                                    <span>Görsel yüklemek için tıklayın<br>veya sürükleyip bırakın</span>
                                    <small>JPG, PNG veya WebP — Maks. 5MB</small>
                                </div>
                                <div class="wpost-cover-upload__preview @if($post?->cover_image) wpost-cover-upload__preview--active @endif" id="coverPreview">
                                    <img src="{{ $post?->cover_image ? upload_url($post->cover_image) : '' }}"
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
                            @if($post?->cover_image)
                                <input type="hidden" name="remove_cover" id="removeCoverFlag" value="0">
                            @endif
                        </div>

                        {{-- Publish Settings --}}
                        <div class="wpost-card">
                            <h3 class="wpost-card__title">
                                <i class="fa-solid fa-gear me-2"></i>Yayın Ayarları
                            </h3>

                            <div class="wpost-form__group">
                                <label class="wpost-form__label" for="postExcerpt">
                                    <i class="fa-solid fa-align-left me-1"></i>Kısa Açıklama
                                </label>
                                <textarea class="wpost-form__input wpost-form__textarea"
                                          id="postExcerpt"
                                          name="excerpt"
                                          placeholder="Yazınızın kısa özetini girin..."
                                          rows="3"
                                          maxlength="300">{{ old('excerpt', $post?->excerpt) }}</textarea>
                                <div class="wpost-form__char-count">
                                    <span id="excerptCharCount">{{ mb_strlen(old('excerpt', $post?->excerpt ?? '')) }}</span> / 300
                                </div>
                            </div>

                            <div class="wpost-form__group">
                                <label class="wpost-form__label" for="postTags">
                                    <i class="fa-solid fa-hashtag me-1"></i>Etiketler
                                </label>
                                <input type="text"
                                       class="wpost-form__input"
                                       id="postTags"
                                       name="tags"
                                       value="{{ old('tags') }}"
                                       placeholder="Etiket yazıp Enter'a basın...">
                                <p class="wpost-form__hint">
                                    <i class="fa-solid fa-circle-info me-1"></i>
                                    Virgül ile ayırarak birden fazla etiket ekleyebilirsiniz.
                                </p>
                            </div>
                        </div>

                        {{-- Submit (Mobile sticky) --}}
                        <div class="wpost-submit-bar">
                            <button type="submit" form="writePostForm" class="wpost-btn wpost-btn--primary wpost-btn--block">
                                <i class="fa-solid fa-paper-plane me-1"></i>{{ $post ? 'Yazıyı Güncelle' : 'Yazıyı Gönder' }}
                            </button>
                            <p class="wpost-submit-bar__note">
                                <i class="fa-solid fa-shield-halved me-1"></i>
                                Yazınız editör onayından sonra yayınlanacaktır.
                            </p>
                        </div>

                    </div>

                </div>
            </form>

        </div>
    </section>

@endsection

@push('scripts')
    <!-- Tom Select -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>
    <!-- TinyMCE 7 -->
    <script src="https://cdn.jsdelivr.net/npm/tinymce@7.6.1/tinymce.min.js"></script>
    <!-- Write Post JS -->
    <script src="{{ asset('js/write-post.js') }}"></script>
@endpush
