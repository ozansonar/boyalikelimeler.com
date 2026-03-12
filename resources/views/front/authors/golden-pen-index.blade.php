@extends('layouts.front')

@section('title', ($pageSettings['golden_pen_title'] ?? 'Altın Kalemlerimiz') . ' — Boyalı Kelimeler')
@section('meta_description', ($pageSettings['golden_pen_description'] ?? 'Boyalı Kelimeler altın kalem yazarları. Her ay en iyi yazarlarımızı seçiyoruz.'))
@section('canonical', route('authors.golden-pen-index'))
@section('og_title', ($pageSettings['golden_pen_title'] ?? 'Altın Kalemlerimiz') . ' — Boyalı Kelimeler')
@section('og_description', ($pageSettings['golden_pen_description'] ?? 'Boyalı Kelimeler altın kalem yazarları. Her ay en iyi yazarlarımızı seçiyoruz.'))

@if(request('page'))
    @section('robots', 'noindex, follow')
@endif

@push('seo_links')
    @if($months->previousPageUrl())
        <link rel="prev" href="{{ $months->previousPageUrl() }}">
    @endif
    @if($months->nextPageUrl())
        <link rel="next" href="{{ $months->nextPageUrl() }}">
    @endif
@endpush

@push('jsonld')
<script type="application/ld+json">
{!! json_encode([
    '@@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'CollectionPage',
            'name' => ($pageSettings['golden_pen_title'] ?? 'Altın Kalemlerimiz') . ' — Boyalı Kelimeler',
            'description' => $pageSettings['golden_pen_description'] ?? 'Boyalı Kelimeler altın kalem yazarları.',
            'url' => route('authors.golden-pen-index'),
        ],
        [
            '@type' => 'BreadcrumbList',
            'name' => 'Breadcrumb',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Ana Sayfa', 'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => $pageSettings['golden_pen_title'] ?? 'Altın Kalemlerimiz'],
            ],
        ],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush

@section('content')

    <!-- =======================================================
         PAGE HEADER
    ======================================================= -->
    <section class="page-header">
        <div class="container">
            <div class="page-header__inner">
                <h1 class="page-header__title">
                    <i class="fa-solid fa-pen-nib me-2"></i>{{ $pageSettings['golden_pen_title'] ?? 'Altın Kalemlerimiz' }}
                </h1>
                <div class="page-header__divider"></div>
                @if(!empty($pageSettings['golden_pen_description']))
                    <p class="page-header__desc">
                        {{ $pageSettings['golden_pen_description'] }}
                    </p>
                @endif
            </div>
        </div>
    </section>

    <!-- =======================================================
         BREADCRUMB
    ======================================================= -->
    <section class="section pt-0 pb-0">
        <div class="container">
            <nav aria-label="Breadcrumb">
                <ol class="golden-breadcrumb">
                    <li class="golden-breadcrumb__item">
                        <a href="{{ url('/') }}" class="golden-breadcrumb__link">
                            <i class="fa-solid fa-house me-1"></i>Ana Sayfa
                        </a>
                    </li>
                    <li class="golden-breadcrumb__item golden-breadcrumb__item--active">
                        {{ $pageSettings['golden_pen_title'] ?? 'Altın Kalemlerimiz' }}
                    </li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- =======================================================
         GOLDEN PEN MONTHS GRID
    ======================================================= -->
    <section class="section">
        <div class="container">
            <div class="row g-4">
                @foreach($months as $month)
                    <div class="col-lg-4 col-md-4 col-6">
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

            <!-- Pagination -->
            @if($months->hasPages())
                <nav class="member-pagination" aria-label="Sayfalama">
                    @if($months->onFirstPage())
                        <button class="member-pagination__btn member-pagination__btn--prev" type="button" disabled aria-label="Önceki sayfa">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                    @else
                        <a href="{{ $months->previousPageUrl() }}" class="member-pagination__btn member-pagination__btn--prev" aria-label="Önceki sayfa">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                    @endif

                    <div class="member-pagination__pages">
                        @foreach($months->getUrlRange(max(1, $months->currentPage() - 2), min($months->lastPage(), $months->currentPage() + 2)) as $page => $url)
                            <a href="{{ $url }}" class="member-pagination__page {{ $page === $months->currentPage() ? 'member-pagination__page--active' : '' }}">{{ $page }}</a>
                        @endforeach
                    </div>

                    @if($months->hasMorePages())
                        <a href="{{ $months->nextPageUrl() }}" class="member-pagination__btn member-pagination__btn--next" aria-label="Sonraki sayfa">
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
