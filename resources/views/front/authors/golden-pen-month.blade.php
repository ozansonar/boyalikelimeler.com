@extends('layouts.front')

@section('title', $monthData['label'] . ' — Boyalı Kelimeler')
@section('meta_description', $monthData['label'] . ' — Boyalı Kelimeler altın kalem sanatçıları.')
@section('canonical', route('authors.golden-pen-month', $yearMonth))
@section('og_title', $monthData['label'] . ' — Boyalı Kelimeler')
@section('og_description', $monthData['label'] . ' — Boyalı Kelimeler altın kalem sanatçıları.')

@push('jsonld')
<script type="application/ld+json">
{!! json_encode([
    '@@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'CollectionPage',
            'name' => $monthData['label'] . ' — Boyalı Kelimeler',
            'description' => $monthData['label'] . ' altın kalem sanatçıları.',
            'url' => route('authors.golden-pen-month', $yearMonth),
        ],
        [
            '@type' => 'BreadcrumbList',
            'name' => 'Breadcrumb',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Ana Sayfa', 'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Sanatçılar', 'item' => route('authors.index')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $monthData['label']],
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
                    <i class="fa-solid fa-pen-nib me-2"></i>{{ $monthData['label'] }}
                </h1>
                <div class="page-header__divider"></div>
                <p class="page-header__desc">
                    {{ $pageSettings['golden_pen_description'] ?? 'Bu ay altın kalem ödülüne layık görülen sanatçılarımız.' }}
                </p>
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
                        <a href="{{ route('authors.index') }}" class="golden-breadcrumb__link">
                            <i class="fa-solid fa-users me-1"></i>Sanatçılarımız
                        </a>
                    </li>
                    <li class="golden-breadcrumb__item golden-breadcrumb__item--active">
                        {{ $monthData['label'] }}
                    </li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- =======================================================
         GOLDEN PEN AUTHORS FOR THIS MONTH
    ======================================================= -->
    <section class="section">
        <div class="container">
            <div class="row g-4 justify-content-center" id="memberGrid">
                @forelse($monthData['authors'] as $gpAuthor)
                    <div class="col-lg-3 col-md-4 col-6">
                        <a href="{{ $gpAuthor->profile_url }}" class="text-decoration-none">
                            <article class="member-card">
                                <div class="member-card__avatar-wrap">
                                    <div class="member-card__avatar" data-initials="{{ mb_strtoupper(mb_substr($gpAuthor->name, 0, 1)) . mb_strtoupper(mb_substr(explode(' ', $gpAuthor->name)[1] ?? '', 0, 1)) }}">
                                        @if($gpAuthor->avatar)
                                            <img src="{{ upload_url($gpAuthor->avatar, 'thumb') }}"
                                                 alt="{{ $gpAuthor->name }}"
                                                 class="member-card__photo"
                                                 loading="lazy">
                                        @endif
                                    </div>
                                    <div class="member-card__glow"></div>
                                    <div class="member-card__golden-badge" title="Altın Kalem">
                                        <i class="fa-solid fa-pen-nib"></i>
                                    </div>
                                </div>
                                <h3 class="member-card__name">{{ $gpAuthor->name }}</h3>
                                <div class="member-card__line"></div>
                                <p class="member-card__role">
                                    {{ $gpAuthor->approved_works_count ?? 0 }} eser
                                    <span class="member-card__separator">·</span>
                                    {{ number_format((int) ($gpAuthor->total_views ?? 0)) }} okunma
                                </p>
                            </article>
                        </a>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="golden-empty">
                            <div class="golden-empty__icon-wrap">
                                <div class="golden-empty__circle">
                                    <i class="fa-solid fa-pen-nib golden-empty__icon"></i>
                                </div>
                            </div>
                            <h2 class="golden-empty__title">Henüz Belirlenmedi</h2>
                            <div class="golden-empty__divider"></div>
                            <p class="golden-empty__text">
                                Bu ay için Altın Kalem sanatçısı henüz belirlenmemiştir.
                                Değerlendirme sürecimiz devam ediyor; lütfen daha sonra tekrar ziyaret ediniz.
                            </p>
                            <p class="golden-empty__sub">
                                <i class="fa-solid fa-quote-left me-1"></i>
                                Edebiyatın ışığı her ay yeni kalemleri aydınlatmaya devam edecek.
                                <i class="fa-solid fa-quote-right ms-1"></i>
                            </p>
                            <a href="{{ route('authors.index') }}" class="golden-empty__btn">
                                <i class="fa-solid fa-users me-2"></i>Tüm Sanatçılarımız
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

@endsection
