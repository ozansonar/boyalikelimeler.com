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

    {{-- Soru Sor Formu --}}
    <section class="qna-ask-section" aria-label="Soru sor">
        <div class="container">
            <div class="qna-ask-card">
                <div class="qna-ask-card__header">
                    <h2 class="qna-ask-card__title">
                        <i class="fa-solid fa-circle-question me-2"></i>Soru Sor
                    </h2>
                    <p class="qna-ask-card__desc">Merak ettiğiniz soruyu buradan hızlıca sorabilirsiniz.</p>
                </div>
                @auth
                    <form id="askQuestionForm" class="qna-ask-card__form" novalidate>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="qna-ask-card__row">
                            <div class="qna-ask-card__field qna-ask-card__field--title">
                                <label class="qna-ask-card__label" for="questionTitle">
                                    <i class="fa-solid fa-heading me-1"></i>Soru Başlığı
                                </label>
                                <input type="text"
                                       class="qna-ask-card__input"
                                       id="questionTitle"
                                       name="title"
                                       placeholder="Sorunuzu kısa ve net bir şekilde özetleyin"
                                       required>
                            </div>
                            <div class="qna-ask-card__field qna-ask-card__field--category">
                                <label class="qna-ask-card__label" for="questionCategory">
                                    <i class="fa-solid fa-tag me-1"></i>Kategori
                                </label>
                                <select class="qna-ask-card__input" id="questionCategory" name="qna_category_id" required>
                                    <option value="">Kategori seçin</option>
                                    @foreach($allCategories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="qna-ask-card__field">
                            <label class="qna-ask-card__label" for="questionBody">
                                <i class="fa-solid fa-align-left me-1"></i>Soru Detayı
                            </label>
                            <textarea class="qna-ask-card__input qna-ask-card__textarea"
                                      id="questionBody"
                                      name="body"
                                      rows="4"
                                      placeholder="Sorunuzu detaylı bir şekilde açıklayın..."
                                      required></textarea>
                        </div>
                        <div class="qna-ask-card__actions">
                            <button type="button" id="submitQuestionBtn" class="qna-ask-card__submit">
                                <i class="fa-solid fa-paper-plane me-2"></i>Soruyu Gönder
                            </button>
                        </div>
                    </form>
                @else
                    <div class="qna-ask-card__guest">
                        <i class="fa-solid fa-lock me-2"></i>
                        Soru sormak için
                        <a href="{{ route('login') }}" class="qna-ask-card__login-link">giriş yapın</a>
                        veya
                        <a href="{{ route('register') }}" class="qna-ask-card__login-link">kayıt olun</a>.
                    </div>
                @endauth
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

@push('scripts')
    <script src="{{ asset('js/qna.js') }}?v={{ filemtime(public_path('js/qna.js')) }}" defer></script>
@endpush
