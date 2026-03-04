@extends('layouts.front')

@section('title', 'Yazarlarımız — Boyalı Kelimeler')
@section('meta_description', 'Boyalı Kelimeler yazarları ile tanışın. Şairler, hikayeciler ve deneme yazarlarımız.')
@section('canonical', route('authors.index'))
@section('og_title', 'Yazarlarımız — Boyalı Kelimeler')
@section('og_description', 'Boyalı Kelimeler yazarları ile tanışın. Şairler, hikayeciler ve deneme yazarlarımız.')

@section('content')

    <!-- =======================================================
         PAGE HEADER
    ======================================================= -->
    <section class="page-header">
        <div class="container">
            <div class="page-header__inner">
                <h1 class="page-header__title">
                    <i class="fa-solid fa-feather-pointed me-2"></i>Yazarlarımız
                </h1>
                <div class="page-header__divider"></div>
                <p class="page-header__desc">
                    Kelimelerin büyüsüne inanan, kalemleriyle dünyalar kuran yazarlarımızla tanışın.
                </p>
            </div>
        </div>
    </section>

    <!-- =======================================================
         STATS
    ======================================================= -->
    <section class="section pt-0">
        <div class="container">
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="author-stat-card" data-aos="fade-up" data-aos-delay="0">
                        <div class="author-stat-card__icon">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div class="author-stat-card__value">{{ number_format($stats['author_count']) }}</div>
                        <div class="author-stat-card__label">Yazar</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="author-stat-card" data-aos="fade-up" data-aos-delay="100">
                        <div class="author-stat-card__icon author-stat-card__icon--gold">
                            <i class="fa-solid fa-pen-nib"></i>
                        </div>
                        <div class="author-stat-card__value">{{ number_format($stats['golden_pen_count']) }}</div>
                        <div class="author-stat-card__label">Altın Kalem</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="author-stat-card" data-aos="fade-up" data-aos-delay="200">
                        <div class="author-stat-card__icon">
                            <i class="fa-solid fa-book-open"></i>
                        </div>
                        <div class="author-stat-card__value">{{ number_format($stats['total_works']) }}</div>
                        <div class="author-stat-card__label">Eser</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="author-stat-card" data-aos="fade-up" data-aos-delay="300">
                        <div class="author-stat-card__icon">
                            <i class="fa-solid fa-eye"></i>
                        </div>
                        <div class="author-stat-card__value">{{ number_format($stats['total_views']) }}</div>
                        <div class="author-stat-card__label">Okunma</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =======================================================
         AUTHORS LIST
    ======================================================= -->
    <section class="section pt-0">
        <div class="container">

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
