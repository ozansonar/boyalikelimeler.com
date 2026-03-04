@extends('layouts.admin')

@section('title', 'Yazarlar Sayfası — Boyalı Kelimeler Admin')

@section('content')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3" data-aos="fade-down" data-aos-duration="400">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link"><i class="bi bi-house"></i> Ana Sayfa</a></li>
            <li class="breadcrumb-item active text-teal">Yazarlar Sayfası</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3" data-aos="fade-down">
        <div>
            <h1 class="page-title">Yazarlar Sayfası</h1>
            <p class="page-subtitle">Yazarlar sayfasının içeriğini, öne çıkan yazarı ve SEO ayarlarını yönetin</p>
        </div>
    </div>

    <!-- Form Layout -->
    <div class="row g-4 align-items-start">

        <!-- Sol Navigasyon (desktop) -->
        <div class="col-lg-3 d-none d-lg-block">
            <div class="stg-nav-inner position-sticky stg-nav-sticky">
                <a href="#section-content" class="stg-nav-item active" onclick="scrollToSection('section-content', this)">
                    <i class="bi bi-body-text"></i>
                    <div><span>Sayfa İçeriği</span><small>Başlık, açıklama, editör</small></div>
                </a>
                <a href="#section-featured" class="stg-nav-item" onclick="scrollToSection('section-featured', this)">
                    <i class="bi bi-star"></i>
                    <div><span>Öne Çıkan Yazar</span><small>Sayfa üstünde gösterilecek yazar</small></div>
                </a>
                <a href="#section-seo" class="stg-nav-item" onclick="scrollToSection('section-seo', this)">
                    <i class="bi bi-search"></i>
                    <div><span>SEO Ayarları</span><small>Meta başlık, açıklama</small></div>
                </a>
            </div>
        </div>

        <!-- Mobile Section Jumper -->
        <div class="col-12 d-lg-none">
            <select class="form-select form-select-sm" onchange="scrollToSection(this.value, null); this.selectedIndex=0">
                <option value="" disabled selected>Bölüme git...</option>
                <option value="section-content">Sayfa İçeriği</option>
                <option value="section-featured">Öne Çıkan Yazar</option>
                <option value="section-seo">SEO Ayarları</option>
            </select>
        </div>

        <!-- Form İçeriği -->
        <div class="col-12 col-lg-9">
            <form action="{{ route('admin.authors-page.update') }}" method="POST" id="authorsPageForm">
                @csrf
                @method('PUT')

                <!-- ==================== SECTION 1: SAYFA İÇERİĞİ ==================== -->
                <div class="card-dark mb-4" id="section-content">
                    <div class="card-header-custom">
                        <div class="form-section-header mb-0">
                            <div class="form-section-icon bg-icon-teal"><i class="bi bi-body-text"></i></div>
                            <div>
                                <h6 class="mb-0">Sayfa İçeriği</h6>
                                <small class="text-muted">Yazarlar sayfasının üst kısmında görünecek içerik</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body-custom">
                        <div class="row g-3">
                            <!-- Başlık -->
                            <div class="col-12">
                                <label class="form-label" for="apTitle">
                                    Sayfa Başlığı
                                </label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                       id="apTitle" name="title"
                                       value="{{ old('title', $settings['title'] ?? '') }}"
                                       placeholder="Örn: Yazarlarımız"
                                       oninput="updateCharCounter(this, 200)">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="d-flex justify-content-between mt-1">
                                    <div class="form-text">Sayfanın ana başlığı (H1 olarak görünür)</div>
                                    <div class="form-text"><span id="apTitle-counter">{{ mb_strlen(old('title', $settings['title'] ?? '')) }}</span>/200</div>
                                </div>
                            </div>

                            <!-- Açıklama -->
                            <div class="col-12">
                                <label class="form-label" for="apDescription">Kısa Açıklama</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="apDescription" name="description" rows="3"
                                          placeholder="Yazarlar sayfası hakkında kısa bir açıklama yazın..."
                                          oninput="updateCharCounter(this, 500)">{{ old('description', $settings['description'] ?? '') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="d-flex justify-content-between mt-1">
                                    <div class="form-text">Başlığın altında gösterilecek açıklama</div>
                                    <div class="form-text"><span id="apDescription-counter">{{ mb_strlen(old('description', $settings['description'] ?? '')) }}</span>/500</div>
                                </div>
                            </div>

                            <!-- İçerik Editörü -->
                            <div class="col-12">
                                <label class="form-label">Sayfa İçeriği</label>
                                <div class="ca-editor-wrapper">
                                    <div class="ca-editor-toolbar">
                                        <div class="ca-toolbar-group">
                                            <button type="button" class="ca-toolbar-btn" onclick="execFormat('bold')" title="Kalın"><i class="bi bi-type-bold"></i></button>
                                            <button type="button" class="ca-toolbar-btn" onclick="execFormat('italic')" title="İtalik"><i class="bi bi-type-italic"></i></button>
                                            <button type="button" class="ca-toolbar-btn" onclick="execFormat('underline')" title="Altı çizili"><i class="bi bi-type-underline"></i></button>
                                            <button type="button" class="ca-toolbar-btn" onclick="execFormat('strikeThrough')" title="Üstü çizili"><i class="bi bi-type-strikethrough"></i></button>
                                        </div>
                                        <div class="ca-toolbar-divider"></div>
                                        <div class="ca-toolbar-group">
                                            <select class="ca-toolbar-select" onchange="execHeading(this.value)" title="Başlık stili">
                                                <option value="">Paragraf</option>
                                                <option value="h2">Başlık 2</option>
                                                <option value="h3">Başlık 3</option>
                                                <option value="h4">Başlık 4</option>
                                            </select>
                                        </div>
                                        <div class="ca-toolbar-divider"></div>
                                        <div class="ca-toolbar-group">
                                            <button type="button" class="ca-toolbar-btn" onclick="execFormat('justifyLeft')" title="Sola hizala"><i class="bi bi-text-left"></i></button>
                                            <button type="button" class="ca-toolbar-btn" onclick="execFormat('justifyCenter')" title="Ortala"><i class="bi bi-text-center"></i></button>
                                            <button type="button" class="ca-toolbar-btn" onclick="execFormat('justifyRight')" title="Sağa hizala"><i class="bi bi-text-right"></i></button>
                                        </div>
                                        <div class="ca-toolbar-divider"></div>
                                        <div class="ca-toolbar-group">
                                            <button type="button" class="ca-toolbar-btn" onclick="execFormat('insertUnorderedList')" title="Madde işaretli liste"><i class="bi bi-list-ul"></i></button>
                                            <button type="button" class="ca-toolbar-btn" onclick="execFormat('insertOrderedList')" title="Numaralı liste"><i class="bi bi-list-ol"></i></button>
                                            <button type="button" class="ca-toolbar-btn" onclick="insertBlockquote()" title="Alıntı"><i class="bi bi-blockquote-left"></i></button>
                                        </div>
                                        <div class="ca-toolbar-divider"></div>
                                        <div class="ca-toolbar-group">
                                            <button type="button" class="ca-toolbar-btn" onclick="insertLink()" title="Bağlantı ekle"><i class="bi bi-link-45deg"></i></button>
                                        </div>
                                    </div>
                                    <div class="ca-editor-body" id="contentEditor" contenteditable="true">{!! old('body', $settings['body'] ?? '<p>İçerik buraya yazılacak...</p>') !!}</div>
                                    <div class="ca-editor-footer">
                                        <span><i class="bi bi-fonts me-1"></i><span id="wordCount">0</span> kelime</span>
                                        <span><i class="bi bi-clock me-1"></i>~<span id="readTime">0</span> dk okuma</span>
                                    </div>
                                </div>
                                <textarea name="body" id="bodyHidden" class="d-none">{{ old('body', $settings['body'] ?? '') }}</textarea>
                                @error('body')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==================== SECTION 2: ÖNE ÇIKAN YAZAR ==================== -->
                <div class="card-dark mb-4" id="section-featured">
                    <div class="card-header-custom">
                        <div class="form-section-header mb-0">
                            <div class="form-section-icon bg-icon-purple"><i class="bi bi-star-fill"></i></div>
                            <div>
                                <h6 class="mb-0">Öne Çıkan Yazar</h6>
                                <small class="text-muted">Yazarlar sayfasının üst kısmında öne çıkarılacak yazarı seçin</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body-custom">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" for="apFeaturedAuthor">Yazar Seçin</label>
                                <select class="form-select @error('featured_author_id') is-invalid @enderror"
                                        id="apFeaturedAuthor" name="featured_author_id">
                                    <option value="">— Öne çıkan yazar yok —</option>
                                    @foreach($writers as $writer)
                                        <option value="{{ $writer->id }}" {{ old('featured_author_id', $settings['featured_author_id'] ?? '') == $writer->id ? 'selected' : '' }}>
                                            {{ $writer->name }} (@{{ $writer->username }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('featured_author_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Seçilen yazar, sayfanın üst kısmında öne çıkan yazar olarak gösterilecektir</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==================== SECTION 3: SEO ==================== -->
                <div class="card-dark mb-4" id="section-seo">
                    <div class="card-header-custom">
                        <div class="form-section-header mb-0">
                            <div class="form-section-icon bg-icon-pink"><i class="bi bi-search"></i></div>
                            <div>
                                <h6 class="mb-0">SEO Ayarları</h6>
                                <small class="text-muted">Arama motoru optimizasyonu</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body-custom">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" for="apMetaTitle">Meta Başlık</label>
                                <input type="text" class="form-control @error('meta_title') is-invalid @enderror"
                                       id="apMetaTitle" name="meta_title"
                                       value="{{ old('meta_title', $settings['meta_title'] ?? '') }}"
                                       placeholder="Yazarlarımız — Boyalı Kelimeler"
                                       maxlength="70"
                                       oninput="updateCharCounter(this, 70)">
                                @error('meta_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="d-flex justify-content-between mt-1">
                                    <div class="form-text">Arama sonuçlarında görünecek başlık</div>
                                    <div class="form-text"><span id="apMetaTitle-counter">{{ mb_strlen(old('meta_title', $settings['meta_title'] ?? '')) }}</span>/70</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="apMetaDesc">Meta Açıklama</label>
                                <textarea class="form-control @error('meta_description') is-invalid @enderror"
                                          id="apMetaDesc" name="meta_description" rows="3"
                                          placeholder="Boyalı Kelimeler yazarları ile tanışın..."
                                          maxlength="170"
                                          oninput="updateCharCounter(this, 170)">{{ old('meta_description', $settings['meta_description'] ?? '') }}</textarea>
                                @error('meta_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="d-flex justify-content-between mt-1">
                                    <div class="form-text">Arama sonuçlarında görünecek açıklama</div>
                                    <div class="form-text"><span id="apMetaDesc-counter">{{ mb_strlen(old('meta_description', $settings['meta_description'] ?? '')) }}</span>/170</div>
                                </div>
                            </div>

                            <!-- SEO Preview -->
                            <div class="col-12">
                                <div class="ca-seo-preview" data-aos="fade-up">
                                    <div class="ca-seo-url">{{ config('app.url') }}/yazarlar</div>
                                    <div class="ca-seo-title" id="seoPreviewTitle">{{ $settings['meta_title'] ?? 'Yazarlarımız — Boyalı Kelimeler' }}</div>
                                    <div class="ca-seo-desc" id="seoPreviewDesc">{{ $settings['meta_description'] ?? 'Boyalı Kelimeler yazarları ile tanışın.' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kaydet Butonu -->
                <div class="d-flex justify-content-end mb-4">
                    <button type="submit" class="btn-teal">
                        <i class="bi bi-check-lg me-1"></i> Kaydet
                    </button>
                </div>

            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script src="{{ asset('assets/admin/js/authors-page.js') }}?v={{ filemtime(public_path('assets/admin/js/app.js')) }}"></script>
@endpush
