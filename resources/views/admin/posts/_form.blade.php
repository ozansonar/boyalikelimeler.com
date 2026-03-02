@php
    $isEdit = isset($post);
@endphp

<!-- Mobile Section Jumper -->
<div class="d-lg-none mb-4">
    <select class="form-select form-select-sm" onchange="scrollToSection(this.value, null); this.selectedIndex=0">
        <option value="" disabled selected>Bölüme git...</option>
        <option value="section-basic">Temel Bilgiler</option>
        <option value="section-content">İçerik Editörü</option>
        <option value="section-media">Medya Yönetimi</option>
        <option value="section-seo">SEO Ayarları</option>
        <option value="section-publish">Yayın Ayarları</option>
        <option value="section-advanced">Gelişmiş Ayarlar</option>
    </select>
</div>

<!-- Form Layout -->
<div class="row g-4 align-items-start">

    <!-- Sol Navigasyon (desktop) -->
    <div class="col-lg-3 d-none d-lg-block">
        <div class="stg-nav-inner position-sticky stg-nav-sticky">
            <a href="#section-basic" class="stg-nav-item active" onclick="scrollToSection('section-basic', this)">
                <i class="bi bi-text-paragraph"></i>
                <div><span>Temel Bilgiler</span><small>Başlık, slug, kategori</small></div>
            </a>
            <a href="#section-content" class="stg-nav-item" onclick="scrollToSection('section-content', this)">
                <i class="bi bi-body-text"></i>
                <div><span>İçerik Editörü</span><small>Ana metin ve özet</small></div>
            </a>
            <a href="#section-media" class="stg-nav-item" onclick="scrollToSection('section-media', this)">
                <i class="bi bi-images"></i>
                <div><span>Medya Yönetimi</span><small>Kapak görseli</small></div>
            </a>
            <a href="#section-seo" class="stg-nav-item" onclick="scrollToSection('section-seo', this)">
                <i class="bi bi-search"></i>
                <div><span>SEO Ayarları</span><small>Meta başlık, açıklama</small></div>
            </a>
            <a href="#section-publish" class="stg-nav-item" onclick="scrollToSection('section-publish', this)">
                <i class="bi bi-calendar-event"></i>
                <div><span>Yayın Ayarları</span><small>Durum, tarih</small></div>
            </a>
            <a href="#section-advanced" class="stg-nav-item" onclick="scrollToSection('section-advanced', this)">
                <i class="bi bi-gear"></i>
                <div><span>Gelişmiş Ayarlar</span><small>Sıralama, yorum, seçenekler</small></div>
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
                        <small class="text-muted">İçeriğin başlığını, URL yapısını ve kategorisini belirleyin</small>
                    </div>
                </div>
            </div>
            <div class="card-body-custom">
                <div class="row g-3">
                    <!-- Başlık -->
                    <div class="col-12">
                        <label class="form-label" for="contentTitle">
                            İçerik Başlığı <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="contentTitle" name="title"
                               value="{{ old('title', $post->title ?? '') }}"
                               placeholder="İçeriğin ana başlığını yazın..."
                               oninput="generateSlug(this.value, 'contentSlug', 'seoPreviewSlug', 'yeni-icerik'); updateSeoPreview(); updateCharCounter(this, 120)" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="d-flex justify-content-between mt-1">
                            <div class="form-text">Dikkat çekici ve SEO uyumlu bir başlık girin</div>
                            <div class="form-text"><span id="contentTitle-counter">{{ mb_strlen(old('title', $post->title ?? '')) }}</span>/120</div>
                        </div>
                    </div>

                    <!-- Slug -->
                    <div class="col-12">
                        <label class="form-label" for="contentSlug">URL (Slug)</label>
                        <div class="input-group">
                            <span class="input-group-text">/icerik/</span>
                            <input type="text" class="form-control" id="contentSlug" name="slug"
                                   value="{{ old('slug', $post->slug ?? '') }}"
                                   placeholder="otomatik-oluşturulur" readonly>
                        </div>
                        <div class="form-text">Başlık yazıldığında otomatik oluşturulur</div>
                    </div>

                    <!-- Kategori -->
                    <div class="col-12">
                        <label class="form-label" for="contentCategory">
                            Kategori <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="contentCategory" name="category_id" required>
                            <option value="">Kategori seçin...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $post->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
                        <small class="text-muted">İçeriğin ana metnini ve kısa özetini yazın</small>
                    </div>
                </div>
            </div>
            <div class="card-body-custom">
                <div class="row g-3">
                    <!-- Kısa Özet -->
                    <div class="col-12">
                        <label class="form-label" for="contentExcerpt">Kısa Özet</label>
                        <textarea class="form-control @error('excerpt') is-invalid @enderror"
                                  id="contentExcerpt" name="excerpt" rows="3"
                                  placeholder="İçeriğin kısa bir özetini yazın..."
                                  oninput="updateCharCounter(this, 300)">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
                        @error('excerpt')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="d-flex justify-content-between mt-1">
                            <div class="form-text">Arama sonuçlarında ve listelerde gösterilecek kısa açıklama</div>
                            <div class="form-text"><span id="contentExcerpt-counter">{{ mb_strlen(old('excerpt', $post->excerpt ?? '')) }}</span>/300</div>
                        </div>
                    </div>

                    <!-- Ana İçerik Editörü -->
                    <div class="col-12">
                        <label class="form-label">
                            Ana İçerik <span class="text-danger">*</span>
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
                            <div class="ca-editor-body" id="contentEditor" contenteditable="true">{!! old('body', $post->body ?? '<p>İçeriğinizi buraya yazmaya başlayın...</p>') !!}</div>
                            <div class="ca-editor-footer">
                                <span><i class="bi bi-fonts me-1"></i><span id="wordCount">0</span> kelime</span>
                                <span><i class="bi bi-clock me-1"></i>~<span id="readTime">0</span> dk okuma</span>
                            </div>
                        </div>
                        <textarea name="body" id="bodyHidden" class="d-none">{{ old('body', $post->body ?? '') }}</textarea>
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
                        @if($isEdit && $post->cover_image)
                            <div class="mb-2">
                                <img src="/uploads/{{ $post->cover_image }}" alt="" class="img-fluid rounded" loading="lazy" style="max-height:200px">
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
                            <div class="ca-seo-url">/icerik/<span id="seoPreviewSlug">{{ $post->slug ?? 'yeni-icerik' }}</span></div>
                            <div class="ca-seo-title" id="seoPreviewTitle">{{ $post->meta_title ?? $post->title ?? 'İçerik Başlığı' }}</div>
                            <div class="ca-seo-desc" id="seoPreviewDesc">{{ $post->meta_description ?? 'Meta açıklama burada görünecek.' }}</div>
                        </div>
                    </div>

                    <!-- Meta Başlık -->
                    <div class="col-12">
                        <label class="form-label" for="metaTitle">Meta Başlık</label>
                        <input type="text" class="form-control" id="metaTitle" name="meta_title"
                               value="{{ old('meta_title', $post->meta_title ?? '') }}"
                               placeholder="SEO için özel başlık (boş bırakılırsa içerik başlığı kullanılır)"
                               oninput="updateSeoPreview(); updateCharCounter(this, 60)">
                        <div class="d-flex justify-content-between mt-1">
                            <div class="form-text">Önerilen: 50–60 karakter</div>
                            <div class="form-text"><span id="metaTitle-counter">{{ mb_strlen(old('meta_title', $post->meta_title ?? '')) }}</span>/60</div>
                        </div>
                    </div>

                    <!-- Meta Açıklama -->
                    <div class="col-12">
                        <label class="form-label" for="metaDescription">Meta Açıklama</label>
                        <textarea class="form-control" id="metaDescription" name="meta_description" rows="3"
                                  placeholder="Arama sonuçlarında görünecek açıklama metni..."
                                  oninput="updateSeoPreview(); updateCharCounter(this, 160)">{{ old('meta_description', $post->meta_description ?? '') }}</textarea>
                        <div class="d-flex justify-content-between mt-1">
                            <div class="form-text">Önerilen: 120–160 karakter</div>
                            <div class="form-text"><span id="metaDescription-counter">{{ mb_strlen(old('meta_description', $post->meta_description ?? '')) }}</span>/160</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- ==================== SECTION 5: YAYIN AYARLARI ==================== -->
        <div class="card-dark mb-4" id="section-publish">
            <div class="card-header-custom">
                <div class="form-section-header mb-0">
                    <div class="form-section-icon bg-icon-teal"><i class="bi bi-calendar-event"></i></div>
                    <div>
                        <h6 class="mb-0">Yayın Ayarları</h6>
                        <small class="text-muted">İçeriğin yayın durumunu ayarlayın</small>
                    </div>
                </div>
            </div>
            <div class="card-body-custom">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="publishStatus">
                            Yayın Durumu <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('status') is-invalid @enderror" id="publishStatus" name="status" onchange="toggleScheduleDate(this.value)" required>
                            <option value="draft" {{ old('status', $post->status->value ?? 'draft') === 'draft' ? 'selected' : '' }}>Taslak</option>
                            <option value="published" {{ old('status', $post->status->value ?? '') === 'published' ? 'selected' : '' }}>Yayınla</option>
                            <option value="scheduled" {{ old('status', $post->status->value ?? '') === 'scheduled' ? 'selected' : '' }}>Zamanla</option>
                            <option value="archived" {{ old('status', $post->status->value ?? '') === 'archived' ? 'selected' : '' }}>Arşivle</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6 {{ old('status', $post->status->value ?? 'draft') === 'scheduled' ? '' : 'd-none' }}" id="scheduleDateWrapper">
                        <label class="form-label" for="publishDate">Yayın Tarihi</label>
                        <input type="datetime-local" class="form-control" id="publishDate" name="published_at"
                               value="{{ old('published_at', isset($post) && $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '') }}">
                    </div>
                </div>
            </div>
        </div>


        <!-- ==================== SECTION 6: GELİŞMİŞ AYARLAR ==================== -->
        <div class="card-dark mb-4" id="section-advanced">
            <div class="card-header-custom">
                <div class="form-section-header mb-0">
                    <div class="form-section-icon bg-icon-purple"><i class="bi bi-gear"></i></div>
                    <div>
                        <h6 class="mb-0">Gelişmiş Ayarlar</h6>
                        <small class="text-muted">Sıralama, yorum ayarları ve diğer seçenekler</small>
                    </div>
                </div>
            </div>
            <div class="card-body-custom">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="contentOrder">Sıralama</label>
                        <input type="number" class="form-control" id="contentOrder" name="sort_order"
                               value="{{ old('sort_order', $post->sort_order ?? 0) }}"
                               min="0" max="999" placeholder="0">
                        <div class="form-text">Düşük değer = Daha üstte görünür</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label mb-3">Sayfa Seçenekleri</label>
                        <div class="ca-toggle-list">
                            <div class="ca-toggle-item">
                                <div class="ca-toggle-info">
                                    <span>Yorumlara İzin Ver</span>
                                    <small>Okuyucuların bu içeriğe yorum yapabilmesini sağlar</small>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="allow_comments" value="1"
                                           {{ old('allow_comments', $post->allow_comments ?? true) ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="ca-toggle-item">
                                <div class="ca-toggle-info">
                                    <span>Öne Çıkan İçerik</span>
                                    <small>Ana sayfada ve öne çıkanlar bölümünde gösterilir</small>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="is_featured" value="1"
                                           {{ old('is_featured', $post->is_featured ?? false) ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- ==================== FORM ACTIONS ==================== -->
        <div class="card-dark mb-4">
            <div class="card-body-custom">
                <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center justify-content-between gap-3">
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.posts.index') }}" class="btn-glass">
                            <i class="bi bi-x-lg me-1"></i>Vazgeç
                        </a>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="submit" name="status" value="draft" class="btn-glass">
                            <i class="bi bi-file-earmark me-1"></i>Taslak Kaydet
                        </button>
                        <button type="submit" class="btn-teal">
                            <i class="bi bi-send me-1"></i>{{ $isEdit ? 'Değişiklikleri Kaydet' : 'İçeriği Yayınla' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- /col-12 col-lg-9 -->
</div><!-- /row -->
