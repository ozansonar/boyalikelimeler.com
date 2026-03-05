@extends('layouts.front')

@section('title', $query ? "\"$query\" arama sonuçları — Boyalı Kelimeler" : 'Ara — Boyalı Kelimeler')
@section('meta_description', $query ? "\"$query\" araması için sonuçlar." : 'Boyalı Kelimeler içerik arama sayfası.')
@section('canonical', route('search.index', $query ? ['q' => $query] : []))
@section('og_title', $query ? "\"$query\" arama sonuçları — Boyalı Kelimeler" : 'Ara — Boyalı Kelimeler')
@section('og_description', $query ? "\"$query\" araması için sonuçlar." : 'Boyalı Kelimeler içerik arama sayfası.')

@section('content')

    <!-- Search Hero -->
    <section class="search-hero" aria-label="Arama">
        <div class="container">
            <div class="search-hero__inner">
                <h1 class="search-hero__title">
                    <i class="fa-solid fa-magnifying-glass me-3"></i>Ara
                </h1>
                <p class="search-hero__desc">
                    Yazılar, blog içerikleri ve yazarlar arasında arama yapın.
                </p>
                <form class="search-hero__form" method="GET" action="{{ route('search.index') }}">
                    <div class="search-hero__input-wrap">
                        <i class="fa-solid fa-magnifying-glass search-hero__input-icon"></i>
                        <input type="text"
                               name="q"
                               class="search-hero__input"
                               placeholder="Ne aramak istiyorsunuz?"
                               value="{{ $query }}"
                               minlength="2"
                               autofocus>
                        <button type="submit" class="search-hero__btn">
                            <i class="fa-solid fa-arrow-right me-1"></i>Ara
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Search Results -->
    <section class="search-results" aria-label="Arama sonuçları">
        <div class="container">

            @if($query && mb_strlen($query) < 2)
                <div class="search-results__notice">
                    <i class="fa-solid fa-info-circle me-2"></i>Lütfen en az 2 karakter girin.
                </div>
            @elseif($results)

                <!-- Result Summary -->
                <div class="search-results__summary">
                    <span class="search-results__summary-text">
                        <i class="fa-solid fa-list me-1"></i>
                        "<strong>{{ $query }}</strong>" için <strong>{{ $results['total'] }}</strong> sonuç bulundu
                    </span>
                </div>

                @if($results['total'] === 0)
                    <div class="clist-empty">
                        <div class="clist-empty__icon">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </div>
                        <h3 class="clist-empty__title">Sonuç Bulunamadı</h3>
                        <p class="clist-empty__desc">
                            "<strong>{{ $query }}</strong>" araması için sonuç bulunamadı. Farklı anahtar kelimeler deneyin.
                        </p>
                        <a href="{{ route('search.index') }}" class="clist-empty__btn">
                            <i class="fa-solid fa-arrows-rotate me-2"></i>Yeni Arama Yap
                        </a>
                    </div>
                @endif

                <!-- Literary Works -->
                @if($results['works']->isNotEmpty())
                    <div class="search-results__section">
                        <h2 class="search-results__section-title">
                            <i class="fa-solid fa-feather-pointed me-2"></i>Yazılar
                            <span class="search-results__section-count">{{ $results['works']->count() }}</span>
                        </h2>
                        <div class="search-results__grid">
                            @foreach($results['works'] as $work)
                                <article class="clist-card">
                                    <a href="{{ route('literary-works.show', $work->slug) }}" class="clist-card__thumb-link">
                                        <div class="clist-card__thumb">
                                            @if($work->cover_image)
                                                <x-responsive-image :path="$work->cover_image" :alt="$work->title" size="md" class="clist-card__thumb-img" />
                                            @else
                                                <div class="clist-card__thumb-placeholder">
                                                    <i class="fa-solid fa-book-open"></i>
                                                </div>
                                            @endif
                                            @if($work->category)
                                                <span class="clist-card__category clist-card__category--{{ $work->category->slug }}">
                                                    <i class="fa-solid fa-tag me-1"></i>{{ $work->category->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </a>
                                    <div class="clist-card__body">
                                        <div class="clist-card__meta-top">
                                            @if($work->published_at)
                                                <time class="clist-card__date" datetime="{{ $work->published_at->toDateString() }}">
                                                    <i class="fa-regular fa-calendar me-1"></i>{{ $work->published_at->translatedFormat('d M Y') }}
                                                </time>
                                            @endif
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
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Blog Posts -->
                @if($results['posts']->isNotEmpty())
                    <div class="search-results__section">
                        <h2 class="search-results__section-title">
                            <i class="fa-solid fa-blog me-2"></i>Blog Yazıları
                            <span class="search-results__section-count">{{ $results['posts']->count() }}</span>
                        </h2>
                        <div class="search-results__grid">
                            @foreach($results['posts'] as $post)
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
                                            <p class="blog-card__excerpt">{{ Str::limit($post->excerpt, 120) }}</p>
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
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Authors -->
                @if($results['authors']->isNotEmpty())
                    <div class="search-results__section">
                        <h2 class="search-results__section-title">
                            <i class="fa-solid fa-users me-2"></i>Yazarlar
                            <span class="search-results__section-count">{{ $results['authors']->count() }}</span>
                        </h2>
                        <div class="search-results__authors">
                            @foreach($results['authors'] as $author)
                                <a href="{{ route('profile.show', $author->username) }}" class="search-results__author-card">
                                    <div class="search-results__author-avatar">
                                        @if($author->avatar_url)
                                            <img src="{{ $author->avatar_url }}" alt="{{ $author->name }}" loading="lazy">
                                        @else
                                            <i class="fa-solid fa-user"></i>
                                        @endif
                                    </div>
                                    <div class="search-results__author-info">
                                        <h3 class="search-results__author-name">{{ $author->name }}</h3>
                                        @if($author->bio)
                                            <p class="search-results__author-bio">{{ Str::limit($author->bio, 80) }}</p>
                                        @endif
                                        <span class="search-results__author-stat">
                                            <i class="fa-solid fa-feather-pointed me-1"></i>{{ $author->literary_works_count }} yazı
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            @endif

        </div>
    </section>

@endsection
