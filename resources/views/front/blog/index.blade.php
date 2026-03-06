@extends('layouts.front')

@section('title', 'Blog — Boyalı Kelimeler')
@section('meta_description', 'Boyalı Kelimeler blog yazıları. Sanat, edebiyat, kültür ve etkinlik haberleri.')
@section('canonical', route('blog.index'))
@section('og_title', 'Blog — Boyalı Kelimeler')
@section('og_description', 'Sanat, edebiyat, kültür ve etkinlik haberleri.')

@if(request('kategori'))
    @section('robots', 'noindex, follow')
@endif

@push('seo_links')
    @if($posts->previousPageUrl())
        <link rel="prev" href="{{ $posts->previousPageUrl() }}">
    @endif
    @if($posts->nextPageUrl())
        <link rel="next" href="{{ $posts->nextPageUrl() }}">
    @endif
@endpush

@push('jsonld')
<script type="application/ld+json">
{!! json_encode([
    '@@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'CollectionPage',
            'name' => 'Blog',
            'description' => 'Boyalı Kelimeler blog yazıları. Sanat, edebiyat, kültür ve etkinlik haberleri.',
            'url' => route('blog.index'),
            'isPartOf' => [
                '@type' => 'WebSite',
                'name' => 'Boyalı Kelimeler',
                'url' => url('/'),
            ],
            'mainEntity' => [
                '@type' => 'ItemList',
                'numberOfItems' => $posts->total(),
                'itemListElement' => $posts->map(fn ($p, $i) => [
                    '@type' => 'ListItem',
                    'position' => ($posts->currentPage() - 1) * $posts->perPage() + $i + 1,
                    'url' => route('blog.show', $p->slug),
                    'name' => $p->title,
                ])->all(),
            ],
        ],
        [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Ana Sayfa', 'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog'],
            ],
        ],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush

@section('content')

    <!-- Page Header -->
    <section class="blog-page-header" aria-label="Blog sayfa başlığı">
        <div class="container">
            <!-- Breadcrumb -->
            <nav class="blog-page-header__breadcrumb" aria-label="Breadcrumb">
                <ol class="blog-page-header__breadcrumb-list">
                    <li class="blog-page-header__breadcrumb-item">
                        <a href="{{ url('/') }}" class="blog-page-header__breadcrumb-link">
                            <i class="fa-solid fa-house"></i>
                        </a>
                    </li>
                    <li class="blog-page-header__breadcrumb-sep">
                        <i class="fa-solid fa-chevron-right"></i>
                    </li>
                    <li class="blog-page-header__breadcrumb-item blog-page-header__breadcrumb-item--active" aria-current="page">
                        Blog
                    </li>
                </ol>
            </nav>
            <!-- Title -->
            <div class="blog-page-header__inner">
                <h1 class="blog-page-header__title">
                    <i class="fa-solid fa-blog me-3"></i>Blog
                </h1>
                <p class="blog-page-header__desc">
                    Sanat dünyasından haberler, edebiyat dünyasından söyleşiler, kültür-sanat etkinlikleri ve daha fazlası.
                </p>
                <div class="blog-page-header__stats">
                    <span class="blog-page-header__stat">
                        <i class="fa-solid fa-file-lines me-1"></i>{{ $stats['total_posts'] }} Yazı
                    </span>
                    <span class="blog-page-header__stat-sep">|</span>
                    <span class="blog-page-header__stat">
                        <i class="fa-solid fa-folder-open me-1"></i>{{ $stats['total_categories'] }} Kategori
                    </span>
                    <span class="blog-page-header__stat-sep">|</span>
                    <span class="blog-page-header__stat">
                        <i class="fa-solid fa-eye me-1"></i>{{ number_format($stats['total_views']) }} Okunma
                    </span>
                </div>
            </div>
        </div>
    </section>

    @if($featuredPosts->isNotEmpty())
    <!-- HERO — Öne Çıkan Yazı (Overlay) -->
    <section class="blog-hero" aria-label="Öne çıkan blog yazısı">
        <div class="blog-hero__bg">
            @if($featuredPosts->first()->cover_image)
                <img src="{{ asset('uploads/' . $featuredPosts->first()->cover_image) }}"
                     alt="{{ $featuredPosts->first()->title }}"
                     class="blog-hero__bg-img"
                     loading="lazy">
            @else
                <div class="blog-hero__bg-placeholder">
                    <i class="fa-solid fa-palette"></i>
                </div>
            @endif
            <div class="blog-hero__overlay"></div>
        </div>
        <div class="container">
            <div class="blog-hero__content">
                <span class="blog-badge blog-badge--sanat blog-hero__badge">
                    <i class="fa-solid fa-star me-1"></i>Öne Çıkan
                </span>
                <h1 class="blog-hero__title">
                    <a href="{{ route('blog.show', $featuredPosts->first()->slug) }}">{{ $featuredPosts->first()->title }}</a>
                </h1>
                @if($featuredPosts->first()->excerpt)
                    <p class="blog-hero__excerpt">
                        {{ $featuredPosts->first()->excerpt }}
                    </p>
                @endif
                <div class="blog-hero__meta">
                    @if($featuredPosts->first()->published_at)
                        <time datetime="{{ $featuredPosts->first()->published_at->toDateString() }}">
                            <i class="fa-regular fa-calendar me-1"></i>{{ $featuredPosts->first()->published_at->translatedFormat('d F Y') }}
                        </time>
                    @endif
                    <span>
                        <i class="fa-regular fa-clock me-1"></i>{{ $featuredPosts->first()->readingTime() }} dk okuma
                    </span>
                    <span>
                        <i class="fa-solid fa-eye me-1"></i>{{ number_format($featuredPosts->first()->view_count) }}
                    </span>
                </div>
                <a href="{{ route('blog.show', $featuredPosts->first()->slug) }}" class="blog-hero__read-btn">
                    <i class="fa-solid fa-arrow-right me-2"></i>Yazıyı Oku
                </a>
            </div>
        </div>
    </section>

    <!-- SON 3 HABER — Yatay Küçük Kartlar -->
    @if($featuredPosts->count() > 1)
    <section class="blog-highlights" aria-label="Son haberler">
        <div class="container">
            <div class="blog-highlights__grid">
                @foreach($featuredPosts->skip(1)->take(3) as $featured)
                    <a href="{{ route('blog.show', $featured->slug) }}" class="blog-highlight">
                        <div class="blog-highlight__icon">
                            <i class="fa-solid fa-newspaper"></i>
                        </div>
                        <div class="blog-highlight__body">
                            @if($featured->category)
                                <span class="blog-highlight__cat">{{ $featured->category->name }}</span>
                            @endif
                            <h3 class="blog-highlight__title">{{ $featured->title }}</h3>
                            @if($featured->published_at)
                                <span class="blog-highlight__date">
                                    <i class="fa-regular fa-calendar me-1"></i>{{ $featured->published_at->translatedFormat('d M Y') }}
                                </span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif
    @endif

    <!-- BLOG İÇERİK — Toolbar + Grid + Sidebar -->
    <section class="blog-section" aria-label="Blog yazıları">
        <div class="container">
            <div class="row g-4">

                <!-- SOL KOLON — ANA İÇERİK -->
                <div class="col-lg-8">

                    <!-- Toolbar: Filter -->
                    <form method="GET" action="{{ route('blog.index') }}" class="blog-toolbar">
                        <div class="blog-toolbar__filters">
                            <select name="kategori" class="wpost-form__input blog-toolbar__select" onchange="this.form.submit()">
                                <option value="">Tüm Kategoriler</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->slug }}" {{ $currentCategory === $cat->slug ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>

                    <!-- Sonuç Bilgisi -->
                    <div class="blog-result-info">
                        <span class="blog-result-info__text">
                            <i class="fa-solid fa-file-lines me-1"></i>Toplam <strong>{{ $posts->total() }}</strong> yazı bulundu
                        </span>
                    </div>

                    <!-- Blog Cards Grid -->
                    <div class="blog-grid">
                        @forelse($posts as $post)
                            <article class="blog-card">
                                <a href="{{ route('blog.show', $post->slug) }}" class="blog-card__thumb-link">
                                    <div class="blog-card__thumb">
                                        @if($post->cover_image)
                                            <img src="{{ asset('uploads/' . $post->cover_image) }}"
                                                 alt="{{ $post->title }}"
                                                 class="blog-card__thumb-img"
                                                 loading="lazy">
                                        @else
                                            <div class="blog-card__thumb-placeholder">
                                                <i class="fa-solid fa-newspaper"></i>
                                            </div>
                                        @endif
                                        @if($post->category)
                                            <span class="blog-badge">
                                                <i class="fa-solid fa-folder me-1"></i>{{ $post->category->name }}
                                            </span>
                                        @endif
                                    </div>
                                </a>
                                <div class="blog-card__body">
                                    @if($post->published_at)
                                        <time class="blog-card__date" datetime="{{ $post->published_at->toDateString() }}">
                                            <i class="fa-regular fa-calendar me-1"></i>{{ $post->published_at->translatedFormat('d M Y') }}
                                        </time>
                                    @endif
                                    <h3 class="blog-card__title">
                                        <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                                    </h3>
                                    @if($post->excerpt)
                                        <p class="blog-card__excerpt">
                                            {{ Str::limit($post->excerpt, 120) }}
                                        </p>
                                    @endif
                                    <div class="blog-card__footer">
                                        <span class="blog-card__read-time">
                                            <i class="fa-regular fa-clock me-1"></i>{{ $post->readingTime() }} dk
                                        </span>
                                        <span class="blog-card__views">
                                            <i class="fa-solid fa-eye me-1"></i>{{ number_format($post->view_count) }}
                                        </span>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="blog-empty text-center py-5">
                                <i class="fa-solid fa-newspaper fa-3x mb-3 text-muted"></i>
                                <p class="text-muted">Henüz yayınlanmış yazı bulunmuyor.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if($posts->hasPages())
                        <nav class="blog-pagination" aria-label="Sayfalama">
                            @if($posts->onFirstPage())
                                <span class="blog-pagination__btn blog-pagination__btn--prev" aria-disabled="true">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </span>
                            @else
                                <a href="{{ $posts->previousPageUrl() }}" class="blog-pagination__btn blog-pagination__btn--prev" aria-label="Önceki sayfa">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </a>
                            @endif

                            @foreach($posts->getUrlRange(1, $posts->lastPage()) as $page => $url)
                                @if($page === $posts->currentPage())
                                    <span class="blog-pagination__page blog-pagination__page--active" aria-current="page">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="blog-pagination__page" aria-label="Sayfa {{ $page }}">{{ $page }}</a>
                                @endif
                            @endforeach

                            @if($posts->hasMorePages())
                                <a href="{{ $posts->nextPageUrl() }}" class="blog-pagination__btn blog-pagination__btn--next" aria-label="Sonraki sayfa">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </a>
                            @else
                                <span class="blog-pagination__btn blog-pagination__btn--next" aria-disabled="true">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </span>
                            @endif
                        </nav>
                    @endif

                </div>

                <!-- SAĞ KOLON — SIDEBAR -->
                <aside class="col-lg-4">
                    <div class="blog-sidebar">

                        <!-- Kategoriler -->
                        <div class="blog-sidebar__card">
                            <h4 class="blog-sidebar__title">
                                <i class="fa-solid fa-folder-open me-2"></i>Kategoriler
                            </h4>
                            <ul class="blog-sidebar__cat-list">
                                <li>
                                    <a href="{{ route('blog.index') }}" class="blog-sidebar__cat-link {{ !$currentCategory ? 'blog-sidebar__cat-link--active' : '' }}">
                                        <i class="fa-solid fa-layer-group me-2"></i>Tümü
                                        <span class="blog-sidebar__cat-count">{{ $posts->total() }}</span>
                                    </a>
                                </li>
                                @foreach($categories as $cat)
                                    <li>
                                        <a href="{{ route('blog.index', ['kategori' => $cat->slug]) }}"
                                           class="blog-sidebar__cat-link {{ $currentCategory === $cat->slug ? 'blog-sidebar__cat-link--active' : '' }}">
                                            <i class="fa-solid fa-folder me-2"></i>{{ $cat->name }}
                                            <span class="blog-sidebar__cat-count">{{ $cat->posts()->where('status', 'published')->count() }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Popüler Yazılar -->
                        @if($popularPosts->isNotEmpty())
                        <div class="blog-sidebar__card">
                            <h4 class="blog-sidebar__title">
                                <i class="fa-solid fa-fire me-2"></i>En Çok Okunanlar
                            </h4>
                            <div class="blog-sidebar__popular">
                                @foreach($popularPosts as $index => $popular)
                                    <a href="{{ route('blog.show', $popular->slug) }}" class="blog-sidebar__popular-item">
                                        <div class="blog-sidebar__popular-rank">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</div>
                                        <div class="blog-sidebar__popular-info">
                                            <h5 class="blog-sidebar__popular-title">{{ $popular->title }}</h5>
                                            <span class="blog-sidebar__popular-meta">
                                                <i class="fa-solid fa-eye me-1"></i>{{ number_format($popular->view_count) }}
                                            </span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Bülten -->
                        <div class="blog-sidebar__card blog-sidebar__card--newsletter">
                            <div class="blog-sidebar__newsletter-icon">
                                <i class="fa-solid fa-envelope-open-text"></i>
                            </div>
                            <h4 class="blog-sidebar__newsletter-title">Blog Bülteni</h4>
                            <p class="blog-sidebar__newsletter-desc">
                                Yeni yazılardan haberdar olun. Haftada bir, en iyi içerikler e-posta adresinize gelsin.
                            </p>
                            <div class="blog-sidebar__newsletter-form">
                                <input type="email" class="wpost-form__input" placeholder="E-posta adresiniz">
                                <button type="button" class="blog-sidebar__newsletter-btn">
                                    <i class="fa-solid fa-paper-plane me-1"></i>Abone Ol
                                </button>
                            </div>
                        </div>

                    </div>
                </aside>

            </div>
        </div>
    </section>

@endsection
