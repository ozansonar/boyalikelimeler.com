@php
    $isEdit = isset($page);
@endphp

<!-- Mobile Section Jumper -->
<div class="d-lg-none mb-4">
    <select class="form-select form-select-sm" onchange="scrollToSection(this.value, null); this.selectedIndex=0">
        <option value="" disabled selected>Bölüme git...</option>
        <option value="section-basic">Temel Bilgiler</option>
        <option value="section-content">İçerik Editörü</option>
        <option value="section-media">Medya Yönetimi</option>
        <option value="section-seo">SEO Ayarları</option>
        <option value="section-settings">Sayfa Ayarları</option>
    </select>
</div>

<!-- Form Layout -->
<div class="row g-4 align-items-start">

    <!-- Sol Navigasyon (desktop) -->
    <div class="col-lg-3 d-none d-lg-block">
        <div class="stg-nav-inner position-sticky stg-nav-sticky">
            <a href="#section-basic" class="stg-nav-item active" onclick="scrollToSection('section-basic', this)">
                <i class="bi bi-text-paragraph"></i>
                <div><span>Temel Bilgiler</span><small>Başlık, kısa açıklama</small></div>
            </a>
            <a href="#section-content" class="stg-nav-item" onclick="scrollToSection('section-content', this)">
                <i class="bi bi-body-text"></i>
                <div><span>İçerik Editörü</span><small>Ana sayfa içeriği</small></div>
            </a>
            <a href="#section-media" class="stg-nav-item" onclick="scrollToSection('section-media', this)">
                <i class="bi bi-images"></i>
                <div><span>Medya Yönetimi</span><small>Kapak görseli</small></div>
            </a>
            <a href="#section-seo" class="stg-nav-item" onclick="scrollToSection('section-seo', this)">
                <i class="bi bi-search"></i>
                <div><span>SEO Ayarları</span><small>Meta başlık, açıklama</small></div>
            </a>
            <a href="#section-settings" class="stg-nav-item" onclick="scrollToSection('section-settings', this)">
                <i class="bi bi-gear"></i>
                <div><span>Sayfa Ayarları</span><small>Durum, sıralama</small></div>
            </a>
        </div>
    </div>

    <!-- Form İçeriği -->
    <div class="col-12 col-lg-9">

        <!-- ==================== SECTION 1: TEMEL BİLGİLER ==================== -->
        <div class="card-dark mb-4" id="section-basic">
            <div class="card-header-custom">
                <div class="form-section-header mb-0">
                    <div class="form-section-icon bg-icon-teal"><i class="bi bi-text-paragraph"></i></div>
                    <div>
                        <h6 class="mb-0">Temel Bilgiler</h6>
                        <small class="text-muted">Sayfanın başlığını ve kısa açıklamasını girin</small>
                    </div>
                </div>
            </div>
            <div class="card-body-custom">
                <div class="row g-3">
                    <!-- Başlık -->
                    <div class="col-12">
                        <label class="form-label" for="pageTitle">
                            Sayfa Başlığı <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="pageTitle" name="title"
                               value="{{ old('title', $page->title ?? '') }}"
                               placeholder="Örn: Hakkımızda, Gizlilik Politikası..."
                               oninput="generatePageSlug(this.value); updateCharCounter(this, 120)" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="d-flex justify-content-between mt-1">
                            <div class="form-text">Sayfanın ana başlığı (H1 olarak görünür)</div>
                            <div class="form-text"><span id="pageTitle-counter">{{ mb_strlen(old('title', $page->title ?? '')) }}</span>/120</div>
                        </div>
                    </div>

                    <!-- Slug -->
                    <div class="col-12">
                        <label class="form-label" for="pageSlug">URL (Slug)</label>
                        <div class="input-group">
                            <span class="input-group-text">/</span>
                            <input type="text" class="form-control" id="pageSlug" name="slug"
                                   value="{{ old('slug', $page->slug ?? '') }}"
                                   placeholder="otomatik-olusturulur" readonly>
                        </div>
                        <div class="form-text">Başlık yazıldığında otomatik oluşturulur — ör: /hakkimizda</div>
                    </div>

                    <!-- Kısa Açıklama -->
                    <div class="col-12">
                        <label class="form-label" for="pageExcerpt">Kısa Açıklama</label>
                        <textarea class="form-control @error('excerpt') is-invalid @enderror"
                                  id="pageExcerpt" name="excerpt" rows="3"
                                  placeholder="Sayfa hakkında kısa bir açıklama yazın..."
                                  oninput="updateCharCounter(this, 300)">{{ old('excerpt', $page->excerpt ?? '') }}</textarea>
                        @error('excerpt')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="d-flex justify-content-between mt-1">
                            <div class="form-text">Sayfa başlığının altında gösterilecek açıklama metni</div>
                            <div class="form-text"><span id="pageExcerpt-counter">{{ mb_strlen(old('excerpt', $page->excerpt ?? '')) }}</span>/300</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- ==================== SECTION 2: İÇERİK EDİTÖRÜ ==================== -->
        <div class="card-dark mb-4" id="section-content">
            <div class="card-header-custom">
                <div class="form-section-header mb-0">
                    <div class="form-section-icon bg-icon-purple"><i class="bi bi-body-text"></i></div>
                    <div>
                        <h6 class="mb-0">İçerik Editörü</h6>
                        <small class="text-muted">Sayfanın ana içeriğini oluşturun</small>
                    </div>
                </div>
            </div>
            <div class="card-body-custom">
                <div class="row g-3">
                    <!-- Ana İçerik Editörü -->
                    <div class="col-12">
                        <label class="form-label">
                            Sayfa İçeriği <span class="text-danger">*</span>
                        </label>
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
                                    <button type="button" class="ca-toolbar-btn" onclick="insertCode()" title="Kod bloğu"><i class="bi bi-code-slash"></i></button>
                                </div>
                            </div>
                            <div class="ca-editor-body" id="contentEditor" contenteditable="true">{!! old('body', $page->body ?? '<p>Sayfa içeriğinizi buraya yazın...</p>') !!}</div>
                            <div class="ca-editor-footer">
                                <span><i class="bi bi-fonts me-1"></i><span id="wordCount">0</span> kelime</span>
                                <span><i class="bi bi-clock me-1"></i>~<span id="readTime">0</span> dk okuma</span>
                            </div>
                        </div>
                        <textarea name="body" id="bodyHidden" class="d-none">{{ old('body', $page->body ?? '') }}</textarea>
                        @error('body')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
        </div>


        <!-- ==================== SECTION 3: MEDYA YÖNETİMİ ==================== -->
        <div class="card-dark mb-4" id="section-media">
            <div class="card-header-custom">
                <div class="form-section-header mb-0">
                    <div class="form-section-icon bg-icon-blue"><i class="bi bi-images"></i></div>
                    <div>
                        <h6 class="mb-0">Medya Yönetimi</h6>
                        <small class="text-muted">Kapak görseli yükleyin</small>
                    </div>
                </div>
            </div>
            <div class="card-body-custom">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label" for="coverInput">Kapak Görseli</label>
                        @if($isEdit && $page->cover_image)
                            <div class="mb-2">
                                <img src="/uploads/{{ $page->cover_image }}" alt="" class="img-fluid rounded" loading="lazy" style="max-height:200px">
                            </div>
                        @endif
                        <input type="file" class="form-control @error('cover_image') is-invalid @enderror"
                               id="coverInput" name="cover_image" accept="image/png,image/jpeg,image/webp">
                        @error('cover_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">PNG, JPG, WebP | Maks. 1 MB | Önerilen: 1200×630 px</div>
                    </div>
                </div>
            </div>
        </div>


        <!-- ==================== SECTION 4: SEO AYARLARI ==================== -->
        <div class="card-dark mb-4" id="section-seo">
            <div class="card-header-custom">
                <div class="form-section-header mb-0">
                    <div class="form-section-icon bg-icon-orange"><i class="bi bi-search"></i></div>
                    <div>
                        <h6 class="mb-0">SEO Ayarları</h6>
                        <small class="text-muted">Arama motorları için meta bilgilerini düzenleyin</small>
                    </div>
                </div>
            </div>
            <div class="card-body-custom">
                <div class="row g-3">
                    <!-- SEO Önizleme -->
                    <div class="col-12">
                        <label class="form-label">Google Arama Önizlemesi</label>
                        <div class="ca-seo-preview">
                            <div class="ca-seo-url">/<span id="seoPreviewSlug">{{ $page->slug ?? 'yeni-sayfa' }}</span></div>
                            <div class="ca-seo-title" id="seoPreviewTitle">{{ $page->meta_title ?? $page->title ?? 'Sayfa Başlığı' }}</div>
                            <div class="ca-seo-desc" id="seoPreviewDesc">{{ $page->meta_description ?? 'Meta açıklama burada görünecek.' }}</div>
                        </div>
                    </div>

                    <!-- Meta Başlık -->
                    <div class="col-12">
                        <label class="form-label" for="metaTitle">Meta Başlık</label>
                        <input type="text" class="form-control" id="metaTitle" name="meta_title"
                               value="{{ old('meta_title', $page->meta_title ?? '') }}"
                               placeholder="SEO için özel başlık (boş bırakılırsa sayfa başlığı kullanılır)"
                               oninput="updateSeoPreview(); updateCharCounter(this, 60)">
                        <div class="d-flex justify-content-between mt-1">
                            <div class="form-text">Önerilen: 50–60 karakter</div>
                            <div class="form-text"><span id="metaTitle-counter">{{ mb_strlen(old('meta_title', $page->meta_title ?? '')) }}</span>/60</div>
                        </div>
                    </div>

                    <!-- Meta Açıklama -->
                    <div class="col-12">
                        <label class="form-label" for="metaDescription">Meta Açıklama</label>
                        <textarea class="form-control" id="metaDescription" name="meta_description" rows="3"
                                  placeholder="Arama sonuçlarında görünecek açıklama metni..."
                                  oninput="updateSeoPreview(); updateCharCounter(this, 160)">{{ old('meta_description', $page->meta_description ?? '') }}</textarea>
                        <div class="d-flex justify-content-between mt-1">
                            <div class="form-text">Önerilen: 120–160 karakter</div>
                            <div class="form-text"><span id="metaDescription-counter">{{ mb_strlen(old('meta_description', $page->meta_description ?? '')) }}</span>/160</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- ==================== SECTION 5: SAYFA AYARLARI ==================== -->
        <div class="card-dark mb-4" id="section-settings">
            <div class="card-header-custom">
                <div class="form-section-header mb-0">
                    <div class="form-section-icon bg-icon-teal"><i class="bi bi-gear"></i></div>
                    <div>
                        <h6 class="mb-0">Sayfa Ayarları</h6>
                        <small class="text-muted">Yayın durumunu ve sıralamayı ayarlayın</small>
                    </div>
                </div>
            </div>
            <div class="card-body-custom">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="pageStatus">
                            Durum <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('is_active') is-invalid @enderror" id="pageStatus" name="is_active" required>
                            <option value="1" {{ old('is_active', $page->is_active ?? true) == true ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('is_active', $page->is_active ?? true) == false ? 'selected' : '' }}>Pasif</option>
                        </select>
                        @error('is_active')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Pasif sayfalar ziyaretçilere görünmez</div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="pageOrder">Sıralama</label>
                        <input type="number" class="form-control" id="pageOrder" name="sort_order"
                               value="{{ old('sort_order', $page->sort_order ?? 0) }}"
                               min="0" max="999" placeholder="0">
                        <div class="form-text">Düşük değer = Daha önce listelenir</div>
                    </div>
                </div>
            </div>
        </div>


        <!-- ==================== FORM ACTIONS ==================== -->
        <div class="card-dark mb-4">
            <div class="card-body-custom">
                <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center justify-content-between gap-3">
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.pages.index') }}" class="btn-glass">
                            <i class="bi bi-x-lg me-1"></i>Vazgeç
                        </a>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="submit" class="btn-teal" onclick="syncEditor()">
                            <i class="bi bi-send me-1"></i>{{ $isEdit ? 'Değişiklikleri Kaydet' : 'Sayfayı Oluştur' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- /col-12 col-lg-9 -->
</div><!-- /row -->
