@extends('layouts.front')

@section('title', ($page->meta_title ?: $page->title) . ' — Boyalı Kelimeler')
@section('meta_description', $page->meta_description ?: Str::limit(strip_tags((string) $page->excerpt), 160))
@section('canonical', route('page.show', $page->slug))
@section('og_title', ($page->meta_title ?: $page->title) . ' — Boyalı Kelimeler')
@section('og_description', $page->meta_description ?: Str::limit(strip_tags((string) $page->excerpt), 160))
@if($page->cover_image)
    @section('og_image', asset('uploads/' . $page->cover_image))
@endif

@push('jsonld')
<script type="application/ld+json">
{!! json_encode([
    '@@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'WebPage',
            'name' => $page->meta_title ?: $page->title,
            'description' => $page->meta_description ?: Str::limit(strip_tags((string) $page->excerpt), 160),
            'url' => route('page.show', $page->slug),
            'dateModified' => $page->updated_at->toIso8601String(),
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Boyalı Kelimeler',
                'url' => url('/'),
            ],
        ],
        [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Ana Sayfa', 'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => $page->title],
            ],
        ],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush

@section('content')

    <!-- Page Header -->
    <section class="page-header" aria-label="Sayfa başlığı">
        <div class="container">
            <h1 class="page-header__title">{{ $page->title }}</h1>
            <div class="page-header__divider"></div>
            @if($page->excerpt)
                <p class="page-header__desc">
                    {{ $page->excerpt }}
                </p>
            @endif
        </div>
    </section>

    <!-- Page Content -->
    <section class="static-page-section" aria-label="Sayfa içeriği">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-8">

                    @if($page->cover_image)
                        <div class="static-page-cover mb-4">
                            <img src="{{ asset('uploads/' . $page->cover_image) }}"
                                 alt="{{ $page->title }}"
                                 class="static-page-cover__img img-fluid rounded"
                                 loading="lazy">
                        </div>
                    @endif

                    <article class="static-page-content">
                        {!! $page->body !!}
                    </article>

                </div>
            </div>

            @if($page->boxes->count())
                <div class="page-boxes-section mt-5">
                    <div class="row g-4 justify-content-center">
                        @foreach($page->boxes as $box)
                            <div class="{{ $box->bootstrapColClass() }}">
                                @if($box->resolvedUrl())
                                    <a href="{{ $box->resolvedUrl() }}" target="{{ $box->link_target->value }}" class="page-box-card" rel="{{ $box->link_target === App\Enums\LinkTarget::Blank ? 'noopener noreferrer' : '' }}">
                                @else
                                    <div class="page-box-card">
                                @endif

                                    @if($box->isVideo() && $box->youtubeId())
                                        <div class="page-box-card__video-wrap">
                                            <div class="ratio ratio-16x9">
                                                <iframe src="https://www.youtube.com/embed/{{ $box->youtubeId() }}"
                                                        title="{{ $box->title }}"
                                                        allowfullscreen
                                                        loading="lazy"></iframe>
                                            </div>
                                        </div>
                                    @elseif($box->isImage() && $box->image)
                                        <div class="page-box-card__img-wrap">
                                            <img src="{{ asset('uploads/' . $box->image) }}"
                                                 alt="{{ $box->title }}"
                                                 class="page-box-card__img img-fluid"
                                                 loading="lazy">
                                        </div>
                                    @else
                                        <div class="page-box-card__img-wrap">
                                            <span class="page-box-card__icon-placeholder">
                                                <i class="bi {{ $box->isVideo() ? 'bi-play-circle' : 'bi-bookmark-star' }}"></i>
                                            </span>
                                        </div>
                                    @endif
                                    <div class="page-box-card__body">
                                        <h3 class="page-box-card__title">{{ $box->title }}</h3>
                                        @if($box->description)
                                            <p class="page-box-card__desc">{{ $box->description }}</p>
                                        @endif
                                        @if($box->resolvedUrl())
                                            <span class="page-box-card__link-hint">
                                                <i class="bi bi-arrow-right"></i>
                                            </span>
                                        @endif
                                    </div>

                                @if($box->resolvedUrl())
                                    </a>
                                @else
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </section>

@endsection
