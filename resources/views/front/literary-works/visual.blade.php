@extends('layouts.front')

@section('title', 'Görsel Eserler — Boyalı Kelimeler')
@section('meta_description', 'Boyalı Kelimeler ressamlarının en etkileyici görsel eserleri. Dijital sanat, illüstrasyon, resim ve daha fazlası.')
@section('canonical', route('literary-works.visual'))
@section('og_title', 'Görsel Eserler — Boyalı Kelimeler')
@section('og_description', 'Ressamlarımızın en etkileyici görsel eserlerini keşfedin.')

@if(request('sirala') || request('ara'))
    @section('robots', 'noindex, follow')
@endif

@push('seo_links')
    @if($works->previousPageUrl())
        <link rel="prev" href="{{ $works->previousPageUrl() }}">
    @endif
    @if($works->nextPageUrl())
        <link rel="next" href="{{ $works->nextPageUrl() }}">
    @endif
@endpush

@push('jsonld')
<script type="application/ld+json">
{!! json_encode([
    '@@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'CollectionPage',
            'name' => 'Görsel Eserler',
            'description' => 'Boyalı Kelimeler ressamlarının en etkileyici görsel eserleri.',
            'url' => route('literary-works.visual'),
            'isPartOf' => [
                '@type' => 'WebSite',
                'name' => 'Boyalı Kelimeler',
                'url' => url('/'),
            ],
            'mainEntity' => [
                '@type' => 'ItemList',
                'numberOfItems' => $works->total(),
                'itemListElement' => $works->map(fn ($w, $i) => [
                    '@type' => 'ListItem',
                    'position' => ($works->currentPage() - 1) * $works->perPage() + $i + 1,
                    'url' => route('literary-works.show', $w->slug),
                    'name' => $w->title,
                ])->all(),
            ],
        ],
        [
            '@type' => 'BreadcrumbList',
            'name' => 'Breadcrumb',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Ana Sayfa', 'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Görsel Eserler'],
            ],
        ],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush

@section('content')

    <!-- Page Hero -->
    <section class="clist-hero" aria-label="Görsel Eserler başlığı">
        <div class="container">
            <div class="clist-hero__inner">
                <h1 class="clist-hero__title">
                    <i class="fa-solid fa-palette me-3"></i>Görsel Eserler
                </h1>
                <p class="clist-hero__desc">
                    Ressamlarımızın fırçasından doğan en etkileyici görsel eserler. Dijital sanattan illüstrasyona — renklerin boyandığı dünya.
                </p>
                <div class="clist-hero__stats">
                    <span class="clist-hero__stat">
                        <i class="fa-solid fa-image me-1"></i>{{ number_format($stats['work_count']) }} Görsel
                    </span>
                    <span class="clist-hero__stat-sep">|</span>
                    <span class="clist-hero__stat">
                        <i class="fa-solid fa-users me-1"></i>{{ number_format($stats['author_count']) }} Ressam
                    </span>
                    <span class="clist-hero__stat-sep">|</span>
                    <span class="clist-hero__stat">
                        <i class="fa-solid fa-eye me-1"></i>{{ number_format($stats['total_views']) }} Görüntülenme
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- Content List Section -->
    <section class="clist-section" aria-label="Görsel eser listesi">
        <div class="container">

            <!-- Toolbar: Search + Filter + Grid Switcher -->
            <form class="clist-toolbar" method="GET" action="{{ route('literary-works.visual') }}" id="clistFilterForm">
                <div class="clist-toolbar__search">
                    <i class="fa-solid fa-magnifying-glass clist-toolbar__search-icon"></i>
                    <input type="text"
                           name="ara"
                           class="wpost-form__input clist-toolbar__search-input"
                           placeholder="Görsel eserlerde ara..."
                           value="{{ request('ara') }}">
                </div>
                <div class="clist-toolbar__filters">
                    <select name="sirala" class="wpost-form__input clist-toolbar__select" onchange="this.form.submit()">
                        <option value="newest" @selected($currentSort === 'newest')>En Yeni</option>
                        <option value="oldest" @selected($currentSort === 'oldest')>En Eski</option>
                        <option value="popular" @selected($currentSort === 'popular')>En Çok Görüntülenen</option>
                    </select>
                </div>
                <div class="clist-toolbar__view" aria-label="Görünüm seçici">
                    <button type="button" class="clist-toolbar__view-btn" data-cols="2" title="2 Kolon" aria-label="2 Kolon görünüm">
                        <i class="fa-solid fa-pause me-1"></i>2
                    </button>
                    <button type="button" class="clist-toolbar__view-btn clist-toolbar__view-btn--active" data-cols="3" title="3 Kolon" aria-label="3 Kolon görünüm">
                        <i class="fa-solid fa-th-large me-1"></i>3
                    </button>
                    <button type="button" class="clist-toolbar__view-btn" data-cols="4" title="4 Kolon" aria-label="4 Kolon görünüm">
                        <i class="fa-solid fa-th me-1"></i>4
                    </button>
                    <button type="button" class="clist-toolbar__view-btn" data-cols="5" title="5 Kolon" aria-label="5 Kolon görünüm">
                        <i class="fa-solid fa-border-all me-1"></i>5
                    </button>
                </div>
            </form>

            <!-- Content Cards Grid -->
            <div class="clist-grid" id="clistGrid" data-cols="3">

                @forelse($works as $work)
                    <article class="clist-card">
                        <a href="{{ route('literary-works.show', $work->slug) }}" class="clist-card__thumb-link">
                            <div class="clist-card__thumb">
                                @if($work->cover_image)
                                    <x-responsive-image :path="$work->cover_image" :alt="$work->title" size="md" class="clist-card__thumb-img" />
                                @else
                                    <div class="clist-card__thumb-placeholder">
                                        <i class="fa-solid fa-palette"></i>
                                    </div>
                                @endif
                                <span class="clist-card__category clist-card__category--{{ $work->category->slug }}">
                                    <i class="fa-solid fa-tag me-1"></i>{{ $work->category->name }}
                                </span>
                            </div>
                        </a>
                        <div class="clist-card__body">
                            <div class="clist-card__meta-top">
                                <time class="clist-card__date" datetime="{{ $work->published_at->toDateString() }}">
                                    <i class="fa-regular fa-calendar me-1"></i>{{ $work->published_at->translatedFormat('d M Y') }}
                                </time>
                                <span class="clist-card__views">
                                    <i class="fa-solid fa-eye me-1"></i>{{ number_format($work->view_count) }}
                                </span>
                            </div>
                            <h3 class="clist-card__title">
                                <a href="{{ route('literary-works.show', $work->slug) }}">{{ $work->title }}</a>
                            </h3>
                            @if($work->excerpt)
                                <p class="clist-card__excerpt">{{ Str::limit($work->excerpt, 120) }}</p>
                            @endif
                            <div class="clist-card__footer">
                                @if($work->author?->username)
                                    <a href="{{ route('profile.show', $work->author->username) }}" class="clist-card__author">
                                        <div class="clist-card__avatar">
                                            @if($work->author->avatar_url)
                                                <img src="{{ $work->author->avatar_url }}" alt="{{ $work->author->name }}" loading="lazy">
                                            @else
                                                <i class="fa-solid fa-user"></i>
                                            @endif
                                        </div>
                                        <span class="clist-card__author-name">{{ $work->author->name }}</span>
                                    </a>
                                @endif
                                <div class="clist-card__stats">
                                    <span class="clist-card__stat"><i class="fa-solid fa-eye me-1"></i>{{ number_format($work->view_count) }}</span>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="clist-empty">
                        <div class="clist-empty__icon">
                            <i class="fa-solid fa-palette"></i>
                        </div>
                        <h3 class="clist-empty__title">Görsel Eser Bulunamadı</h3>
                        <p class="clist-empty__desc">
                            @if(request('ara'))
                                <strong>"{{ request('ara') }}"</strong> araması için sonuç bulunamadı.
                            @else
                                Henüz görsel eser bulunmamaktadır.
                            @endif
                        </p>
                        <a href="{{ route('literary-works.visual') }}" class="clist-empty__btn">
                            <i class="fa-solid fa-arrows-rotate me-2"></i>Tüm Görsel Eserleri Gör
                        </a>
                    </div>
                @endforelse

            </div>

            <!-- Pagination -->
            @if($works->hasPages())
                <nav class="clist-pagination" aria-label="Sayfalama">
                    {{ $works->links('vendor.pagination.literary-works') }}
                </nav>
            @endif

        </div>
    </section>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    var grid = document.getElementById('clistGrid');
    var viewBtns = document.querySelectorAll('.clist-toolbar__view-btn');

    viewBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var cols = this.getAttribute('data-cols');

            viewBtns.forEach(function (b) {
                b.classList.remove('clist-toolbar__view-btn--active');
            });
            this.classList.add('clist-toolbar__view-btn--active');

            grid.setAttribute('data-cols', cols);
        });
    });
})();
</script>
@endpush
