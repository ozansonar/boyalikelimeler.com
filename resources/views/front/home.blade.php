@extends('layouts.front')

@section('title', 'Boyalı Kelimeler — Sosyal Çöküntüye Sanatsal Direniş')
@section('meta_description', 'Boyalı Kelimeler, sanat ve edebiyatın buluştuğu premium bir platform. Şiir, hikaye, resim ve daha fazlası.')
@section('canonical', url('/'))
@section('og_title', 'Boyalı Kelimeler — Sosyal Çöküntüye Sanatsal Direniş')
@section('og_description', 'Sanat ve edebiyatın buluştuğu premium platform.')
@section('og_image', asset('images/og-cover.jpg'))

@push('jsonld')
<script type="application/ld+json">
{!! json_encode([
    '@@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'WebSite',
            'name' => 'Boyalı Kelimeler',
            'url' => url('/'),
            'description' => 'Sosyal çöküntüye sanatsal direniş. Kelimelerin boyandığı, fırçaların konuştuğu bir sanat hareketi.',
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Boyalı Kelimeler',
                'url' => url('/'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('images/logo.svg'),
                ],
            ],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => route('search.index') . '?q={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ],
        [
            '@type' => 'Organization',
            'name' => 'Boyalı Kelimeler',
            'url' => url('/'),
            'logo' => asset('images/logo.svg'),
            'sameAs' => [],
        ],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush

@section('content')

    <!-- ===================================================
         HERO SECTION
    =================================================== -->
    <section class="hero" aria-label="Hero bölümü">
        <div class="hero__overlay"></div>
        <div class="hero__pattern"></div>
        <div class="container position-relative">
            <div class="animate-fadein">
                <h1 class="hero__title">{{ $hero['hero_title'] ?? 'Boyalı Kelimeler' }}</h1>
            </div>
            <div class="animate-fadein animate-fadein--delay-1">
                <p class="hero__subtitle">{{ $hero['hero_subtitle'] ?? 'Sosyal Çöküntüye Sanatsal Direniş' }}</p>
            </div>
            <div class="hero__divider animate-fadein animate-fadein--delay-2"></div>
            <div class="animate-fadein animate-fadein--delay-3">
                <p class="hero__tagline">{{ $hero['hero_tagline'] ?? '— Bir Sanat Hareketi —' }}</p>
            </div>
            <div class="animate-fadein animate-fadein--delay-4">
                <p class="hero__description mt-3">
                    {{ $hero['hero_description'] ?? 'Kelimelerin boyandığı, fırçaların konuştuğu, sanatın direniş olduğu bir platform. 2026\'nın en cesur edebiyat ve sanat hareketi burada başlıyor.' }}
                </p>
            </div>
        </div>
    </section>

    <!-- ===================================================
         TEAM & POPULAR SECTION
    =================================================== -->
    <section class="section section--dark" aria-label="Ekip ve popüler içerikler">
        <div class="container">
            <div class="row g-4">
                <!-- Left: Team Cards + Son Paylaşımlar -->
                <div class="col-lg-8">
                    <div class="content-block">
                        <h2 class="section__title" data-aos="fade-down" data-aos-duration="600">Şair Erdem ve Yoldaşları</h2>
                        <div class="section__divider"></div>
                        <div class="row g-3">
                            <div class="col-md-4" data-aos="fade-up" data-aos-delay="0">
                                <div class="team-card">
                                    <div class="team-card__icon">
                                        <i class="fa-solid fa-users"></i>
                                    </div>
                                    <h3 class="team-card__title">Yönetim Ekibi</h3>
                                    <p class="team-card__text">Sanatın yolunu aydınlatan isimler</p>
                                    <a href="#" class="team-card__link">Keşfet <i class="fa-solid fa-arrow-right ms-1"></i></a>
                                </div>
                            </div>
                            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                                <div class="team-card">
                                    <div class="team-card__icon">
                                        <i class="fa-solid fa-pen-fancy"></i>
                                    </div>
                                    <h3 class="team-card__title">Yazarlarımız</h3>
                                    <p class="team-card__text">Kelimelere hayat veren kalemler</p>
                                    <a href="#" class="team-card__link">Keşfet <i class="fa-solid fa-arrow-right ms-1"></i></a>
                                </div>
                            </div>
                            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                                <div class="team-card">
                                    <div class="team-card__icon">
                                        <i class="fa-solid fa-palette"></i>
                                    </div>
                                    <h3 class="team-card__title">Ressamlarımız</h3>
                                    <p class="team-card__text">Tuvale ruh üfleyen sanatçılar</p>
                                    <a href="#" class="team-card__link">Keşfet <i class="fa-solid fa-arrow-right ms-1"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Son Paylaşımlar (Tabs) -->
                    <div class="content-block">
                        <h2 class="section__title" data-aos="fade-down" data-aos-duration="600">Son Paylaşımlar</h2>
                        <div class="section__divider"></div>

                        <!-- Tabs -->
                        <div class="tabs-bk" role="tablist" aria-label="İçerik kategorileri" data-aos="fade-up" data-aos-duration="600">
                            <button class="tabs-bk__item tabs-bk__item--active"
                                    role="tab" aria-selected="true"
                                    data-tab-target="tab-yazilar">Yazılar</button>
                            <button class="tabs-bk__item"
                                    role="tab" aria-selected="false"
                                    data-tab-target="tab-resimler">Resimler</button>
                            <button class="tabs-bk__item"
                                    role="tab" aria-selected="false"
                                    data-tab-target="tab-sorucevap">Soru Cevap</button>
                            <button class="tabs-bk__item"
                                    role="tab" aria-selected="false"
                                    data-tab-target="tab-sanatokulu">Sanat Okulu</button>
                        </div>

                        <!-- Tab: Yazılar -->
                        <div class="tabs-bk__panel tabs-bk__panel--active" id="tab-yazilar" role="tabpanel">
                            <div class="row g-3">
                                @forelse($latestWrittenWorks as $work)
                                    <div class="col-md-4">
                                        <a href="{{ route('literary-works.show', $work->slug) }}" class="card-bk card-bk--link">
                                            <div class="card-bk__body">
                                                <span class="popular-list__category popular-list__category--siir mb-2 d-inline-block">{{ $work->category?->name ?? 'Genel' }}</span>
                                                <h4 class="card-bk__title">{{ $work->title }}</h4>
                                                <p class="card-bk__text">{{ Str::limit(strip_tags($work->excerpt ?? $work->body), 80) }}</p>
                                            </div>
                                            <div class="card-bk__footer">
                                                <span class="card-bk__meta"><i class="fa-regular fa-user me-1"></i>{{ $work->author?->name ?? 'Anonim' }}</span>
                                                <span class="card-bk__meta"><i class="fa-regular fa-eye me-1"></i>{{ $work->view_count }}</span>
                                            </div>
                                        </a>
                                    </div>
                                @empty
                                    <div class="col-md-4">
                                        <div class="card-bk">
                                            <div class="card-bk__body">
                                                <span class="popular-list__category popular-list__category--siir mb-2 d-inline-block">Şiir</span>
                                                <h4 class="card-bk__title">Bir Avuç Gece</h4>
                                                <p class="card-bk__text">Karanlığın en koyu yerinde bile bir ışık vardır, yeter ki aramayı bil...</p>
                                            </div>
                                            <div class="card-bk__footer">
                                                <span class="card-bk__meta"><i class="fa-regular fa-user me-1"></i>Erdem Yıldız</span>
                                                <span class="card-bk__meta"><i class="fa-regular fa-eye me-1"></i>342</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card-bk">
                                            <div class="card-bk__body">
                                                <span class="popular-list__category popular-list__category--hikaye mb-2 d-inline-block">Hikaye</span>
                                                <h4 class="card-bk__title">Sessiz Çığlık</h4>
                                                <p class="card-bk__text">O gün nehir kenarında oturmuş, geçmişin sesini dinliyordu. Rüzgar...</p>
                                            </div>
                                            <div class="card-bk__footer">
                                                <span class="card-bk__meta"><i class="fa-regular fa-user me-1"></i>Ayşe Kara</span>
                                                <span class="card-bk__meta"><i class="fa-regular fa-eye me-1"></i>218</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card-bk">
                                            <div class="card-bk__body">
                                                <span class="popular-list__category popular-list__category--deneme mb-2 d-inline-block">Deneme</span>
                                                <h4 class="card-bk__title">Zamanın Kıyısında</h4>
                                                <p class="card-bk__text">Modern çağın en büyük yanılsaması, zamanın bize ait olduğunu sanm...</p>
                                            </div>
                                            <div class="card-bk__footer">
                                                <span class="card-bk__meta"><i class="fa-regular fa-user me-1"></i>Mehmet Demir</span>
                                                <span class="card-bk__meta"><i class="fa-regular fa-eye me-1"></i>189</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Tab: Resimler -->
                        <div class="tabs-bk__panel" id="tab-resimler" role="tabpanel">
                            <div class="row g-3">
                                @forelse($latestVisualWorks as $work)
                                    <div class="col-md-4">
                                        <a href="{{ route('literary-works.show', $work->slug) }}" class="card-bk card-bk--link">
                                            <div class="card-bk__body text-center py-5">
                                                <i class="fa-solid fa-paintbrush fa-3x text-gold mb-3 d-block"></i>
                                                <h4 class="card-bk__title">{{ $work->title }}</h4>
                                                <p class="card-bk__text">{{ Str::limit(strip_tags($work->excerpt ?? $work->body), 80) }}</p>
                                            </div>
                                            <div class="card-bk__footer">
                                                <span class="card-bk__meta"><i class="fa-regular fa-user me-1"></i>{{ $work->author?->name ?? 'Anonim' }}</span>
                                                <span class="card-bk__meta"><i class="fa-regular fa-heart me-1"></i>{{ $work->view_count }}</span>
                                            </div>
                                        </a>
                                    </div>
                                @empty
                                    <div class="col-md-4">
                                        <div class="card-bk">
                                            <div class="card-bk__body text-center py-5">
                                                <i class="fa-solid fa-paintbrush fa-3x text-gold mb-3 d-block"></i>
                                                <h4 class="card-bk__title">Mavi Hüzün</h4>
                                                <p class="card-bk__text">Yağlıboya — 60×80 cm</p>
                                            </div>
                                            <div class="card-bk__footer">
                                                <span class="card-bk__meta"><i class="fa-regular fa-user me-1"></i>Zeynep Ateş</span>
                                                <span class="card-bk__meta"><i class="fa-regular fa-heart me-1"></i>127</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card-bk">
                                            <div class="card-bk__body text-center py-5">
                                                <i class="fa-solid fa-paintbrush fa-3x text-gold mb-3 d-block"></i>
                                                <h4 class="card-bk__title">Sonbahar Rüyası</h4>
                                                <p class="card-bk__text">Akrilik — 50×70 cm</p>
                                            </div>
                                            <div class="card-bk__footer">
                                                <span class="card-bk__meta"><i class="fa-regular fa-user me-1"></i>Ali Fırtına</span>
                                                <span class="card-bk__meta"><i class="fa-regular fa-heart me-1"></i>98</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card-bk">
                                            <div class="card-bk__body text-center py-5">
                                                <i class="fa-solid fa-paintbrush fa-3x text-gold mb-3 d-block"></i>
                                                <h4 class="card-bk__title">Işığın Dansı</h4>
                                                <p class="card-bk__text">Suluboya — 40×50 cm</p>
                                            </div>
                                            <div class="card-bk__footer">
                                                <span class="card-bk__meta"><i class="fa-regular fa-user me-1"></i>Deniz Çelik</span>
                                                <span class="card-bk__meta"><i class="fa-regular fa-heart me-1"></i>85</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Tab: Soru Cevap -->
                        <div class="tabs-bk__panel" id="tab-sorucevap" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="card-bk">
                                        <div class="card-bk__body">
                                            <i class="fa-solid fa-comments text-gold mb-2 d-block"></i>
                                            <h4 class="card-bk__title">Şiir nasıl yazılır?</h4>
                                            <p class="card-bk__text">İlk şiirimi yazacağım ama nereden başlayacağımı bilmiyorum...</p>
                                        </div>
                                        <div class="card-bk__footer">
                                            <span class="card-bk__meta"><i class="fa-regular fa-message me-1"></i>12 cevap</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card-bk">
                                        <div class="card-bk__body">
                                            <i class="fa-solid fa-comments text-gold mb-2 d-block"></i>
                                            <h4 class="card-bk__title">En iyi edebiyat kitapları?</h4>
                                            <p class="card-bk__text">2026 yılında mutlaka okunması gereken kitaplar hangileri?</p>
                                        </div>
                                        <div class="card-bk__footer">
                                            <span class="card-bk__meta"><i class="fa-regular fa-message me-1"></i>24 cevap</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card-bk">
                                        <div class="card-bk__body">
                                            <i class="fa-solid fa-comments text-gold mb-2 d-block"></i>
                                            <h4 class="card-bk__title">Sanatçı bloku nasıl aşılır?</h4>
                                            <p class="card-bk__text">Uzun süredir yazamıyorum, yaratıcılığım tıkandı. Önerileriniz...</p>
                                        </div>
                                        <div class="card-bk__footer">
                                            <span class="card-bk__meta"><i class="fa-regular fa-message me-1"></i>18 cevap</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab: Sanat Okulu -->
                        <div class="tabs-bk__panel" id="tab-sanatokulu" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="card-bk">
                                        <div class="card-bk__body text-center py-4">
                                            <i class="fa-solid fa-play-circle fa-3x text-gold mb-2 d-block"></i>
                                            <h4 class="card-bk__title">Şiirde Ölçü ve Kafiye</h4>
                                            <p class="card-bk__text">Temel aruz ölçüsü dersi</p>
                                        </div>
                                        <div class="card-bk__footer">
                                            <span class="card-bk__meta"><i class="fa-regular fa-clock me-1"></i>25 dk</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card-bk">
                                        <div class="card-bk__body text-center py-4">
                                            <i class="fa-solid fa-play-circle fa-3x text-gold mb-2 d-block"></i>
                                            <h4 class="card-bk__title">Hikaye Yazma Teknikleri</h4>
                                            <p class="card-bk__text">Karakter geliştirme yöntemleri</p>
                                        </div>
                                        <div class="card-bk__footer">
                                            <span class="card-bk__meta"><i class="fa-regular fa-clock me-1"></i>32 dk</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card-bk">
                                        <div class="card-bk__body text-center py-4">
                                            <i class="fa-solid fa-play-circle fa-3x text-gold mb-2 d-block"></i>
                                            <h4 class="card-bk__title">Yağlıboya Başlangıç</h4>
                                            <p class="card-bk__text">Temel boya teknikleri</p>
                                        </div>
                                        <div class="card-bk__footer">
                                            <span class="card-bk__meta"><i class="fa-regular fa-clock me-1"></i>40 dk</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Popular List + Reklam + Dergi İletişim -->
                <div class="col-lg-4">
                    <div class="popular-list" data-aos="fade-left" data-aos-duration="600">
                        <h3 class="popular-list__title">
                            <i class="fa-solid fa-fire me-2"></i>En Çok Okunanlar
                        </h3>
                        @forelse($popularWorks as $index => $popular)
                            <a href="{{ route('literary-works.show', $popular->slug) }}" class="popular-list__item">
                                <span class="popular-list__rank">{{ $index + 1 }}</span>
                                <div class="popular-list__content">
                                    <span class="popular-list__name">{{ $popular->title }}</span>
                                    <span class="popular-list__category popular-list__category--siir">{{ $popular->category?->name ?? 'Genel' }}</span>
                                </div>
                            </a>
                        @empty
                            <a href="#" class="popular-list__item">
                                <span class="popular-list__rank">1</span>
                                <div class="popular-list__content">
                                    <span class="popular-list__name">Gecenin Şiiri</span>
                                    <span class="popular-list__category popular-list__category--siir">Şiir</span>
                                </div>
                            </a>
                            <a href="#" class="popular-list__item">
                                <span class="popular-list__rank">2</span>
                                <div class="popular-list__content">
                                    <span class="popular-list__name">Kayıp Rüzgarlar</span>
                                    <span class="popular-list__category popular-list__category--hikaye">Hikaye</span>
                                </div>
                            </a>
                            <a href="#" class="popular-list__item">
                                <span class="popular-list__rank">3</span>
                                <div class="popular-list__content">
                                    <span class="popular-list__name">Sanatın Anlamı Üzerine</span>
                                    <span class="popular-list__category popular-list__category--deneme">Deneme</span>
                                </div>
                            </a>
                            <a href="#" class="popular-list__item">
                                <span class="popular-list__rank">4</span>
                                <div class="popular-list__content">
                                    <span class="popular-list__name">Mavi Hüzün Tablosu</span>
                                    <span class="popular-list__category popular-list__category--resim">Resim</span>
                                </div>
                            </a>
                        @endforelse
                    </div>

                    <!-- Reklam Alanı -->
                    <div class="ad-banner ad-banner--sidebar mt-3">
                        <p class="ad-banner__text mb-0"><i class="fa-solid fa-bullhorn me-2"></i>Reklam Alanı</p>
                    </div>

                    <!-- Dergimizi Almak İçin İletişime Geç -->
                    <div class="cta-box cta-box--magazine mt-3" data-aos="fade-left" data-aos-delay="200">
                        <div class="cta-box__icon">
                            <i class="fa-solid fa-book-open"></i>
                        </div>
                        <h3 class="cta-box__title">Dergimizi Alın</h3>
                        <p class="cta-box__text">Boyalı Kelimeler dergisinin yeni sayısı çıktı! Dergiyi edinmek için bizimle iletişime geçin.</p>
                        <a href="{{ route('contact.show') }}" class="cta-box__btn">
                            <i class="fa-solid fa-envelope me-1"></i> İletişime Geç
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===================================================
         FULL-WIDTH PREMIUM SLIDER
    =================================================== -->
    <section class="hero-slider" aria-label="Tam genişlik slider">
        <div class="hero-slider__overlay"></div>
        @forelse($homeSliders as $index => $slide)
            <div class="hero-slider__slide {{ $index === 0 ? 'hero-slider__slide--active' : '' }}" data-slide="{{ $index }}">
                <div class="hero-slider__bg" role="img" aria-label="Slider arka plan"></div>
                <div class="hero-slider__content">
                    <span class="hero-slider__badge">
                        @if($slide->badge_icon)
                            <i class="{{ $slide->badge_icon }} me-1"></i>
                        @endif
                        {{ $slide->badge_text }}
                    </span>
                    <h2 class="hero-slider__title">{{ $slide->title }}</h2>
                    <div class="hero-slider__divider"></div>
                    <p class="hero-slider__text">{{ $slide->description }}</p>
                </div>
            </div>
        @empty
            <div class="hero-slider__slide hero-slider__slide--active" data-slide="0">
                <div class="hero-slider__bg" role="img" aria-label="Slider arka plan"></div>
                <div class="hero-slider__content">
                    <span class="hero-slider__badge"><i class="fa-solid fa-feather-pointed me-1"></i>Edebiyat</span>
                    <h2 class="hero-slider__title">Kelimelerin Gücü</h2>
                    <div class="hero-slider__divider"></div>
                    <p class="hero-slider__text">Bir kelime dünyayı değiştirebilir. Bir şiir ruhu dokunabilir. Bir hikaye hayatları dönüştürebilir.</p>
                </div>
            </div>
        @endforelse

        @if($homeSliders->count() > 1)
            <!-- Navigation Arrows -->
            <button class="hero-slider__arrow hero-slider__arrow--prev" aria-label="Önceki slayt" data-hero-prev>
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button class="hero-slider__arrow hero-slider__arrow--next" aria-label="Sonraki slayt" data-hero-next>
                <i class="fa-solid fa-chevron-right"></i>
            </button>

            <!-- Slider Dots -->
            <div class="hero-slider__dots">
                @foreach($homeSliders as $index => $slide)
                    <button class="hero-slider__dot {{ $index === 0 ? 'hero-slider__dot--active' : '' }}" data-hero-dot="{{ $index }}" aria-label="Slayt {{ $index + 1 }}"></button>
                @endforeach
            </div>

            <!-- Progress Bar -->
            <div class="hero-slider__progress">
                <div class="hero-slider__progress-bar"></div>
            </div>
        @endif
    </section>

    <!-- ===================================================
         MIDDLE SECTION — Categories + Film + Anket
    =================================================== -->
    <section class="section section--dark" aria-label="Kategoriler">
        <div class="container">
            <p class="section__slogan" data-aos="fade-down" data-aos-duration="800">"Sanat, ruhun gözle görülür halidir."</p>

            <div class="row g-4 align-items-start">
                <!-- Left: 4 Category Cards (2x2) -->
                <div class="col-lg-8">
                    <div class="row g-3">
                        <div class="col-6" data-aos="zoom-in" data-aos-delay="0">
                            <a href="#" class="text-decoration-none d-block">
                                <div class="category-card">
                                    <div class="category-card__icon">
                                        <i class="fa-solid fa-comments"></i>
                                    </div>
                                    <h3 class="category-card__title">Söz Meydanı</h3>
                                    <p class="category-card__text">Soru &amp; Cevap</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-6" data-aos="zoom-in" data-aos-delay="100">
                            <a href="#" class="text-decoration-none d-block">
                                <div class="category-card">
                                    <div class="category-card__icon">
                                        <i class="fa-solid fa-award"></i>
                                    </div>
                                    <h3 class="category-card__title">Altın Kalem</h3>
                                    <p class="category-card__text">Yazı Yarışması</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-6" data-aos="zoom-in" data-aos-delay="200">
                            <a href="#" class="text-decoration-none d-block">
                                <div class="category-card">
                                    <div class="category-card__icon">
                                        <i class="fa-solid fa-graduation-cap"></i>
                                    </div>
                                    <h3 class="category-card__title">Sanat Okulu</h3>
                                    <p class="category-card__text">Video Dersler</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-6" data-aos="zoom-in" data-aos-delay="300">
                            <a href="#" class="text-decoration-none d-block">
                                <div class="category-card">
                                    <div class="category-card__icon">
                                        <i class="fa-solid fa-star"></i>
                                    </div>
                                    <h3 class="category-card__title">Astroloji</h3>
                                    <p class="category-card__text">Burç Yorumları</p>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- İçeriklerimiz -->
                    <h3 class="section__block-title" data-aos="fade-up">
                        <i class="fa-solid fa-pen-fancy me-2"></i>İçeriklerimiz
                    </h3>
                    <div class="row g-3">
                        <div class="col-6 col-lg-3" data-aos="zoom-in" data-aos-delay="0">
                            <a href="{{ route('blog.index') }}" class="text-decoration-none d-block">
                                <div class="category-card">
                                    <div class="category-card__icon">
                                        <i class="fa-solid fa-palette"></i>
                                    </div>
                                    <h3 class="category-card__title">Sanat</h3>
                                    <p class="category-card__text">Görsel Sanatlar</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-lg-3" data-aos="zoom-in" data-aos-delay="100">
                            <a href="{{ route('blog.index') }}" class="text-decoration-none d-block">
                                <div class="category-card">
                                    <div class="category-card__icon">
                                        <i class="fa-solid fa-book-open"></i>
                                    </div>
                                    <h3 class="category-card__title">Edebiyat</h3>
                                    <p class="category-card__text">Yazın Dünyası</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-lg-3" data-aos="zoom-in" data-aos-delay="200">
                            <a href="{{ route('blog.index') }}" class="text-decoration-none d-block">
                                <div class="category-card">
                                    <div class="category-card__icon">
                                        <i class="fa-solid fa-masks-theater"></i>
                                    </div>
                                    <h3 class="category-card__title">Kültür</h3>
                                    <p class="category-card__text">Kültür-Sanat</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-lg-3" data-aos="zoom-in" data-aos-delay="300">
                            <a href="{{ route('blog.index') }}" class="text-decoration-none d-block">
                                <div class="category-card">
                                    <div class="category-card__icon">
                                        <i class="fa-solid fa-calendar-days"></i>
                                    </div>
                                    <h3 class="category-card__title">Etkinlik</h3>
                                    <p class="category-card__text">Etkinlikler</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="list-block list-block--auto mb-3" data-aos="fade-left" data-aos-duration="600">
                        <h3 class="list-block__title">
                            <i class="fa-solid fa-film me-2"></i>Haftanın Film Önerisi
                        </h3>
                        <a href="#" class="list-block__item">
                            <i class="fa-solid fa-clapperboard list-block__item-icon"></i>
                            <span>Paterson (2016) — Jim Jarmusch</span>
                        </a>
                        <a href="#" class="list-block__item">
                            <i class="fa-solid fa-clapperboard list-block__item-icon"></i>
                            <span>Dead Poets Society (1989)</span>
                        </a>
                        <a href="#" class="list-block__item">
                            <i class="fa-solid fa-clapperboard list-block__item-icon"></i>
                            <span>Midnight in Paris (2011)</span>
                        </a>
                        <a href="#" class="list-block__item">
                            <i class="fa-solid fa-clapperboard list-block__item-icon"></i>
                            <span>Bright Star (2009)</span>
                        </a>
                        <a href="#" class="list-block__item">
                            <i class="fa-solid fa-clapperboard list-block__item-icon"></i>
                            <span>Il Postino (1994)</span>
                        </a>
                    </div>

                    <div class="poll" data-aos="fade-left" data-aos-delay="200">
                        <h3 class="poll__title"><i class="fa-solid fa-chart-bar me-2"></i>Anket</h3>
                        <p class="poll__question">En çok hangi edebiyat türünü seviyorsunuz?</p>
                        <button class="poll__option" type="button">Şiir</button>
                        <button class="poll__option" type="button">Hikaye</button>
                        <button class="poll__option" type="button">Roman</button>
                        <button class="poll__option" type="button">Deneme</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===================================================
         BOTTOM SECTION — Altın Fırça, Hakkımızda, Dergi, Reklam + Video + Quote + Söz
    =================================================== -->
    <section class="section section--deeper" aria-label="Alt bölüm">
        <div class="container">
            <p class="section__slogan" data-aos="fade-down" data-aos-duration="800">"Kelimeler boyanır, renkler konuşur."</p>

            <div class="row g-4 align-items-stretch">
                <!-- Left col-lg-8 -->
                <div class="col-lg-8">
                    <!-- Row 1: Creative 3-column grid -->
                    <div class="creative-grid mb-3" data-aos="fade-up" data-aos-duration="700">
                        <!-- Altın Fırça — tall left -->
                        <a href="#" class="text-decoration-none d-block creative-grid__tall creative-grid__tall--left">
                            <div class="category-card category-card--tall">
                                <div class="category-card__icon">
                                    <i class="fa-solid fa-paintbrush"></i>
                                </div>
                                <h3 class="category-card__title">Altın Fırça</h3>
                                <p class="category-card__text">Resim Yarışması</p>
                            </div>
                        </a>

                        <!-- Hakkımızda — top middle -->
                        <a href="#" class="text-decoration-none d-block creative-grid__top">
                            <div class="category-card">
                                <div class="category-card__icon">
                                    <i class="fa-solid fa-circle-info"></i>
                                </div>
                                <h3 class="category-card__title">Hakkımızda</h3>
                                <p class="category-card__text">Bizi Tanıyın</p>
                            </div>
                        </a>

                        <!-- Değerlerimiz — bottom middle -->
                        <a href="#" class="text-decoration-none d-block creative-grid__bottom">
                            <div class="category-card">
                                <div class="category-card__icon">
                                    <i class="fa-solid fa-gem"></i>
                                </div>
                                <h3 class="category-card__title">Değerlerimiz</h3>
                                <p class="category-card__text">İlkelerimiz</p>
                            </div>
                        </a>

                        <!-- Reklam — tall right -->
                        <div class="creative-grid__tall creative-grid__tall--right">
                            <div class="ad-banner ad-banner--tall">
                                <p class="ad-banner__text mb-0">
                                    <i class="fa-solid fa-bullhorn me-2"></i>Reklam Alanı
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: YouTube Kanal Videoları -->
                    <div class="video-gallery" data-aos="fade-up" data-aos-delay="100">
                        <div class="video-gallery__header">
                            <h3 class="video-gallery__title">
                                <i class="fa-brands fa-youtube me-2"></i>Kanal Videoları
                            </h3>
                            <div class="video-gallery__nav">
                                <button class="video-gallery__nav-btn video-gallery__nav-btn--prev" type="button" aria-label="Önceki videolar">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </button>
                                <button class="video-gallery__nav-btn video-gallery__nav-btn--next" type="button" aria-label="Sonraki videolar">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                        <div class="swiper video-gallery__swiper">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <a href="#" class="video-gallery__item" data-video-id="QNnvNzWAdy4">
                                        <div class="video-gallery__thumb">
                                            <img src="https://img.youtube.com/vi/QNnvNzWAdy4/mqdefault.jpg"
                                                 alt="Beden Bir Sanat Eseri midir?"
                                                 class="video-gallery__img" loading="lazy">
                                            <div class="video-gallery__play">
                                                <i class="fa-solid fa-play"></i>
                                            </div>
                                        </div>
                                        <p class="video-gallery__name">Beden Bir Sanat Eseri midir?</p>
                                    </a>
                                </div>
                                <div class="swiper-slide">
                                    <a href="#" class="video-gallery__item" data-video-id="L_urE5Dvf7Q">
                                        <div class="video-gallery__thumb">
                                            <img src="https://img.youtube.com/vi/L_urE5Dvf7Q/mqdefault.jpg"
                                                 alt="Pratik Çizimler: Kolay Ağaçlar ve Yengeç"
                                                 class="video-gallery__img" loading="lazy">
                                            <div class="video-gallery__play">
                                                <i class="fa-solid fa-play"></i>
                                            </div>
                                        </div>
                                        <p class="video-gallery__name">Pratik Çizimler: Kolay Ağaçlar</p>
                                    </a>
                                </div>
                                <div class="swiper-slide">
                                    <a href="#" class="video-gallery__item" data-video-id="_e95zQvYVGU">
                                        <div class="video-gallery__thumb">
                                            <img src="https://img.youtube.com/vi/_e95zQvYVGU/mqdefault.jpg"
                                                 alt="Boyalı Kelimeler Şiir Gecesi"
                                                 class="video-gallery__img" loading="lazy">
                                            <div class="video-gallery__play">
                                                <i class="fa-solid fa-play"></i>
                                            </div>
                                        </div>
                                        <p class="video-gallery__name">Şiir Gecesi</p>
                                    </a>
                                </div>
                                <div class="swiper-slide">
                                    <a href="#" class="video-gallery__item" data-video-id="H80BOUoNzP8">
                                        <div class="video-gallery__thumb">
                                            <img src="https://img.youtube.com/vi/H80BOUoNzP8/mqdefault.jpg"
                                                 alt="Sessiz Çöküş: Toplum Nereye Gidiyor?"
                                                 class="video-gallery__img" loading="lazy">
                                            <div class="video-gallery__play">
                                                <i class="fa-solid fa-play"></i>
                                            </div>
                                        </div>
                                        <p class="video-gallery__name">Sessiz Çöküş: Toplum Nereye?</p>
                                    </a>
                                </div>
                                <div class="swiper-slide">
                                    <a href="#" class="video-gallery__item" data-video-id="mxodbZBE5qk">
                                        <div class="video-gallery__thumb">
                                            <img src="https://img.youtube.com/vi/mxodbZBE5qk/mqdefault.jpg"
                                                 alt="Edebiyat Öğretmeni ile Sanatın İzinde"
                                                 class="video-gallery__img" loading="lazy">
                                            <div class="video-gallery__play">
                                                <i class="fa-solid fa-play"></i>
                                            </div>
                                        </div>
                                        <p class="video-gallery__name">Sanatın İzinde</p>
                                    </a>
                                </div>
                                <div class="swiper-slide">
                                    <a href="#" class="video-gallery__item" data-video-id="dQw4w9WgXcQ">
                                        <div class="video-gallery__thumb">
                                            <img src="https://img.youtube.com/vi/dQw4w9WgXcQ/mqdefault.jpg"
                                                 alt="Sanat ve Hayat Üzerine"
                                                 class="video-gallery__img" loading="lazy">
                                            <div class="video-gallery__play">
                                                <i class="fa-solid fa-play"></i>
                                            </div>
                                        </div>
                                        <p class="video-gallery__name">Sanat ve Hayat Üzerine</p>
                                    </a>
                                </div>
                                <div class="swiper-slide">
                                    <a href="#" class="video-gallery__item" data-video-id="9bZkp7q19f0">
                                        <div class="video-gallery__thumb">
                                            <img src="https://img.youtube.com/vi/9bZkp7q19f0/mqdefault.jpg"
                                                 alt="Kalem ve Tuval Buluşması"
                                                 class="video-gallery__img" loading="lazy">
                                            <div class="video-gallery__play">
                                                <i class="fa-solid fa-play"></i>
                                            </div>
                                        </div>
                                        <p class="video-gallery__name">Kalem ve Tuval Buluşması</p>
                                    </a>
                                </div>
                                <div class="swiper-slide">
                                    <a href="#" class="video-gallery__item" data-video-id="kJQP7kiw5Fk">
                                        <div class="video-gallery__thumb">
                                            <img src="https://img.youtube.com/vi/kJQP7kiw5Fk/mqdefault.jpg"
                                                 alt="Edebiyat Sohbetleri"
                                                 class="video-gallery__img" loading="lazy">
                                            <div class="video-gallery__play">
                                                <i class="fa-solid fa-play"></i>
                                            </div>
                                        </div>
                                        <p class="video-gallery__name">Edebiyat Sohbetleri</p>
                                    </a>
                                </div>
                                <div class="swiper-slide">
                                    <a href="#" class="video-gallery__item" data-video-id="RgKAFK5djSk">
                                        <div class="video-gallery__thumb">
                                            <img src="https://img.youtube.com/vi/RgKAFK5djSk/mqdefault.jpg"
                                                 alt="Şiirin Güçlü Sesi"
                                                 class="video-gallery__img" loading="lazy">
                                            <div class="video-gallery__play">
                                                <i class="fa-solid fa-play"></i>
                                            </div>
                                        </div>
                                        <p class="video-gallery__name">Şiirin Güçlü Sesi</p>
                                    </a>
                                </div>
                                <div class="swiper-slide">
                                    <a href="#" class="video-gallery__item" data-video-id="JGwWNGJdvx8">
                                        <div class="video-gallery__thumb">
                                            <img src="https://img.youtube.com/vi/JGwWNGJdvx8/mqdefault.jpg"
                                                 alt="Görsel Sanatlara Giriş"
                                                 class="video-gallery__img" loading="lazy">
                                            <div class="video-gallery__play">
                                                <i class="fa-solid fa-play"></i>
                                            </div>
                                        </div>
                                        <p class="video-gallery__name">Görsel Sanatlara Giriş</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Video Modal -->
                    <div class="video-modal" id="videoModal">
                        <div class="video-modal__overlay"></div>
                        <div class="video-modal__content">
                            <button class="video-modal__close" type="button" aria-label="Kapat">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                            <div class="video-modal__wrapper">
                                <iframe class="video-modal__iframe" id="videoIframe"
                                        src="" frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right col-lg-4 -->
                <div class="col-lg-4 d-flex flex-column">
                    <!-- Günün Sözü + Yorum Gönder -->
                    <div class="quote-comment mb-3" data-aos="fade-left" data-aos-duration="600">
                        <div class="quote-comment__header">
                            <i class="fa-solid fa-quote-left quote-comment__icon"></i>
                            <h3 class="quote-comment__heading">Günün Sözü</h3>
                        </div>
                        <blockquote class="quote-comment__text">
                            "Şiir, dünyanın en güzel yalanı ve en acı gerçeğidir."
                        </blockquote>
                        <span class="quote-comment__author">
                            <i class="fa-solid fa-feather-pointed me-1"></i>Boyalı Kelimeler
                        </span>

                        <div class="quote-comment__divider"></div>

                        <p class="quote-comment__label">Bu söz hakkında ne düşünüyorsunuz?</p>
                        <textarea class="quote-comment__textarea" rows="3" placeholder="Yorumunuzu buraya yazın..."></textarea>
                        <button class="quote-comment__btn" type="button">
                            <i class="fa-solid fa-paper-plane me-1"></i> Gönder
                        </button>
                    </div>

                    <!-- 2 Küçük CTA Kutusu -->
                    <div class="row g-3 mt-auto">
                        <div class="col-6" data-aos="zoom-in" data-aos-delay="0">
                            <a href="{{ route('register') }}" class="text-decoration-none d-block">
                                <div class="mini-cta">
                                    <div class="mini-cta__icon">
                                        <i class="fa-solid fa-user-plus"></i>
                                    </div>
                                    <h4 class="mini-cta__title">Üye Ol</h4>
                                    <p class="mini-cta__text">Topluluğa katıl</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-6" data-aos="zoom-in" data-aos-delay="150">
                            <a href="{{ route('myposts.create') }}" class="text-decoration-none d-block">
                                <div class="mini-cta">
                                    <div class="mini-cta__icon">
                                        <i class="fa-solid fa-pen-nib"></i>
                                    </div>
                                    <h4 class="mini-cta__title">Yazmaya Başla</h4>
                                    <p class="mini-cta__text">İlk yazını paylaş</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
