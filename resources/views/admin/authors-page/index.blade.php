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
            <p class="page-subtitle">Yazarlar sayfasının bölümlerini, öne çıkan yazarı ve SEO ayarlarını yönetin</p>
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
                    <div><span>Öne Çıkan Yazar</span><small>Sayfa üstünde gösterilecek yazar</small></div>
                </a>
                <a href="#section-golden" class="stg-nav-item" onclick="scrollToSection('section-golden', this)">
                    <i class="bi bi-pen"></i>
                    <div><span>Altın Kalemler</span><small>Altın kalem bölümü başlık/açıklama</small></div>
                </a>
                <a href="#section-list" class="stg-nav-item" onclick="scrollToSection('section-list', this)">
                    <i class="bi bi-people"></i>
                    <div><span>Yazar Listesi</span><small>Liste bölümü başlığı</small></div>
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
                <option value="section-featured">Öne Çıkan Yazar</option>
                <option value="section-golden">Altın Kalemler</option>
                <option value="section-list">Yazar Listesi</option>
                <option value="section-seo">SEO Ayarları</option>
            </select>
        </div>

        <!-- Form İçeriği -->
        <div class="col-12 col-lg-9">
            <form action="{{ route('admin.authors-page.update') }}" method="POST" id="authorsPageForm">
                @csrf
                @method('PUT')

                <!-- ==================== SECTION 1: SAYFA BAŞLIĞI ==================== -->
                <div class="card-dark mb-4" id="section-header">
                    <div class="card-header-custom">
                        <div class="form-section-header mb-0">
                            <div class="form-section-icon bg-icon-teal"><i class="bi bi-body-text"></i></div>
                            <div>
                                <h6 class="mb-0">Sayfa Başlığı</h6>
                                <small class="text-muted">Yazarlar sayfasının üst kısmında görünecek başlık ve açıklama</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body-custom">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" for="apTitle">Sayfa Başlığı</label>
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

                <!-- ==================== SECTION 3: ALTIN KALEMLER ==================== -->
                <div class="card-dark mb-4" id="section-golden">
                    <div class="card-header-custom">
                        <div class="form-section-header mb-0">
                            <div class="form-section-icon bg-icon-pink"><i class="bi bi-pen-fill"></i></div>
                            <div>
                                <h6 class="mb-0">Altın Kalemler Bölümü</h6>
                                <small class="text-muted">Altın kalem slider bölümünün başlığı ve açıklaması</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body-custom">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" for="apGoldenTitle">Bölüm Başlığı</label>
                                <input type="text" class="form-control @error('golden_pen_title') is-invalid @enderror"
                                       id="apGoldenTitle" name="golden_pen_title"
                                       value="{{ old('golden_pen_title', $settings['golden_pen_title'] ?? '') }}"
                                       placeholder="Örn: Altın Kalemlerimiz"
                                       oninput="updateCharCounter(this, 200)">
                                @error('golden_pen_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="d-flex justify-content-between mt-1">
                                    <div class="form-text">Altın kalem slider bölümünün başlığı</div>
                                    <div class="form-text"><span id="apGoldenTitle-counter">{{ mb_strlen(old('golden_pen_title', $settings['golden_pen_title'] ?? '')) }}</span>/200</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="apGoldenDesc">Bölüm Açıklaması</label>
                                <textarea class="form-control @error('golden_pen_description') is-invalid @enderror"
                                          id="apGoldenDesc" name="golden_pen_description" rows="3"
                                          placeholder="Altın kalem bölümü açıklaması..."
                                          oninput="updateCharCounter(this, 500)">{{ old('golden_pen_description', $settings['golden_pen_description'] ?? '') }}</textarea>
                                @error('golden_pen_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="d-flex justify-content-between mt-1">
                                    <div class="form-text">Başlığın altında gösterilecek açıklama</div>
                                    <div class="form-text"><span id="apGoldenDesc-counter">{{ mb_strlen(old('golden_pen_description', $settings['golden_pen_description'] ?? '')) }}</span>/500</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==================== SECTION 4: YAZAR LİSTESİ ==================== -->
                <div class="card-dark mb-4" id="section-list">
                    <div class="card-header-custom">
                        <div class="form-section-header mb-0">
                            <div class="form-section-icon bg-icon-teal"><i class="bi bi-people-fill"></i></div>
                            <div>
                                <h6 class="mb-0">Yazar Listesi Bölümü</h6>
                                <small class="text-muted">Tüm yazarların listelendiği bölümün başlığı</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body-custom">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" for="apListTitle">Bölüm Başlığı</label>
                                <input type="text" class="form-control @error('authors_list_title') is-invalid @enderror"
                                       id="apListTitle" name="authors_list_title"
                                       value="{{ old('authors_list_title', $settings['authors_list_title'] ?? '') }}"
                                       placeholder="Örn: Yazarlarımız"
                                       oninput="updateCharCounter(this, 200)">
                                @error('authors_list_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="d-flex justify-content-between mt-1">
                                    <div class="form-text">Yazar listesi bölümünün başlığı</div>
                                    <div class="form-text"><span id="apListTitle-counter">{{ mb_strlen(old('authors_list_title', $settings['authors_list_title'] ?? '')) }}</span>/200</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==================== SECTION 5: SEO ==================== -->
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
<script src="{{ asset('assets/admin/js/authors-page.js') }}?v={{ filemtime(public_path('assets/admin/js/authors-page.js')) }}"></script>
@endpush
