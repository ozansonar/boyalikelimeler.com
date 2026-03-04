@extends('layouts.front')

@section('title', !empty($pageSettings['meta_title']) ? $pageSettings['meta_title'] : 'Yazarlarımız — Boyalı Kelimeler')
@section('meta_description', !empty($pageSettings['meta_description']) ? $pageSettings['meta_description'] : 'Boyalı Kelimeler yazarları ile tanışın. Şairler, hikayeciler ve deneme yazarlarımız.')
@section('canonical', route('authors.index'))
@section('og_title', !empty($pageSettings['meta_title']) ? $pageSettings['meta_title'] : 'Yazarlarımız — Boyalı Kelimeler')
@section('og_description', !empty($pageSettings['meta_description']) ? $pageSettings['meta_description'] : 'Boyalı Kelimeler yazarları ile tanışın. Şairler, hikayeciler ve deneme yazarlarımız.')

@section('content')

    <!-- =======================================================
         PAGE HEADER
    ======================================================= -->
    <section class="page-header">
        <div class="container">
            <div class="page-header__inner">
                <h1 class="page-header__title">
                    <i class="fa-solid fa-feather-pointed me-2"></i>{{ $pageSettings['title'] ?? 'Yazarlarımız' }}
                </h1>
                <div class="page-header__divider"></div>
                <p class="page-header__desc">
                    {{ $pageSettings['description'] ?? 'Kelimelerin büyüsüne inanan, kalemleriyle dünyalar kuran yazarlarımızla tanışın.' }}
                </p>
            </div>
        </div>
    </section>

    <!-- =======================================================
         FEATURED AUTHOR
    ======================================================= -->
    @if($featuredAuthor)
        <section class="section pt-0">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-5 col-md-7 col-10" data-aos="fade-up">
                        <a href="{{ $featuredAuthor->profile_url }}" class="text-decoration-none">
                            <article class="authors-featured">
                                <div class="authors-featured__badge">
                                    <i class="fa-solid fa-crown me-1"></i> Öne Çıkan Yazar
                                </div>
                                <div class="authors-featured__avatar-wrap">
                                    <div class="authors-featured__avatar" data-initials="{{ mb_strtoupper(mb_substr($featuredAuthor->name, 0, 1)) . mb_strtoupper(mb_substr(explode(' ', $featuredAuthor->name)[1] ?? '', 0, 1)) }}">
                                        @if($featuredAuthor->avatar)
                                            <img src="{{ upload_url($featuredAuthor->avatar, 'md') }}"
                                                 alt="{{ $featuredAuthor->name }}"
                                                 class="authors-featured__photo img-fluid"
                                                 loading="lazy">
                                        @endif
                                    </div>
                                    @if($featuredAuthor->hasActiveGoldenPen())
                                        <div class="authors-featured__golden" title="Altın Kalem">
                                            <i class="fa-solid fa-pen-nib"></i>
                                        </div>
                                    @endif
                                </div>
                                <h2 class="authors-featured__name">{{ $featuredAuthor->name }}</h2>
                                <div class="authors-featured__line"></div>
                                <p class="authors-featured__stats">
                                    {{ $featuredAuthor->approved_works_count ?? 0 }} eser
                                    <span class="authors-featured__sep">·</span>
                                    {{ number_format((int) ($featuredAuthor->total_views ?? 0)) }} okunma
                                </p>
                                @if($featuredAuthor->bio)
                                    <p class="authors-featured__bio">{{ Str::limit($featuredAuthor->bio, 150) }}</p>
                                @endif
                            </article>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- =======================================================
         GOLDEN PEN MONTHS — MONTHLY CARDS SLIDER
    ======================================================= -->
    @if(!empty($goldenPenMonths))
        <section class="section section--surface" data-aos="fade-up">
            <div class="container">
                <div class="golden-slider">
                    <div class="golden-slider__header">
                        <div>
                            <h2 class="golden-slider__title">
                                <i class="fa-solid fa-pen-nib me-2"></i>{{ $pageSettings['golden_pen_title'] ?? 'Altın Kalemlerimiz' }}
                            </h2>
                            @if(!empty($pageSettings['golden_pen_description']))
                                <p class="golden-slider__desc">{{ $pageSettings['golden_pen_description'] }}</p>
                            @endif
                        </div>
                        <div class="golden-slider__nav">
                            <button class="golden-slider__nav-btn golden-slider__nav-btn--prev" type="button" aria-label="Önceki ay">
                                <i class="fa-solid fa-chevron-left"></i>
                            </button>
                            <button class="golden-slider__nav-btn golden-slider__nav-btn--next" type="button" aria-label="Sonraki ay">
                                <i class="fa-solid fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>

                    <div class="swiper golden-slider__swiper">
                        <div class="swiper-wrapper">
                            @foreach($goldenPenMonths as $month)
                                <div class="swiper-slide">
                                    <a href="{{ route('authors.golden-pen-month', $month['key']) }}" class="text-decoration-none">
                                        <article class="golden-month-card">
                                            <div class="golden-month-card__icon">
                                                <i class="fa-solid fa-pen-nib"></i>
                                            </div>
                                            <h3 class="golden-month-card__label">{{ $month['label'] }}</h3>
                                            <div class="golden-month-card__action">
                                                <span>Yazarları Gör</span>
                                                <i class="fa-solid fa-arrow-right ms-2"></i>
                                            </div>
                                        </article>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- =======================================================
         AUTHORS LIST
    ======================================================= -->
    <section class="section">
        <div class="container">

            <h2 class="authors-list__title" data-aos="fade-up">
                <i class="fa-solid fa-users me-2"></i>{{ $pageSettings['authors_list_title'] ?? 'Yazarlarımız' }}
            </h2>

            <!-- Toolbar: Search + Sort + Filter -->
            <div class="member-toolbar">
                <form method="GET" action="{{ route('authors.index') }}" class="member-toolbar__form" id="authorFilterForm">
                    <div class="member-toolbar__search">
                        <i class="fa-solid fa-magnifying-glass member-toolbar__search-icon"></i>
                        <input type="text"
                               name="search"
                               class="member-toolbar__input"
                               placeholder="Yazar ara..."
                               value="{{ $filters['search'] ?? '' }}"
                               autocomplete="off">
                    </div>
                    <div class="member-toolbar__sort">
                        @php
                            $currentSort = ($filters['sort'] ?? 'created_at') . '_' . ($filters['dir'] ?? 'desc');
                        @endphp
                        <button class="member-toolbar__sort-btn {{ $currentSort === 'created_at_desc' ? 'member-toolbar__sort-btn--active' : '' }}"
                                type="submit" name="sort" value="created_at" title="En Yeni">
                            <i class="fa-solid fa-clock me-1"></i><span class="d-none d-md-inline">En Yeni</span>
                            <input type="hidden" name="dir" value="desc" disabled>
                        </button>
                        <button class="member-toolbar__sort-btn {{ $currentSort === 'name_asc' ? 'member-toolbar__sort-btn--active' : '' }}"
                                type="submit" name="sort" value="name" title="A-Z">
                            <i class="fa-solid fa-arrow-down-a-z me-1"></i><span class="d-none d-md-inline">A-Z</span>
                            <input type="hidden" name="dir" value="asc" disabled>
                        </button>
                        <button class="member-toolbar__sort-btn {{ $currentSort === 'works_desc' ? 'member-toolbar__sort-btn--active' : '' }}"
                                type="submit" name="sort" value="works" title="En Çok Eser">
                            <i class="fa-solid fa-book me-1"></i><span class="d-none d-md-inline">En Çok Eser</span>
                            <input type="hidden" name="dir" value="desc" disabled>
                        </button>
                        <button class="member-toolbar__sort-btn {{ !empty($filters['golden_pen']) ? 'member-toolbar__sort-btn--active' : '' }}"
                                type="submit" name="golden_pen" value="{{ !empty($filters['golden_pen']) ? '' : '1' }}" title="Altın Kalem">
                            <i class="fa-solid fa-pen-nib me-1"></i><span class="d-none d-md-inline">Altın Kalem</span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="member-toolbar__count">
                <span>{{ $authors->total() }} yazar gösteriliyor</span>
            </div>

            <div class="row g-4" id="memberGrid">
                @forelse($authors as $author)
                    <div class="col-lg-3 col-md-4 col-6">
                        <a href="{{ $author->profile_url }}" class="text-decoration-none">
                            <article class="member-card">
                                <div class="member-card__avatar-wrap">
                                    <div class="member-card__avatar" data-initials="{{ mb_strtoupper(mb_substr($author->name, 0, 1)) . mb_strtoupper(mb_substr(explode(' ', $author->name)[1] ?? '', 0, 1)) }}">
                                        @if($author->avatar)
                                            <img src="{{ upload_url($author->avatar, 'thumb') }}"
                                                 alt="{{ $author->name }}"
                                                 class="member-card__photo"
                                                 loading="lazy">
                                        @endif
                                    </div>
                                    <div class="member-card__glow"></div>
                                    @if($author->hasActiveGoldenPen())
                                        <div class="member-card__golden-badge" title="Altın Kalem">
                                            <i class="fa-solid fa-pen-nib"></i>
                                        </div>
                                    @endif
                                </div>
                                <h3 class="member-card__name">{{ $author->name }}</h3>
                                <div class="member-card__line"></div>
                                <p class="member-card__role">
                                    {{ $author->approved_works_count ?? 0 }} eser
                                    <span class="member-card__separator">·</span>
                                    {{ number_format((int) ($author->total_views ?? 0)) }} okunma
                                </p>
                            </article>
                        </a>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="member-no-result member-no-result--visible">
                            <i class="fa-solid fa-search member-no-result__icon"></i>
                            <p class="member-no-result__text">Aramanızla eşleşen yazar bulunamadı.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($authors->hasPages())
                <nav class="member-pagination" aria-label="Sayfalama">
                    @if($authors->onFirstPage())
                        <button class="member-pagination__btn member-pagination__btn--prev" type="button" disabled aria-label="Önceki sayfa">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                    @else
                        <a href="{{ $authors->previousPageUrl() }}" class="member-pagination__btn member-pagination__btn--prev" aria-label="Önceki sayfa">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                    @endif

                    <div class="member-pagination__pages">
                        @foreach($authors->getUrlRange(max(1, $authors->currentPage() - 2), min($authors->lastPage(), $authors->currentPage() + 2)) as $page => $url)
                            <a href="{{ $url }}" class="member-pagination__page {{ $page === $authors->currentPage() ? 'member-pagination__page--active' : '' }}">{{ $page }}</a>
                        @endforeach
                    </div>

                    @if($authors->hasMorePages())
                        <a href="{{ $authors->nextPageUrl() }}" class="member-pagination__btn member-pagination__btn--next" aria-label="Sonraki sayfa">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    @else
                        <button class="member-pagination__btn member-pagination__btn--next" type="button" disabled aria-label="Sonraki sayfa">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    @endif
                </nav>
            @endif

        </div>
    </section>

@endsection
