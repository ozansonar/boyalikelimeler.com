@extends('layouts.front')

@section('title', $user->name . ' — Yazar Profili | Boyalı Kelimeler')
@section('meta_description', $user->bio ?? $user->name . ' profili — Boyalı Kelimeler')
@section('canonical', route('profile.show', $user->username))
@section('og_title', $user->name . ' — Yazar Profili | Boyalı Kelimeler')
@section('og_description', $user->bio ?? $user->name . ' profili')

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
                            <span class="profile-header__badge profile-header__badge--gold">
                                <i class="fa-solid fa-feather-pointed me-1"></i>Yazar
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
                                    <i class="fa-solid fa-feather-pointed me-1"></i>Yazı Gönder
                                </a>
                            @endif
                        @endif
                    @endauth
                </div>
            </div>

            {{-- Stats Bar --}}
            <div class="profile-stats">
                @if($stats['approved_works'] > 0)
                    <div class="profile-stats__item">
                        <span class="profile-stats__number">{{ $stats['approved_works'] }}</span>
                        <span class="profile-stats__label">Eser</span>
                    </div>
                    <div class="profile-stats__divider"></div>
                @endif
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

                    {{-- Writer Application CTA Card (only for non-writers) --}}
                    @auth
                        @if(auth()->id() === $user->id && !$user->isYazar() && !$user->isAdmin() && !$user->isSuperAdmin())
                            <div class="writer-cta-card">
                                <div class="writer-cta-card__glow"></div>
                                <div class="writer-cta-card__icon">
                                    <i class="fa-solid fa-feather-pointed"></i>
                                </div>
                                <h4 class="writer-cta-card__title">Yazar Olmak İstiyor musunuz?</h4>
                                <p class="writer-cta-card__text">
                                    Eserlerinizi platformumuzda yayınlamak, topluluğumuzla buluşmak ve yarışmalara katılmak için yazar başvurusu yapın.
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
                    @if($works->isNotEmpty())
                        <div class="profile-card mb-3">
                            <h3 class="profile-card__title">
                                <i class="fa-solid fa-book-open me-2"></i>Edebiyat Eserleri
                                <span class="profile-tabs__count ms-2">{{ $stats['approved_works'] }}</span>
                            </h3>
                        </div>

                        @foreach($works as $work)
                            <article class="profile-post">
                                <div class="profile-post__inner">
                                    @if($work->cover_image)
                                        <div class="profile-post__thumb">
                                            <img src="{{ upload_url($work->cover_image) }}"
                                                 alt="{{ $work->title }} görseli"
                                                 class="profile-post__thumb-img"
                                                 loading="lazy">
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
                        @endforeach
                    @endif

                    {{-- Blog Yazıları --}}
                    @if($posts->isNotEmpty())
                        <div class="profile-card mb-3">
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
                                            <img src="{{ upload_url($post->cover_image) }}"
                                                 alt="{{ $post->title }} görseli"
                                                 class="profile-post__thumb-img"
                                                 loading="lazy">
                                            @if($post->category)
                                                <span class="profile-post__category">{{ $post->category->name }}</span>
                                            @endif
                                        </div>
                                    @endif
                                    <div class="profile-post__body">
                                        <h3 class="profile-post__title">
                                            <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
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
                    @endif

                    {{-- Boş durum --}}
                    @if($works->isEmpty() && $posts->isEmpty())
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
        @if(auth()->id() === $user->id && !$user->isYazar() && !$user->isAdmin() && !$user->isSuperAdmin())
            @include('front.profile._writer-modal')
        @endif
    @endauth

@endsection

@push('scripts')
    <script src="{{ asset('js/profile.js') }}"></script>
@endpush
