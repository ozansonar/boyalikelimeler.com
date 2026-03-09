@extends('layouts.admin')

@section('title', 'Ressamlar Sayfası — Boyalı Kelimeler Admin')

@section('content')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3" data-aos="fade-down" data-aos-duration="400">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link"><i class="bi bi-house"></i> Ana Sayfa</a></li>
            <li class="breadcrumb-item active text-teal">Ressamlar Sayfası</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3" data-aos="fade-down">
        <div>
            <h1 class="page-title">Ressamlar Sayfası</h1>
            <p class="page-subtitle">Ressamlar sayfasının bölümlerini, öne çıkan ressamı ve SEO ayarlarını yönetin</p>
        </div>
    </div>

    <!-- Form Layout -->
    <div class="row g-4 align-items-start">

        <!-- Sol Navigasyon (desktop) -->
        <div class="col-lg-3 d-none d-lg-block">
            <div class="stg-nav-inner position-sticky stg-nav-sticky">
                <a href="#section-header" class="stg-nav-item active" onclick="scrollToSection('section-header', this)">
                    <i class="bi bi-body-text"></i>
                    <div><span>Sayfa Başlığı</span><small>Başlık ve açıklama</small></div>
                </a>
                <a href="#section-featured" class="stg-nav-item" onclick="scrollToSection('section-featured', this)">
                    <i class="bi bi-star"></i>
                    <div><span>Öne Çıkan Ressamlar</span><small>Sayfa üstünde gösterilecek ressamlar</small></div>
                </a>
                <a href="#section-list" class="stg-nav-item" onclick="scrollToSection('section-list', this)">
                    <i class="bi bi-people"></i>
                    <div><span>Ressam Listesi</span><small>Liste bölümü başlığı</small></div>
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
                <option value="section-header">Sayfa Başlığı</option>
                <option value="section-featured">Öne Çıkan Ressamlar</option>
                <option value="section-list">Ressam Listesi</option>
                <option value="section-seo">SEO Ayarları</option>
            </select>
        </div>

        <!-- Form İçeriği -->
        <div class="col-12 col-lg-9">
            <form action="{{ route('admin.painters-page.update') }}" method="POST" id="paintersPageForm">
                @csrf
                @method('PUT')

                <!-- ==================== SECTION 1: SAYFA BAŞLIĞI ==================== -->
                <div class="card-dark mb-4" id="section-header">
                    <div class="card-header-custom">
                        <div class="form-section-header mb-0">
                            <div class="form-section-icon bg-icon-teal"><i class="bi bi-body-text"></i></div>
                            <div>
                                <h6 class="mb-0">Sayfa Başlığı</h6>
                                <small class="text-muted">Ressamlar sayfasının üst kısmında görünecek başlık ve açıklama</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body-custom">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" for="ppTitle">Sayfa Başlığı</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                       id="ppTitle" name="title"
                                       value="{{ old('title', $settings['title'] ?? '') }}"
                                       placeholder="Örn: Ressamlarımız"
                                       oninput="updateCharCounter(this, 200)">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="d-flex justify-content-between mt-1">
                                    <div class="form-text">Sayfanın ana başlığı (H1 olarak görünür)</div>
                                    <div class="form-text"><span id="ppTitle-counter">{{ mb_strlen(old('title', $settings['title'] ?? '')) }}</span>/200</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="ppDescription">Kısa Açıklama</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="ppDescription" name="description" rows="3"
                                          placeholder="Ressamlar sayfası hakkında kısa bir açıklama yazın..."
                                          oninput="updateCharCounter(this, 500)">{{ old('description', $settings['description'] ?? '') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="d-flex justify-content-between mt-1">
                                    <div class="form-text">Başlığın altında gösterilecek açıklama</div>
                                    <div class="form-text"><span id="ppDescription-counter">{{ mb_strlen(old('description', $settings['description'] ?? '')) }}</span>/500</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==================== SECTION 2: ÖNE ÇIKAN RESSAMLAR ==================== -->
                <div class="card-dark mb-4" id="section-featured">
                    <div class="card-header-custom">
                        <div class="form-section-header mb-0">
                            <div class="form-section-icon bg-icon-purple"><i class="bi bi-star-fill"></i></div>
                            <div>
                                <h6 class="mb-0">Öne Çıkan Ressamlar</h6>
                                <small class="text-muted">Ressamlar sayfasının üst kısmında öne çıkarılacak ressamları seçin (satırda 4 kart gösterilir)</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body-custom">
                        @php
                            $savedIds = json_decode($settings['featured_painter_ids'] ?? '[]', true) ?: [];
                            $savedLabels = json_decode($settings['featured_painter_labels'] ?? '{}', true) ?: [];
                            $oldIds = old('featured_painter_ids', $savedIds);
                            $oldLabels = old('featured_painter_labels', $savedLabels);
                        @endphp

                        <div id="featuredPaintersContainer">
                            @forelse($oldIds as $idx => $painterId)
                                <div class="featured-painter-row mb-3 p-3 border border-secondary rounded">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <select class="form-select" name="featured_painter_ids[]">
                                            <option value="">— Ressam seçin —</option>
                                            @foreach($painters as $painter)
                                                <option value="{{ $painter->id }}" {{ (int) $painterId === $painter->id ? 'selected' : '' }}>
                                                    {{ $painter->name }} ({{ '@' }}{{ $painter->username }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-sm btn-outline-danger js-remove-featured-painter" title="Kaldır">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                    <input type="text" class="form-control form-control-sm" name="featured_painter_labels[]"
                                           value="{{ $oldLabels[$painterId] ?? '' }}"
                                           placeholder="Kısa açıklama yazın (ör: Sulu boya ustası, Dijital sanatçı...)" maxlength="150">
                                </div>
                            @empty
                                <div class="featured-painter-row mb-3 p-3 border border-secondary rounded">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <select class="form-select" name="featured_painter_ids[]">
                                            <option value="">— Ressam seçin —</option>
                                            @foreach($painters as $painter)
                                                <option value="{{ $painter->id }}">
                                                    {{ $painter->name }} ({{ '@' }}{{ $painter->username }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-sm btn-outline-danger js-remove-featured-painter" title="Kaldır">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                    <input type="text" class="form-control form-control-sm" name="featured_painter_labels[]"
                                           value="" placeholder="Kısa açıklama yazın (ör: Sulu boya ustası, Dijital sanatçı...)" maxlength="150">
                                </div>
                            @endforelse
                        </div>

                        @error('featured_painter_ids')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        @error('featured_painter_ids.*')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror

                        <button type="button" class="btn btn-sm btn-outline-teal mt-2" id="addFeaturedPainter">
                            <i class="bi bi-plus-lg me-1"></i> Ressam Ekle
                        </button>
                        <div class="form-text mt-2">Seçilen ressamlar sayfanın üst kısmında grid halinde gösterilir. Satırda 4 kart sığar.</div>
                    </div>
                </div>

                <!-- ==================== SECTION 3: RESSAM LİSTESİ ==================== -->
                <div class="card-dark mb-4" id="section-list">
                    <div class="card-header-custom">
                        <div class="form-section-header mb-0">
                            <div class="form-section-icon bg-icon-teal"><i class="bi bi-people-fill"></i></div>
                            <div>
                                <h6 class="mb-0">Ressam Listesi Bölümü</h6>
                                <small class="text-muted">Tüm ressamların listelendiği bölümün başlığı</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body-custom">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" for="ppListTitle">Bölüm Başlığı</label>
                                <input type="text" class="form-control @error('painters_list_title') is-invalid @enderror"
                                       id="ppListTitle" name="painters_list_title"
                                       value="{{ old('painters_list_title', $settings['painters_list_title'] ?? '') }}"
                                       placeholder="Örn: Ressamlarımız"
                                       oninput="updateCharCounter(this, 200)">
                                @error('painters_list_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="d-flex justify-content-between mt-1">
                                    <div class="form-text">Ressam listesi bölümünün başlığı</div>
                                    <div class="form-text"><span id="ppListTitle-counter">{{ mb_strlen(old('painters_list_title', $settings['painters_list_title'] ?? '')) }}</span>/200</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==================== SECTION 4: SEO ==================== -->
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
                                <label class="form-label" for="ppMetaTitle">Meta Başlık</label>
                                <input type="text" class="form-control @error('meta_title') is-invalid @enderror"
                                       id="ppMetaTitle" name="meta_title"
                                       value="{{ old('meta_title', $settings['meta_title'] ?? '') }}"
                                       placeholder="Ressamlarımız — Boyalı Kelimeler"
                                       maxlength="70"
                                       oninput="updateCharCounter(this, 70)">
                                @error('meta_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="d-flex justify-content-between mt-1">
                                    <div class="form-text">Arama sonuçlarında görünecek başlık</div>
                                    <div class="form-text"><span id="ppMetaTitle-counter">{{ mb_strlen(old('meta_title', $settings['meta_title'] ?? '')) }}</span>/70</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="ppMetaDesc">Meta Açıklama</label>
                                <textarea class="form-control @error('meta_description') is-invalid @enderror"
                                          id="ppMetaDesc" name="meta_description" rows="3"
                                          placeholder="Boyalı Kelimeler ressamları ile tanışın..."
                                          maxlength="170"
                                          oninput="updateCharCounter(this, 170)">{{ old('meta_description', $settings['meta_description'] ?? '') }}</textarea>
                                @error('meta_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="d-flex justify-content-between mt-1">
                                    <div class="form-text">Arama sonuçlarında görünecek açıklama</div>
                                    <div class="form-text"><span id="ppMetaDesc-counter">{{ mb_strlen(old('meta_description', $settings['meta_description'] ?? '')) }}</span>/170</div>
                                </div>
                            </div>

                            <!-- SEO Preview -->
                            <div class="col-12">
                                <div class="ca-seo-preview" data-aos="fade-up">
                                    <div class="ca-seo-url">{{ config('app.url') }}/ressamlar</div>
                                    <div class="ca-seo-title" id="seoPreviewTitle">{{ $settings['meta_title'] ?? 'Ressamlarımız — Boyalı Kelimeler' }}</div>
                                    <div class="ca-seo-desc" id="seoPreviewDesc">{{ $settings['meta_description'] ?? 'Boyalı Kelimeler ressamları ile tanışın.' }}</div>
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
<script src="{{ asset('assets/admin/js/painters-page.js') }}?v={{ filemtime(public_path('assets/admin/js/painters-page.js')) }}"></script>
@endpush
