@extends('layouts.front')

@section('title', $user->name . ' — Yazar Profili | Boyalı Kelimeler')
@section('meta_description', $user->bio ?? $user->name . ' profili — Boyalı Kelimeler')
@section('canonical', route('profile.show', $user->username))
@section('og_title', $user->name . ' — Yazar Profili | Boyalı Kelimeler')
@section('og_description', $user->bio ?? $user->name . ' profili')
@section('og_type', 'profile')
@if($user->avatar_url)
    @section('og_image', $user->avatar_url)
@endif

@push('jsonld')
@php
    $socialLinks = array_values(array_filter([
        $user->instagram ? 'https://instagram.com/' . $user->instagram : null,
        $user->twitter ? 'https://x.com/' . $user->twitter : null,
        $user->youtube ? 'https://youtube.com/' . $user->youtube : null,
        $user->tiktok ? 'https://tiktok.com/@' . $user->tiktok : null,
        $user->website ?? null,
    ]));

    $personData = array_filter([
        '@type' => 'Person',
        'name' => $user->name,
        'url' => route('profile.show', $user->username),
        'image' => $user->avatar_url ?: null,
        'description' => $user->bio,
        'sameAs' => $socialLinks ?: null,
        'interactionStatistic' => [
            [
                '@type' => 'InteractionCounter',
                'interactionType' => 'https://schema.org/WriteAction',
                'userInteractionCount' => ($stats['approved_works'] ?? 0) + ($stats['published_posts'] ?? 0),
            ],
        ],
    ]);
@endphp
<script type="application/ld+json">
{!! json_encode([
    '@@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'ProfilePage',
            'dateModified' => $user->updated_at->toIso8601String(),
            'mainEntity' => $personData,
        ],
        [
            '@type' => 'BreadcrumbList',
            'name' => 'Breadcrumb',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Ana Sayfa', 'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Yazarlar', 'item' => route('authors.index')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $user->name],
            ],
        ],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush

@section('content')

    {{-- Cover + Profile Header --}}
    <section class="profile-cover" aria-label="Yazar kapak görseli">
        <div class="profile-cover__image">
            @if($user->cover_image_url)
                <img src="{{ $user->cover_image_url }}"
                     alt="{{ $user->name }} kapak görseli"
                     class="profile-cover__img"
                     loading="lazy">
            @else
                <img src="https://picsum.photos/1920/400?random={{ $user->id }}"
                     alt="{{ $user->name }} kapak görseli"
                     class="profile-cover__img"
                     loading="lazy">
            @endif
            <div class="profile-cover__overlay"></div>
        </div>
    </section>

    <section class="profile-header" aria-label="Yazar bilgileri">
        <div class="container">
            <div class="profile-header__inner">
                {{-- Profile Photo --}}
                <div class="profile-header__avatar-wrap">
                    <div class="profile-header__avatar">
                        @if($user->avatar_url)
                            <img src="{{ $user->avatar_url }}"
                                 alt="{{ $user->name }} profil fotoğrafı"
                                 class="profile-header__avatar-img"
                                 loading="lazy">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=200&background=D4AF37&color=1A1A1E&bold=true"
                                 alt="{{ $user->name }} profil fotoğrafı"
                                 class="profile-header__avatar-img"
                                 loading="lazy">
                        @endif
                    </div>
                </div>

                {{-- Name + Badge + Bio --}}
                <div class="profile-header__info">
                    <div class="profile-header__name-row">
                        <h1 class="profile-header__name">{{ $user->name }}</h1>
                        @if($user->isYazar())
                            @php
                                $hasWritten = ($stats['work_type_counts'][\App\Enums\LiteraryWorkType::Written->value] ?? 0) > 0;
                                $hasVisual  = ($stats['work_type_counts'][\App\Enums\LiteraryWorkType::Visual->value] ?? 0) > 0;

                                if ($hasWritten && $hasVisual) {
                                    $roleLabel = 'Yazar ve Ressam';
                                    $roleIcon  = 'fa-solid fa-feather-pointed';
                                } elseif ($hasVisual) {
                                    $roleLabel = 'Ressam';
                                    $roleIcon  = 'fa-solid fa-palette';
                                } else {
                                    $roleLabel = 'Yazar';
                                    $roleIcon  = 'fa-solid fa-feather-pointed';
                                }
                            @endphp
                            <span class="profile-header__badge profile-header__badge--gold">
                                <i class="{{ $roleIcon }} me-1"></i>{{ $roleLabel }}
                            </span>
                        @endif
                        @if($user->isAdmin() || $user->isSuperAdmin())
                            <span class="profile-header__badge profile-header__badge--silver">
                                <i class="fa-solid fa-shield-halved me-1"></i>Editör
                            </span>
                        @endif
                    </div>
                    @if($user->username)
                        <p class="profile-header__username">{{ '@' . $user->username }}</p>
                    @endif
                    @if($user->bio)
                        <p class="profile-header__bio">{{ $user->bio }}</p>
                    @endif
                </div>

                {{-- Action Buttons --}}
                <div class="profile-header__actions">
                    @auth
                        @if(auth()->id() === $user->id)
                            <a href="{{ route('profile.edit') }}" class="profile-header__btn profile-header__btn--outline">
                                <i class="fa-solid fa-pen-to-square me-1"></i>Düzenle
                            </a>
                            @if($user->isYazar() || $user->isAdmin() || $user->isSuperAdmin())
                                <a href="{{ route('myposts.index') }}" class="profile-header__btn profile-header__btn--outline">
                                    <i class="fa-solid fa-list me-1"></i>Yazılarım
                                </a>
                                <a href="{{ route('myposts.create') }}" class="profile-header__btn profile-header__btn--primary">
                                    <i class="fa-solid fa-feather-pointed me-1"></i>Eser gönder
                                </a>
                            @endif
                        @endif
                    @endauth
                </div>
            </div>

            {{-- Stats Bar --}}
            <div class="profile-stats">
                @foreach($stats['work_type_counts'] as $typeValue => $typeCount)
                    <div class="profile-stats__item">
                        <span class="profile-stats__number">{{ $typeCount }}</span>
                        <span class="profile-stats__label">{{ \App\Enums\LiteraryWorkType::from($typeValue)->label() }}</span>
                    </div>
                    <div class="profile-stats__divider"></div>
                @endforeach
                <div class="profile-stats__item">
                    <span class="profile-stats__number">{{ $stats['published_posts'] }}</span>
                    <span class="profile-stats__label">Yazı</span>
                </div>
                <div class="profile-stats__divider"></div>
                <div class="profile-stats__item">
                    <span class="profile-stats__number">{{ number_format($stats['total_views'] + $stats['total_work_views']) }}</span>
                    <span class="profile-stats__label">Görüntülenme</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Content Area (Two Column) --}}
    <section class="profile-content" aria-label="Yazar içerikleri">
        <div class="container">
            <div class="row g-4">

                {{-- LEFT: Sidebar --}}
                <div class="col-lg-4 order-lg-1 order-2">

                    {{-- Writer Application CTA Card --}}
                    @auth
                        @if(auth()->id() === $user->id && $writerStatus !== null)
                            @if($writerStatus['can_apply'])
                                <div class="writer-cta-card">
                                    <div class="writer-cta-card__glow"></div>
                                    <div class="writer-cta-card__icon">
                                        <i class="fa-solid fa-feather-pointed"></i>
                                    </div>
                                    <h4 class="writer-cta-card__title">Yazar Olmak İstiyor musunuz?</h4>
                                    <p class="writer-cta-card__text">
                                        Eserlerinizi platformumuzda yayınlamak, topluluğumuzla buluşmak ve yarışmalara katılmak için yazar & ressam başvurusu yapın.
                                    </p>
                                    <button type="button"
                                            class="writer-cta-card__btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#writerApplicationModal"
                                            aria-haspopup="dialog">
                                        <i class="fa-solid fa-paper-plane me-2"></i>Yazar Olma İsteğinde Bulun
                                    </button>
                                    <div class="writer-cta-card__note">
                                        <i class="fa-solid fa-shield-halved me-1"></i>Başvurunuz 3–5 iş günü içinde değerlendirilir
                                    </div>
                                </div>
                            @elseif($writerStatus['reason'] === 'pending')
                                <div class="writer-cta-card writer-cta-card--pending">
                                    <div class="writer-cta-card__icon">
                                        <i class="fa-solid fa-hourglass-half"></i>
                                    </div>
                                    <h4 class="writer-cta-card__title">Başvurunuz Değerlendiriliyor</h4>
                                    <p class="writer-cta-card__text">
                                        Yazar & Ressam başvurunuz editör ekibimiz tarafından incelenmektedir. Sonuç e-posta ile bildirilecektir.
                                    </p>
                                    <div class="writer-cta-card__note">
                                        <i class="fa-solid fa-calendar me-1"></i>Başvuru tarihi: {{ $writerStatus['last_application']->created_at->format('d.m.Y') }}
                                    </div>
                                </div>
                            @elseif($writerStatus['reason'] === 'cooldown')
                                @php
                                    $daysLeft = 30 - (int) $writerStatus['last_application']->reviewed_at->diffInDays(now());
                                @endphp
                                <div class="writer-cta-card writer-cta-card--rejected">
                                    <div class="writer-cta-card__icon">
                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                    </div>
                                    <h4 class="writer-cta-card__title">Başvurunuz Reddedildi</h4>
                                    @if($writerStatus['last_application']->admin_note)
                                        <p class="writer-cta-card__text">
                                            <strong>Değerlendirme notu:</strong> {{ $writerStatus['last_application']->admin_note }}
                                        </p>
                                    @endif
                                    <div class="writer-cta-card__note">
                                        <i class="fa-solid fa-hourglass me-1"></i>{{ $daysLeft }} gün sonra tekrar başvurabilirsiniz
                                    </div>
                                </div>
                            @endif
                        @endif
                    @endauth

                    {{-- About Card --}}
                    @if($user->about)
                        <div class="profile-card">
                            <h3 class="profile-card__title">
                                <i class="fa-solid fa-user me-2"></i>Hakkında
                            </h3>
                            <p class="profile-card__text">{{ $user->about }}</p>
                        </div>
                    @endif

                    {{-- Info Card --}}
                    <div class="profile-card">
                        <h3 class="profile-card__title">
                            <i class="fa-solid fa-circle-info me-2"></i>Bilgiler
                        </h3>
                        <ul class="profile-info-list">
                            <li class="profile-info-list__item">
                                <i class="fa-solid fa-calendar-days profile-info-list__icon"></i>
                                <div>
                                    <span class="profile-info-list__label">Üyelik Tarihi</span>
                                    <span class="profile-info-list__value">{{ $user->created_at->translatedFormat('d F Y') }}</span>
                                </div>
                            </li>
                            @if($user->location)
                                <li class="profile-info-list__item">
                                    <i class="fa-solid fa-location-dot profile-info-list__icon"></i>
                                    <div>
                                        <span class="profile-info-list__label">Konum</span>
                                        <span class="profile-info-list__value">{{ $user->location }}</span>
                                    </div>
                                </li>
                            @endif
                            @if($user->show_email && $user->email)
                                <li class="profile-info-list__item">
                                    <i class="fa-solid fa-envelope profile-info-list__icon"></i>
                                    <div>
                                        <span class="profile-info-list__label">E-posta</span>
                                        <span class="profile-info-list__value">{{ $user->email }}</span>
                                    </div>
                                </li>
                            @endif
                            @if($user->website)
                                <li class="profile-info-list__item">
                                    <i class="fa-solid fa-globe profile-info-list__icon"></i>
                                    <div>
                                        <span class="profile-info-list__label">Website</span>
                                        <a href="{{ $user->website }}" class="profile-info-list__value profile-info-list__link" target="_blank" rel="noopener noreferrer">{{ parse_url($user->website, PHP_URL_HOST) }}</a>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>

                    {{-- Social Media Card --}}
                    @if($user->instagram || $user->twitter || $user->youtube || $user->tiktok)
                        <div class="profile-card">
                            <h3 class="profile-card__title">
                                <i class="fa-solid fa-share-nodes me-2"></i>Sosyal Medya
                            </h3>
                            <div class="profile-social">
                                @if($user->instagram)
                                    <a href="https://instagram.com/{{ $user->instagram }}" class="profile-social__link profile-social__link--instagram" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                                        <i class="fa-brands fa-instagram"></i>
                                        <span>{{ '@' . $user->instagram }}</span>
                                    </a>
                                @endif
                                @if($user->twitter)
                                    <a href="https://x.com/{{ $user->twitter }}" class="profile-social__link profile-social__link--twitter" target="_blank" rel="noopener noreferrer" aria-label="Twitter">
                                        <i class="fa-brands fa-x-twitter"></i>
                                        <span>{{ '@' . $user->twitter }}</span>
                                    </a>
                                @endif
                                @if($user->youtube)
                                    <a href="https://youtube.com/@{{ $user->youtube }}" class="profile-social__link profile-social__link--youtube" target="_blank" rel="noopener noreferrer" aria-label="YouTube">
                                        <i class="fa-brands fa-youtube"></i>
                                        <span>{{ $user->youtube }}</span>
                                    </a>
                                @endif
                                @if($user->tiktok)
                                    <a href="https://tiktok.com/@{{ $user->tiktok }}" class="profile-social__link profile-social__link--tiktok" target="_blank" rel="noopener noreferrer" aria-label="TikTok">
                                        <i class="fa-brands fa-tiktok"></i>
                                        <span>{{ '@' . $user->tiktok }}</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Interest Tags --}}
                    @if($user->interests && count($user->interests) > 0)
                        <div class="profile-card">
                            <h3 class="profile-card__title">
                                <i class="fa-solid fa-tags me-2"></i>İlgi Alanları
                            </h3>
                            <div class="profile-tags">
                                @foreach($user->interests as $interest)
                                    <span class="profile-tags__item">{{ $interest }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

                {{-- RIGHT: Content Feed --}}
                <div class="col-lg-8 order-lg-2 order-1">

                    {{-- Edebiyat Eserleri --}}
                    @if($works->isNotEmpty() || $works->currentPage() > 1)
                        <div class="profile-card mb-3" id="eserler">
                            <h3 class="profile-card__title">
                                <i class="fa-solid fa-book-open me-2"></i>Edebiyat Eserleri
                                @foreach($stats['work_type_counts'] as $typeValue => $typeCount)
                                    <span class="profile-tabs__count ms-2">{{ \App\Enums\LiteraryWorkType::from($typeValue)->label() }}: {{ $typeCount }}</span>
                                @endforeach
                            </h3>
                        </div>

                        @foreach($works as $work)
                            <a href="{{ route('literary-works.show', $work->slug) }}" class="profile-post__link">
                                <article class="profile-post">
                                    <div class="profile-post__inner">
                                        @if($work->cover_image)
                                            <div class="profile-post__thumb">
                                                <x-responsive-image :path="$work->cover_image" :alt="$work->title . ' görseli'" size="sm" class="profile-post__thumb-img" />
                                                @if($work->category)
                                                    <span class="profile-post__category">{{ $work->category->name }}</span>
                                                @endif
                                            </div>
                                        @else
                                            @if($work->category)
                                                <div class="profile-post__thumb">
                                                    <div class="profile-post__thumb-placeholder">
                                                        <i class="fa-solid fa-feather-pointed fa-2x"></i>
                                                    </div>
                                                    <span class="profile-post__category">{{ $work->category->name }}</span>
                                                </div>
                                            @endif
                                        @endif
                                        <div class="profile-post__body">
                                            <h3 class="profile-post__title">{{ $work->title }}</h3>
                                            @if($work->excerpt)
                                                <p class="profile-post__excerpt">{{ Str::limit($work->excerpt, 200) }}</p>
                                            @endif
                                            <div class="profile-post__meta">
                                                <span class="profile-post__date">
                                                    <i class="fa-regular fa-calendar me-1"></i>{{ $work->published_at?->translatedFormat('d F Y') }}
                                                </span>
                                                <span class="profile-post__read-time">
                                                    <i class="fa-regular fa-clock me-1"></i>{{ $work->readingTime() }} dk okuma
                                                </span>
                                            </div>
                                            <div class="profile-post__stats">
                                                <span class="profile-post__stat">
                                                    <i class="fa-regular fa-eye me-1"></i>{{ number_format($work->view_count) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </a>
                        @endforeach

                        @if($works->hasPages())
                            <nav class="member-pagination my-4" aria-label="Eser sayfalama">
                                @if($works->onFirstPage())
                                    <button class="member-pagination__btn member-pagination__btn--prev" type="button" disabled aria-label="Önceki sayfa">
                                        <i class="fa-solid fa-chevron-left"></i>
                                    </button>
                                @else
                                    <a href="{{ $works->previousPageUrl() }}#eserler" class="member-pagination__btn member-pagination__btn--prev" aria-label="Önceki sayfa">
                                        <i class="fa-solid fa-chevron-left"></i>
                                    </a>
                                @endif

                                <div class="member-pagination__pages">
                                    @foreach($works->getUrlRange(max(1, $works->currentPage() - 2), min($works->lastPage(), $works->currentPage() + 2)) as $page => $url)
                                        <a href="{{ $url }}#eserler" class="member-pagination__page {{ $page === $works->currentPage() ? 'member-pagination__page--active' : '' }}">{{ $page }}</a>
                                    @endforeach
                                </div>

                                @if($works->hasMorePages())
                                    <a href="{{ $works->nextPageUrl() }}#eserler" class="member-pagination__btn member-pagination__btn--next" aria-label="Sonraki sayfa">
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </a>
                                @else
                                    <button class="member-pagination__btn member-pagination__btn--next" type="button" disabled aria-label="Sonraki sayfa">
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </button>
                                @endif
                            </nav>
                        @endif
                    @endif

                    {{-- Blog Yazıları --}}
                    @if($posts->isNotEmpty() || $posts->currentPage() > 1)
                        <div class="profile-card mb-3" id="yazilar">
                            <h3 class="profile-card__title">
                                <i class="fa-solid fa-newspaper me-2"></i>Blog Yazıları
                                <span class="profile-tabs__count ms-2">{{ $stats['published_posts'] }}</span>
                            </h3>
                        </div>

                        @foreach($posts as $post)
                            <article class="profile-post">
                                <div class="profile-post__inner">
                                    @if($post->cover_image)
                                        <div class="profile-post__thumb">
                                            <x-responsive-image :path="$post->cover_image" :alt="$post->title . ' görseli'" size="sm" class="profile-post__thumb-img" />
                                            @if($post->category)
                                                <span class="profile-post__category">{{ $post->category->name }}</span>
                                            @endif
                                        </div>
                                    @endif
                                    <div class="profile-post__body">
                                        <h3 class="profile-post__title">
                                            <a href="{{ $post->url() }}">{{ $post->title }}</a>
                                        </h3>
                                        @if($post->excerpt)
                                            <p class="profile-post__excerpt">{{ Str::limit($post->excerpt, 200) }}</p>
                                        @endif
                                        <div class="profile-post__meta">
                                            <span class="profile-post__date">
                                                <i class="fa-regular fa-calendar me-1"></i>{{ $post->published_at?->translatedFormat('d F Y') }}
                                            </span>
                                            <span class="profile-post__read-time">
                                                <i class="fa-regular fa-clock me-1"></i>{{ $post->readingTime() }} dk okuma
                                            </span>
                                        </div>
                                        <div class="profile-post__stats">
                                            <span class="profile-post__stat">
                                                <i class="fa-regular fa-eye me-1"></i>{{ number_format($post->view_count) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endforeach

                        @if($posts->hasPages())
                            <nav class="member-pagination my-4" aria-label="Yazı sayfalama">
                                @if($posts->onFirstPage())
                                    <button class="member-pagination__btn member-pagination__btn--prev" type="button" disabled aria-label="Önceki sayfa">
                                        <i class="fa-solid fa-chevron-left"></i>
                                    </button>
                                @else
                                    <a href="{{ $posts->previousPageUrl() }}#yazilar" class="member-pagination__btn member-pagination__btn--prev" aria-label="Önceki sayfa">
                                        <i class="fa-solid fa-chevron-left"></i>
                                    </a>
                                @endif

                                <div class="member-pagination__pages">
                                    @foreach($posts->getUrlRange(max(1, $posts->currentPage() - 2), min($posts->lastPage(), $posts->currentPage() + 2)) as $page => $url)
                                        <a href="{{ $url }}#yazilar" class="member-pagination__page {{ $page === $posts->currentPage() ? 'member-pagination__page--active' : '' }}">{{ $page }}</a>
                                    @endforeach
                                </div>

                                @if($posts->hasMorePages())
                                    <a href="{{ $posts->nextPageUrl() }}#yazilar" class="member-pagination__btn member-pagination__btn--next" aria-label="Sonraki sayfa">
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </a>
                                @else
                                    <button class="member-pagination__btn member-pagination__btn--next" type="button" disabled aria-label="Sonraki sayfa">
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </button>
                                @endif
                            </nav>
                        @endif
                    @endif

                    {{-- Beğenilen Eserler --}}
                    @if($favoriteWorks->isNotEmpty())
                        <div class="profile-card mb-3">
                            <h3 class="profile-card__title">
                                <i class="fa-solid fa-heart me-2"></i>Beğendiği Eserler
                                <span class="profile-tabs__count ms-2">{{ $favoriteWorks->count() }}</span>
                            </h3>
                        </div>

                        @foreach($favoriteWorks as $favWork)
                            <a href="{{ route('literary-works.show', $favWork->slug) }}" class="profile-post__link">
                                <article class="profile-post">
                                    <div class="profile-post__inner">
                                        @if($favWork->cover_image)
                                            <div class="profile-post__thumb">
                                                <x-responsive-image :path="$favWork->cover_image" :alt="$favWork->title . ' görseli'" size="sm" class="profile-post__thumb-img" />
                                                @if($favWork->category)
                                                    <span class="profile-post__category">{{ $favWork->category->name }}</span>
                                                @endif
                                            </div>
                                        @endif
                                        <div class="profile-post__body">
                                            <h3 class="profile-post__title">{{ $favWork->title }}</h3>
                                            @if($favWork->excerpt)
                                                <p class="profile-post__excerpt">{{ Str::limit($favWork->excerpt, 200) }}</p>
                                            @endif
                                            <div class="profile-post__meta">
                                                <span class="profile-post__date">
                                                    <i class="fa-regular fa-calendar me-1"></i>{{ $favWork->published_at?->translatedFormat('d F Y') }}
                                                </span>
                                                <span class="profile-post__read-time">
                                                    <i class="fa-solid fa-user me-1"></i>{{ $favWork->author->name }}
                                                </span>
                                            </div>
                                            <div class="profile-post__stats">
                                                <span class="profile-post__stat">
                                                    <i class="fa-solid fa-heart me-1"></i>{{ $favWork->favorites()->count() }}
                                                </span>
                                                <span class="profile-post__stat">
                                                    <i class="fa-regular fa-eye me-1"></i>{{ number_format($favWork->view_count) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </a>
                        @endforeach
                    @endif

                    {{-- Beğenilen Blog Yazıları --}}
                    @if($favoritePosts->isNotEmpty())
                        <div class="profile-card mb-3">
                            <h3 class="profile-card__title">
                                <i class="fa-solid fa-heart me-2"></i>Beğendiği Yazılar
                                <span class="profile-tabs__count ms-2">{{ $favoritePosts->count() }}</span>
                            </h3>
                        </div>

                        @foreach($favoritePosts as $favPost)
                            <article class="profile-post">
                                <div class="profile-post__inner">
                                    @if($favPost->cover_image)
                                        <div class="profile-post__thumb">
                                            <x-responsive-image :path="$favPost->cover_image" :alt="$favPost->title . ' görseli'" size="sm" class="profile-post__thumb-img" />
                                            @if($favPost->category)
                                                <span class="profile-post__category">{{ $favPost->category->name }}</span>
                                            @endif
                                        </div>
                                    @endif
                                    <div class="profile-post__body">
                                        <h3 class="profile-post__title">
                                            <a href="{{ $favPost->url() }}">{{ $favPost->title }}</a>
                                        </h3>
                                        @if($favPost->excerpt)
                                            <p class="profile-post__excerpt">{{ Str::limit($favPost->excerpt, 200) }}</p>
                                        @endif
                                        <div class="profile-post__meta">
                                            <span class="profile-post__date">
                                                <i class="fa-regular fa-calendar me-1"></i>{{ $favPost->published_at?->translatedFormat('d F Y') }}
                                            </span>
                                            <span class="profile-post__read-time">
                                                <i class="fa-solid fa-user me-1"></i>{{ $favPost->author->name }}
                                            </span>
                                        </div>
                                        <div class="profile-post__stats">
                                            <span class="profile-post__stat">
                                                <i class="fa-solid fa-heart me-1"></i>{{ $favPost->favorites()->count() }}
                                            </span>
                                            <span class="profile-post__stat">
                                                <i class="fa-regular fa-eye me-1"></i>{{ number_format($favPost->view_count) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    @endif

                    {{-- Boş durum --}}
                    @if($works->total() === 0 && $posts->total() === 0)
                        <div class="profile-card text-center">
                            <i class="fa-solid fa-feather-pointed fa-3x mb-3" aria-hidden="true"></i>
                            <p class="profile-card__text">Henüz yayınlanmış içerik bulunmuyor.</p>
                            @auth
                                @if(auth()->id() === $user->id && ($user->isYazar() || $user->isAdmin() || $user->isSuperAdmin()))
                                    <a href="{{ route('myposts.create') }}" class="profile-header__btn profile-header__btn--primary mt-3">
                                        <i class="fa-solid fa-feather-pointed me-1"></i>İlk Eserini Gönder
                                    </a>
                                @endif
                            @endauth
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </section>

    {{-- Writer Application Modal --}}
    @auth
        @if(auth()->id() === $user->id && ($writerStatus['can_apply'] ?? false))
            @include('front.profile._writer-modal')
        @endif
    @endauth

@endsection

@push('scripts')
    <script src="{{ asset('js/profile.js') }}?v={{ filemtime(public_path('js/profile.js')) }}"></script>
@endpush
