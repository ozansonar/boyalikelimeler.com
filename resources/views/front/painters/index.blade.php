@extends('layouts.front')

@section('title', !empty($pageSettings['meta_title']) ? $pageSettings['meta_title'] : 'Ressamlarımız — Boyalı Kelimeler')
@section('meta_description', !empty($pageSettings['meta_description']) ? $pageSettings['meta_description'] : 'Boyalı Kelimeler ressamları ile tanışın. Görsel eser veren sanatçılarımız.')
@section('canonical', route('painters.index'))
@section('og_title', !empty($pageSettings['meta_title']) ? $pageSettings['meta_title'] : 'Ressamlarımız — Boyalı Kelimeler')
@section('og_description', !empty($pageSettings['meta_description']) ? $pageSettings['meta_description'] : 'Boyalı Kelimeler ressamları ile tanışın. Görsel eser veren sanatçılarımız.')

@if(request()->anyFilled(['search', 'sort', 'dir', 'page']))
    @section('robots', 'noindex, follow')
@endif

@push('jsonld')
<script type="application/ld+json">
{!! json_encode([
    '@@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'CollectionPage',
            'name' => !empty($pageSettings['meta_title']) ? $pageSettings['meta_title'] : 'Ressamlarımız — Boyalı Kelimeler',
            'description' => !empty($pageSettings['meta_description']) ? $pageSettings['meta_description'] : 'Boyalı Kelimeler ressamları ile tanışın.',
            'url' => route('painters.index'),
        ],
        [
            '@type' => 'BreadcrumbList',
            'name' => 'Breadcrumb',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Ana Sayfa', 'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Ressamlar'],
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
                    <i class="fa-solid fa-palette me-2"></i>{{ $pageSettings['title'] ?? 'Ressamlarımız' }}
                </h1>
                <div class="page-header__divider"></div>
                <p class="page-header__desc">
                    {{ $pageSettings['description'] ?? 'Renklerin ve fırçanın büyüsüne inanan, görsel eserleriyle dünyalar kuran ressamlarımızla tanışın.' }}
                </p>
            </div>
        </div>
    </section>

    <!-- =======================================================
         FEATURED PAINTERS
    ======================================================= -->
    @if($featuredPainters->isNotEmpty())
        @php
            $featuredLabels = json_decode($pageSettings['featured_painter_labels'] ?? '{}', true) ?: [];
        @endphp
        <section class="section">
            <div class="container">
                <div class="row g-4 justify-content-center">
                    @foreach($featuredPainters as $featuredPainter)
                        <div class="col-lg-3 col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                            <a href="{{ $featuredPainter->profile_url }}" class="text-decoration-none">
                                <article class="authors-featured">
                                    <div class="authors-featured__avatar-wrap">
                                        <div class="authors-featured__badge">
                                            <i class="fa-solid fa-palette me-1"></i> Öne Çıkan
                                        </div>
                                        <div class="authors-featured__avatar" data-initials="{{ mb_strtoupper(mb_substr($featuredPainter->name, 0, 1)) . mb_strtoupper(mb_substr(explode(' ', $featuredPainter->name)[1] ?? '', 0, 1)) }}">
                                            @if($featuredPainter->avatar)
                                                <img src="{{ upload_url($featuredPainter->avatar, 'md') }}"
                                                     alt="{{ $featuredPainter->name }}"
                                                     class="authors-featured__photo img-fluid"
                                                     loading="lazy">
                                            @endif
                                        </div>
                                        @if($featuredPainter->hasActiveGoldenPen())
                                            <div class="authors-featured__golden" title="Altın Kalem">
                                                <i class="fa-solid fa-pen-nib"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <h2 class="authors-featured__name">{{ $featuredPainter->name }}</h2>
                                    <div class="authors-featured__line"></div>
                                    <p class="authors-featured__stats">
                                        {{ $featuredPainter->approved_visual_works_count ?? 0 }} görsel eser
                                        <span class="authors-featured__sep">·</span>
                                        {{ number_format((int) ($featuredPainter->total_visual_views ?? 0)) }} görüntülenme
                                    </p>
                                    @if(!empty($featuredLabels[(string) $featuredPainter->id]))
                                        <p class="authors-featured__bio">{{ $featuredLabels[(string) $featuredPainter->id] }}</p>
                                    @elseif($featuredPainter->bio)
                                        <p class="authors-featured__bio">{{ Str::limit($featuredPainter->bio, 100) }}</p>
                                    @endif
                                </article>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- =======================================================
         PAINTERS LIST
    ======================================================= -->
    <section class="section">
        <div class="container">

            <h2 class="authors-list__title" data-aos="fade-up">
                <i class="fa-solid fa-palette me-2"></i>{{ $pageSettings['painters_list_title'] ?? 'Ressamlarımız' }}
            </h2>

            <!-- Toolbar: Search + Sort -->
            <div class="member-toolbar">
                <form method="GET" action="{{ route('painters.index') }}" class="member-toolbar__form" id="painterFilterForm">
                    <div class="member-toolbar__search">
                        <i class="fa-solid fa-magnifying-glass member-toolbar__search-icon"></i>
                        <input type="text"
                               name="search"
                               class="member-toolbar__input"
                               placeholder="Ressam ara..."
                               value="{{ $filters['search'] ?? '' }}"
                               autocomplete="off">
                    </div>
                    <div class="member-toolbar__sort">
                        @php
                            $currentSort = ($filters['sort'] ?? 'created_at') . '_' . ($filters['dir'] ?? 'desc');
                        @endphp
                        <input type="hidden" name="dir" id="painterSortDir" value="{{ $filters['dir'] ?? 'desc' }}">
                        <button class="member-toolbar__sort-btn {{ $currentSort === 'created_at_desc' ? 'member-toolbar__sort-btn--active' : '' }}"
                                type="submit" name="sort" value="created_at" title="Yeniden Eskiye"
                                data-dir="desc">
                            <i class="fa-solid fa-clock me-1"></i><span class="d-none d-md-inline">Yeniden Eskiye</span>
                        </button>
                        <button class="member-toolbar__sort-btn {{ $currentSort === 'created_at_asc' ? 'member-toolbar__sort-btn--active' : '' }}"
                                type="submit" name="sort" value="created_at" title="Eskiden Yeniye"
                                data-dir="asc">
                            <i class="fa-solid fa-clock-rotate-left me-1"></i><span class="d-none d-md-inline">Eskiden Yeniye</span>
                        </button>
                        <button class="member-toolbar__sort-btn {{ $currentSort === 'name_asc' ? 'member-toolbar__sort-btn--active' : '' }}"
                                type="submit" name="sort" value="name" title="A'dan Z'ye"
                                data-dir="asc">
                            <i class="fa-solid fa-arrow-down-a-z me-1"></i><span class="d-none d-md-inline">A-Z</span>
                        </button>
                        <button class="member-toolbar__sort-btn {{ $currentSort === 'name_desc' ? 'member-toolbar__sort-btn--active' : '' }}"
                                type="submit" name="sort" value="name" title="Z'den A'ya"
                                data-dir="desc">
                            <i class="fa-solid fa-arrow-up-z-a me-1"></i><span class="d-none d-md-inline">Z-A</span>
                        </button>
                        <button class="member-toolbar__sort-btn {{ $currentSort === 'works_desc' ? 'member-toolbar__sort-btn--active' : '' }}"
                                type="submit" name="sort" value="works" title="En Çok Eser"
                                data-dir="desc">
                            <i class="fa-solid fa-image me-1"></i><span class="d-none d-md-inline">Çok Eser</span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="member-toolbar__count">
                <span>{{ $painters->total() }} ressam gösteriliyor</span>
            </div>

            <div class="row g-4" id="memberGrid">
                @forelse($painters as $painter)
                    <div class="col-lg-3 col-md-4 col-6">
                        <a href="{{ $painter->profile_url }}" class="text-decoration-none">
                            <article class="member-card">
                                <div class="member-card__avatar-wrap">
                                    <div class="member-card__avatar" data-initials="{{ mb_strtoupper(mb_substr($painter->name, 0, 1)) . mb_strtoupper(mb_substr(explode(' ', $painter->name)[1] ?? '', 0, 1)) }}">
                                        @if($painter->avatar)
                                            <img src="{{ upload_url($painter->avatar, 'thumb') }}"
                                                 alt="{{ $painter->name }}"
                                                 class="member-card__photo"
                                                 loading="lazy">
                                        @endif
                                    </div>
                                    <div class="member-card__glow"></div>
                                    @if($painter->hasActiveGoldenPen())
                                        <div class="member-card__golden-badge" title="Altın Kalem">
                                            <i class="fa-solid fa-pen-nib"></i>
                                        </div>
                                    @endif
                                </div>
                                <h3 class="member-card__name">{{ $painter->name }}</h3>
                                <div class="member-card__line"></div>
                                <p class="member-card__role">
                                    {{ $painter->approved_visual_works_count ?? 0 }} görsel eser
                                    <span class="member-card__separator">·</span>
                                    {{ number_format((int) ($painter->total_visual_views ?? 0)) }} görüntülenme
                                </p>
                            </article>
                        </a>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="member-no-result member-no-result--visible">
                            <i class="fa-solid fa-search member-no-result__icon"></i>
                            <p class="member-no-result__text">Aramanızla eşleşen ressam bulunamadı.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($painters->hasPages())
                <nav class="member-pagination" aria-label="Sayfalama">
                    @if($painters->onFirstPage())
                        <button class="member-pagination__btn member-pagination__btn--prev" type="button" disabled aria-label="Önceki sayfa">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                    @else
                        <a href="{{ $painters->previousPageUrl() }}" class="member-pagination__btn member-pagination__btn--prev" aria-label="Önceki sayfa">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                    @endif

                    <div class="member-pagination__pages">
                        @foreach($painters->getUrlRange(max(1, $painters->currentPage() - 2), min($painters->lastPage(), $painters->currentPage() + 2)) as $page => $url)
                            <a href="{{ $url }}" class="member-pagination__page {{ $page === $painters->currentPage() ? 'member-pagination__page--active' : '' }}">{{ $page }}</a>
                        @endforeach
                    </div>

                    @if($painters->hasMorePages())
                        <a href="{{ $painters->nextPageUrl() }}" class="member-pagination__btn member-pagination__btn--next" aria-label="Sonraki sayfa">
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

@push('scripts')
<script>
    document.querySelectorAll('.member-toolbar__sort-btn[data-dir]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('painterSortDir').value = this.dataset.dir;
        });
    });
</script>
@endpush
