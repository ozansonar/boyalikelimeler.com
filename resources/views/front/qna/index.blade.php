@extends('layouts.front')

@section('title', 'Söz Meydanı — Boyalı Kelimeler')
@section('meta_description', 'Söz Meydanı\'nda edebiyattan sanata, psikolojiden astrolojiye her konuda soru sorun, cevap verin.')
@section('canonical', route('qna.index'))
@section('og_title', 'Söz Meydanı — Boyalı Kelimeler')
@section('og_description', 'Edebiyattan sanata her konuda soru sorun, cevap verin.')

@section('content')

    {{-- Hero --}}
    <section class="qna-hero" aria-label="Söz Meydanı başlığı">
        <div class="container">
            <div class="qna-hero__inner">
                <div class="qna-hero__badge">
                    <i class="fa-solid fa-comments me-2"></i>Soru &amp; Cevap
                </div>
                <h1 class="qna-hero__title">Söz Meydanı</h1>
                <p class="qna-hero__desc">
                    Merak ettiklerini sor, bildiklerini paylaş. Edebiyattan sanata, psikolojiden astrolojiye — kelimelerin buluştuğu meydan.
                </p>
                <div class="qna-hero__stats">
                    <span class="qna-hero__stat">
                        <i class="fa-solid fa-layer-group me-1"></i>{{ $stats['categories'] }} Kategori
                    </span>
                    <span class="qna-hero__stat-sep">|</span>
                    <span class="qna-hero__stat">
                        <i class="fa-solid fa-circle-question me-1"></i>{{ number_format($stats['questions']) }} Soru
                    </span>
                    <span class="qna-hero__stat-sep">|</span>
                    <span class="qna-hero__stat">
                        <i class="fa-solid fa-message me-1"></i>{{ number_format($stats['answers']) }} Cevap
                    </span>
                </div>
            </div>
        </div>
    </section>

    {{-- Subtitle Bar --}}
    <div class="qna-subtitle-bar">
        <div class="container">
            <p class="qna-subtitle-bar__text">
                <i class="fa-solid fa-compass me-2"></i>Bir kategori seçerek soruları keşfetmeye başlayın
            </p>
        </div>
    </div>

    {{-- Categories Grid --}}
    <section class="qna-section" aria-label="Soru cevap kategorileri">
        <div class="container">
            <div class="qna-cat-grid">

                @foreach($categories as $category)
                    <a href="{{ route('qna.category', $category->slug) }}" class="qna-cat-card">
                        <div class="qna-cat-card__icon-wrap {{ $category->color_class }}">
                            <i class="{{ $category->icon }}"></i>
                        </div>
                        <h3 class="qna-cat-card__title">{{ $category->name }}</h3>
                        <p class="qna-cat-card__desc">{{ $category->description }}</p>
                        <div class="qna-cat-card__meta">
                            <span class="qna-cat-card__count"><i class="fa-solid fa-circle-question me-1"></i>{{ $category->approved_questions_count }} Soru</span>
                        </div>
                        <span class="qna-cat-card__arrow">
                            <i class="fa-solid fa-arrow-right"></i>
                        </span>
                    </a>
                @endforeach

            </div>
        </div>
    </section>

@endsection
