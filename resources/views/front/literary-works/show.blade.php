@extends('layouts.front')

@section('title', ($work->meta_title ?? $work->title) . ' — Boyalı Kelimeler')
@section('meta_description', $work->meta_description ?? Str::limit(strip_tags($work->body), 160))
@section('canonical', route('literary-works.show', $work->slug))
@section('og_title', ($work->meta_title ?? $work->title) . ' — Boyalı Kelimeler')
@section('og_description', $work->meta_description ?? Str::limit(strip_tags($work->body), 160))

@section('content')

    <!-- Breadcrumb -->
    <nav class="cdetail-breadcrumb" aria-label="Breadcrumb">
        <div class="container">
            <ol class="cdetail-breadcrumb__list">
                <li class="cdetail-breadcrumb__item">
                    <a href="{{ url('/') }}" class="cdetail-breadcrumb__link">
                        <i class="fa-solid fa-house"></i>
                    </a>
                </li>
                <li class="cdetail-breadcrumb__sep">
                    <i class="fa-solid fa-chevron-right"></i>
                </li>
                <li class="cdetail-breadcrumb__item">
                    <a href="{{ route('literary-works.index') }}" class="cdetail-breadcrumb__link">İçerikler</a>
                </li>
                <li class="cdetail-breadcrumb__sep">
                    <i class="fa-solid fa-chevron-right"></i>
                </li>
                <li class="cdetail-breadcrumb__item">
                    <a href="{{ route('literary-works.index', ['kategori' => $work->category->slug]) }}" class="cdetail-breadcrumb__link">{{ $work->category->name }}</a>
                </li>
                <li class="cdetail-breadcrumb__sep">
                    <i class="fa-solid fa-chevron-right"></i>
                </li>
                <li class="cdetail-breadcrumb__item cdetail-breadcrumb__item--active" aria-current="page">
                    {{ Str::limit($work->title, 50) }}
                </li>
            </ol>
        </div>
    </nav>

    <!-- Article Section -->
    <article class="cdetail-section">
        <div class="container">
            <div class="row g-4">

                <!-- =============================================
                     SOL KOLON — ANA İÇERİK
                ============================================== -->
                <div class="col-lg-8">

                    <!-- Article Header -->
                    <header class="cdetail-header">
                        <span class="clist-card__category clist-card__category--{{ $work->category->slug }} cdetail-header__category">
                            <i class="fa-solid fa-tag me-1"></i>{{ $work->category->name }}
                        </span>

                        <h1 class="cdetail-header__title">
                            {{ $work->title }}
                        </h1>

                        <div class="cdetail-header__meta">
                            <time class="cdetail-header__date" datetime="{{ $work->published_at->toDateString() }}">
                                <i class="fa-regular fa-calendar me-1"></i>{{ $work->published_at->translatedFormat('d F Y') }}
                            </time>
                            <span class="cdetail-header__read-time">
                                <i class="fa-regular fa-clock me-1"></i>{{ $work->readingTime() }} dk okuma
                            </span>
                            <span class="cdetail-header__views">
                                <i class="fa-solid fa-eye me-1"></i>{{ number_format($work->view_count) }} okunma
                            </span>
                        </div>
                    </header>

                    <!-- Cover Image -->
                    <div class="cdetail-cover">
                        @if($work->cover_image)
                            <img src="{{ upload_url($work->cover_image) }}"
                                 alt="{{ $work->title }}"
                                 class="cdetail-cover__img img-fluid"
                                 loading="lazy">
                        @else
                            <div class="cdetail-cover__placeholder">
                                <i class="fa-solid fa-book-open"></i>
                                <span>Kapak Görseli</span>
                            </div>
                        @endif
                    </div>

                    <!-- Article Content -->
                    <div class="cdetail-content">
                        {!! $work->body !!}
                    </div>

                    <!-- Action Bar (Like, Share, Bookmark) -->
                    <div class="cdetail-actions">
                        <div class="cdetail-actions__left">
                            <button type="button" class="cdetail-actions__btn cdetail-actions__btn--like">
                                <i class="fa-regular fa-heart me-1"></i>
                                <span>Beğen</span>
                            </button>
                        </div>
                        <div class="cdetail-actions__right">
                            <button type="button" class="cdetail-actions__btn" title="Yer İmi" aria-label="Yer İmi">
                                <i class="fa-regular fa-bookmark"></i>
                            </button>
                            <button type="button" class="cdetail-actions__btn" title="Paylaş" aria-label="Paylaş">
                                <i class="fa-solid fa-share-nodes"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Author Box -->
                    <div class="cdetail-author-box">
                        <div class="cdetail-author-box__avatar">
                            @if($work->author->avatar_url)
                                <img src="{{ $work->author->avatar_url }}" alt="{{ $work->author->name }}" loading="lazy">
                            @else
                                <i class="fa-solid fa-user"></i>
                            @endif
                        </div>
                        <div class="cdetail-author-box__info">
                            <h4 class="cdetail-author-box__name">
                                <a href="{{ route('profile.show', $work->author->username) }}">{{ $work->author->name }}</a>
                            </h4>
                            @if($work->author->bio)
                                <p class="cdetail-author-box__bio">
                                    {{ $work->author->bio }}
                                </p>
                            @endif
                            <div class="cdetail-author-box__stats">
                                <span><i class="fa-solid fa-file-lines me-1"></i>{{ $work->author->literaryWorks()->where('status', 'approved')->count() }} Yazı</span>
                                <span><i class="fa-solid fa-eye me-1"></i>{{ number_format($work->author->literaryWorks()->where('status', 'approved')->sum('view_count')) }} Okunma</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- =============================================
                     SAĞ KOLON — SIDEBAR
                ============================================== -->
                <aside class="col-lg-4">
                    <div class="cdetail-sidebar">

                        <!-- Author Card -->
                        <div class="cdetail-sidebar__card">
                            <h4 class="cdetail-sidebar__title">
                                <i class="fa-solid fa-user-pen me-2"></i>Yazar
                            </h4>
                            <div class="cdetail-sidebar__author">
                                <div class="cdetail-sidebar__author-avatar">
                                    @if($work->author->avatar_url)
                                        <img src="{{ $work->author->avatar_url }}" alt="{{ $work->author->name }}" loading="lazy">
                                    @else
                                        <i class="fa-solid fa-user"></i>
                                    @endif
                                </div>
                                <div class="cdetail-sidebar__author-info">
                                    <a href="{{ route('profile.show', $work->author->username) }}" class="cdetail-sidebar__author-name">{{ $work->author->name }}</a>
                                    @if($work->author->bio)
                                        <span class="cdetail-sidebar__author-role">{{ Str::limit($work->author->bio, 50) }}</span>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('profile.show', $work->author->username) }}" class="wpost-btn wpost-btn--ghost cdetail-sidebar__follow-btn">
                                <i class="fa-solid fa-user me-1"></i>Profile Git
                            </a>
                        </div>

                        <!-- Article Stats -->
                        <div class="cdetail-sidebar__card">
                            <h4 class="cdetail-sidebar__title">
                                <i class="fa-solid fa-chart-simple me-2"></i>Yazı İstatistikleri
                            </h4>
                            <div class="cdetail-sidebar__stats">
                                <div class="cdetail-sidebar__stat">
                                    <i class="fa-solid fa-eye"></i>
                                    <div class="cdetail-sidebar__stat-info">
                                        <span class="cdetail-sidebar__stat-number">{{ number_format($work->view_count) }}</span>
                                        <span class="cdetail-sidebar__stat-label">Okunma</span>
                                    </div>
                                </div>
                                <div class="cdetail-sidebar__stat">
                                    <i class="fa-solid fa-clock"></i>
                                    <div class="cdetail-sidebar__stat-info">
                                        <span class="cdetail-sidebar__stat-number">{{ $work->readingTime() }} dk</span>
                                        <span class="cdetail-sidebar__stat-label">Okuma Süresi</span>
                                    </div>
                                </div>
                                <div class="cdetail-sidebar__stat">
                                    <i class="fa-solid fa-calendar"></i>
                                    <div class="cdetail-sidebar__stat-info">
                                        <span class="cdetail-sidebar__stat-number">{{ $work->published_at->translatedFormat('d M') }}</span>
                                        <span class="cdetail-sidebar__stat-label">Yayın Tarihi</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Related Posts -->
                        @if($relatedWorks->isNotEmpty())
                            <div class="cdetail-sidebar__card">
                                <h4 class="cdetail-sidebar__title">
                                    <i class="fa-solid fa-layer-group me-2"></i>Benzer İçerikler
                                </h4>
                                <div class="cdetail-sidebar__related">
                                    @foreach($relatedWorks as $related)
                                        <a href="{{ route('literary-works.show', $related->slug) }}" class="cdetail-sidebar__related-item">
                                            <div class="cdetail-sidebar__related-thumb">
                                                @if($related->cover_image)
                                                    <img src="{{ upload_url($related->cover_image) }}" alt="{{ $related->title }}" loading="lazy">
                                                @else
                                                    <i class="fa-solid fa-book-open"></i>
                                                @endif
                                            </div>
                                            <div class="cdetail-sidebar__related-info">
                                                <h5 class="cdetail-sidebar__related-title">{{ Str::limit($related->title, 50) }}</h5>
                                                <span class="cdetail-sidebar__related-meta">
                                                    <i class="fa-solid fa-eye me-1"></i>{{ number_format($related->view_count) }}
                                                    <span class="mx-1">·</span>
                                                    {{ $related->author->name }}
                                                </span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Categories -->
                        <div class="cdetail-sidebar__card">
                            <h4 class="cdetail-sidebar__title">
                                <i class="fa-solid fa-fire me-2"></i>Kategoriler
                            </h4>
                            <div class="cdetail-tags__list">
                                @foreach($categories as $cat)
                                    <a href="{{ route('literary-works.index', ['kategori' => $cat->slug]) }}" class="cdetail-tags__tag">{{ $cat->name }}</a>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </aside>

            </div>
        </div>
    </article>

@endsection

@push('scripts')
<script>
document.querySelectorAll('.cdetail-content img').forEach(function(img) {
    img.setAttribute('loading', 'lazy');
    img.removeAttribute('width');
    img.removeAttribute('height');
});
</script>
@endpush
