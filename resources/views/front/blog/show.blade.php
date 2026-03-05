@extends('layouts.front')

@section('title', ($post->meta_title ?: $post->title) . ' — Boyalı Kelimeler Blog')
@section('meta_description', $post->meta_description ?: Str::limit(strip_tags((string) $post->excerpt), 160))
@section('canonical', route('blog.show', $post->slug))
@section('og_title', ($post->meta_title ?: $post->title) . ' — Boyalı Kelimeler Blog')
@section('og_description', $post->meta_description ?: Str::limit(strip_tags((string) $post->excerpt), 160))
@section('og_type', 'article')
@if($post->cover_image)
    @section('og_image', asset('uploads/' . $post->cover_image))
@endif

@push('og_meta')
    @if($post->published_at)
        <meta property="article:published_time" content="{{ $post->published_at->toIso8601String() }}">
    @endif
    <meta property="article:modified_time" content="{{ $post->updated_at->toIso8601String() }}">
    @if($post->category)
        <meta property="article:section" content="{{ $post->category->name }}">
    @endif
@endpush

@push('jsonld')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@graph' => [
        array_filter([
            '@type' => 'BlogPosting',
            'headline' => $post->meta_title ?: $post->title,
            'description' => $post->meta_description ?: Str::limit(strip_tags((string) $post->excerpt), 160),
            'image' => $post->cover_image ? asset('uploads/' . $post->cover_image) : asset('images/og-cover.jpg'),
            'datePublished' => $post->published_at?->toIso8601String(),
            'dateModified' => $post->updated_at->toIso8601String(),
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Boyalı Kelimeler',
                'url' => url('/'),
                'logo' => ['@type' => 'ImageObject', 'url' => asset('images/logo.svg')],
            ],
            'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => route('blog.show', $post->slug)],
            'articleSection' => $post->category?->name,
            'wordCount' => preg_match_all('/\pL+/u', strip_tags((string) $post->body)),
        ]),
        [
            '@type' => 'BreadcrumbList',
            'itemListElement' => array_values(array_filter([
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Ana Sayfa', 'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => route('blog.index')],
                $post->category ? ['@type' => 'ListItem', 'position' => 3, 'name' => $post->category->name, 'item' => route('blog.index', ['kategori' => $post->category->slug])] : null,
                ['@type' => 'ListItem', 'position' => $post->category ? 4 : 3, 'name' => $post->title],
            ])),
        ],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush

@section('content')

    <!-- Page Header -->
    <section class="blog-page-header blog-page-header--detail" aria-label="Blog detay sayfa başlığı">
        <div class="container">
            <!-- Breadcrumb -->
            <nav class="blog-page-header__breadcrumb" aria-label="Breadcrumb">
                <ol class="blog-page-header__breadcrumb-list">
                    <li class="blog-page-header__breadcrumb-item">
                        <a href="{{ url('/') }}" class="blog-page-header__breadcrumb-link">
                            <i class="fa-solid fa-house"></i>
                        </a>
                    </li>
                    <li class="blog-page-header__breadcrumb-sep">
                        <i class="fa-solid fa-chevron-right"></i>
                    </li>
                    <li class="blog-page-header__breadcrumb-item">
                        <a href="{{ route('blog.index') }}" class="blog-page-header__breadcrumb-link">Blog</a>
                    </li>
                    @if($post->category)
                        <li class="blog-page-header__breadcrumb-sep">
                            <i class="fa-solid fa-chevron-right"></i>
                        </li>
                        <li class="blog-page-header__breadcrumb-item">
                            <a href="{{ route('blog.index', ['kategori' => $post->category->slug]) }}" class="blog-page-header__breadcrumb-link">{{ $post->category->name }}</a>
                        </li>
                    @endif
                    <li class="blog-page-header__breadcrumb-sep">
                        <i class="fa-solid fa-chevron-right"></i>
                    </li>
                    <li class="blog-page-header__breadcrumb-item blog-page-header__breadcrumb-item--active" aria-current="page">
                        {{ Str::limit($post->title, 40) }}
                    </li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Article Section -->
    <article class="blogd-section">
        <div class="container">
            <div class="row g-4">

                <!-- SOL KOLON — ANA İÇERİK -->
                <div class="col-lg-8">

                    <!-- Article Header -->
                    <header class="blogd-header">
                        @if($post->category)
                            <span class="blog-badge blogd-header__category">
                                <i class="fa-solid fa-folder me-1"></i>{{ $post->category->name }}
                            </span>
                        @endif

                        <h1 class="blogd-header__title">
                            {{ $post->title }}
                        </h1>

                        <div class="blogd-header__meta">
                            @if($post->published_at)
                                <time class="blogd-header__date" datetime="{{ $post->published_at->toDateString() }}">
                                    <i class="fa-regular fa-calendar me-1"></i>{{ $post->published_at->translatedFormat('d F Y') }}
                                </time>
                            @endif
                            <span class="blogd-header__read-time">
                                <i class="fa-regular fa-clock me-1"></i>{{ $post->readingTime() }} dk okuma
                            </span>
                            <span class="blogd-header__views">
                                <i class="fa-solid fa-eye me-1"></i>{{ number_format($post->view_count) }} okunma
                            </span>
                        </div>
                    </header>

                    <!-- Cover Image -->
                    @if($post->cover_image)
                        <div class="blogd-cover">
                            <img src="{{ asset('uploads/' . $post->cover_image) }}"
                                 alt="{{ $post->title }}"
                                 class="blogd-cover__img img-fluid"
                                 loading="lazy">
                        </div>
                    @else
                        <div class="blogd-cover">
                            <div class="blogd-cover__placeholder">
                                <i class="fa-solid fa-newspaper"></i>
                                <span>Kapak Görseli</span>
                            </div>
                        </div>
                    @endif

                    <!-- Article Content -->
                    <div class="blogd-content">
                        @if($post->excerpt)
                            <p class="blogd-content__lead">
                                {{ $post->excerpt }}
                            </p>
                        @endif

                        {!! $post->body !!}
                    </div>

                    <!-- Action Bar (Like + Share) -->
                    <div class="blogd-actions">
                        <div class="blogd-actions__left">
                            <button type="button"
                                    class="blogd-actions__like-btn js-favorite-btn {{ $post->isFavoritedBy() ? 'blogd-actions__like-btn--liked' : '' }}"
                                    data-type="post"
                                    data-id="{{ $post->id }}"
                                    @guest data-login-required="true" @endguest
                                    aria-label="Beğen">
                                <i class="{{ $post->isFavoritedBy() ? 'fa-solid' : 'fa-regular' }} fa-heart me-1"></i>
                                <span class="js-favorite-text">{{ $post->isFavoritedBy() ? 'Beğenildi' : 'Beğen' }}</span>
                                <span class="blogd-actions__count js-favorite-count">{{ $post->favorites_count ?? $post->favorites()->count() }}</span>
                            </button>
                        </div>
                        <div class="blogd-actions__right">
                            @include('partials.front.share-buttons', [
                                'shareUrl' => route('blog.show', $post->slug),
                                'shareTitle' => $post->title,
                            ])
                        </div>
                    </div>

                    <!-- Comment Section -->
                    @include('partials.front.comment-section', ['commentable' => $post, 'commentableType' => 'post'])

                </div>

                <!-- SAĞ KOLON — SIDEBAR -->
                <aside class="col-lg-4">
                    <div class="blogd-sidebar">

                        <!-- Categories -->
                        <div class="blogd-sidebar__card">
                            <h4 class="blogd-sidebar__title">
                                <i class="fa-solid fa-folder-open me-2"></i>Kategoriler
                            </h4>
                            <ul class="blogd-sidebar__cat-list">
                                @foreach($categories as $cat)
                                    <li>
                                        <a href="{{ route('blog.index', ['kategori' => $cat->slug]) }}" class="blogd-sidebar__cat-link">
                                            <i class="fa-solid fa-folder me-2"></i>{{ $cat->name }}
                                            <span class="blogd-sidebar__cat-count">{{ $cat->posts()->where('status', 'published')->count() }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Popular Posts -->
                        @if($popularPosts->isNotEmpty())
                        <div class="blogd-sidebar__card">
                            <h4 class="blogd-sidebar__title">
                                <i class="fa-solid fa-fire me-2"></i>Popüler Yazılar
                            </h4>
                            <div class="blogd-sidebar__popular">
                                @foreach($popularPosts as $popular)
                                    <a href="{{ route('blog.show', $popular->slug) }}" class="blogd-sidebar__popular-item">
                                        <div class="blogd-sidebar__popular-thumb">
                                            <i class="fa-solid fa-newspaper"></i>
                                        </div>
                                        <div class="blogd-sidebar__popular-info">
                                            <h5 class="blogd-sidebar__popular-title">{{ $popular->title }}</h5>
                                            <span class="blogd-sidebar__popular-meta">
                                                <i class="fa-solid fa-eye me-1"></i>{{ number_format($popular->view_count) }}
                                                <span class="mx-1">&middot;</span>
                                                @if($popular->published_at)
                                                    {{ $popular->published_at->translatedFormat('d M Y') }}
                                                @endif
                                            </span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        @endif

                    </div>
                </aside>

            </div>
        </div>
    </article>

@endsection

@push('scripts')
    <script src="{{ asset('js/favorite.js') }}?v={{ filemtime(public_path('js/favorite.js')) }}"></script>
@endpush
