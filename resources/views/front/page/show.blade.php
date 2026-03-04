@extends('layouts.front')

@section('title', ($page->meta_title ?: $page->title) . ' — Boyalı Kelimeler')
@section('meta_description', $page->meta_description ?: Str::limit(strip_tags((string) $page->excerpt), 160))
@section('canonical', route('page.show', $page->slug))
@section('og_title', ($page->meta_title ?: $page->title) . ' — Boyalı Kelimeler')
@section('og_description', $page->meta_description ?: Str::limit(strip_tags((string) $page->excerpt), 160))
@if($page->cover_image)
    @section('og_image', asset('uploads/' . $page->cover_image))
@endif

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
                    <div class="row g-4">
                        @foreach($page->boxes as $box)
                            <div class="{{ $box->bootstrapColClass() }}">
                                @if($box->link)
                                    <a href="{{ $box->link }}" target="{{ $box->link_target }}" class="page-box-card" rel="{{ $box->link_target === '_blank' ? 'noopener noreferrer' : '' }}">
                                @else
                                    <div class="page-box-card">
                                @endif

                                    @if($box->image)
                                        <div class="page-box-card__img-wrap">
                                            <img src="{{ asset('uploads/' . $box->image) }}"
                                                 alt="{{ $box->title }}"
                                                 class="page-box-card__img img-fluid"
                                                 loading="lazy">
                                        </div>
                                    @endif
                                    <div class="page-box-card__body">
                                        <h3 class="page-box-card__title">{{ $box->title }}</h3>
                                        @if($box->description)
                                            <p class="page-box-card__desc">{{ $box->description }}</p>
                                        @endif
                                        @if($box->link)
                                            <span class="page-box-card__link-hint">
                                                <i class="bi bi-arrow-right"></i>
                                            </span>
                                        @endif
                                    </div>

                                @if($box->link)
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
