@extends('layouts.front')

@section('title', $category->name . ' — Söz Meydanı — Boyalı Kelimeler')
@section('meta_description', $category->name . ' kategorisinde soru sorun, cevap verin. ' . $category->description)
@section('canonical', route('qna.category', $category->slug))
@section('og_title', $category->name . ' — Söz Meydanı — Boyalı Kelimeler')
@section('og_description', $category->name . ' kategorisinde soru sorun, cevap verin.')

@section('content')

    {{-- Breadcrumb + Category Header --}}
    <section class="qna-cat-hero" aria-label="Kategori başlığı">
        <div class="container">
            {{-- Breadcrumb --}}
            <nav class="qna-breadcrumb" aria-label="Sayfa yolu">
                <a href="{{ route('qna.index') }}" class="qna-breadcrumb__link">
                    <i class="fa-solid fa-comments me-1"></i>Söz Meydanı
                </a>
                <i class="fa-solid fa-chevron-right qna-breadcrumb__sep"></i>
                <span class="qna-breadcrumb__current">{{ $category->name }}</span>
            </nav>

            <div class="qna-cat-hero__inner">
                <div class="qna-cat-hero__left">
                    <div class="qna-cat-hero__icon {{ $category->color_class }}">
                        <i class="{{ $category->icon }}"></i>
                    </div>
                    <div>
                        <h1 class="qna-cat-hero__title">{{ $category->name }}</h1>
                        <p class="qna-cat-hero__desc">{{ $category->description }}</p>
                    </div>
                </div>
                <div class="qna-cat-hero__right">
                    <div class="qna-cat-hero__stat-item">
                        <span class="qna-cat-hero__stat-val">{{ $questions->total() }}</span>
                        <span class="qna-cat-hero__stat-label">Soru</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Questions Section --}}
    <section class="qna-section" aria-label="Sorular">
        <div class="container">

            {{-- Toolbar --}}
            <div class="qna-toolbar">
                <div class="qna-toolbar__search">
                    <i class="fa-solid fa-magnifying-glass qna-toolbar__search-icon"></i>
                    <input type="text"
                           class="wpost-form__input qna-toolbar__search-input"
                           id="qnaSearchInput"
                           placeholder="Sorularda ara..."
                           value="{{ $filters['search'] ?? '' }}">
                </div>
                <div class="qna-toolbar__filters">
                    <select class="wpost-form__input qna-toolbar__select" id="qnaSortSelect">
                        <option value="newest" @selected(($filters['sort'] ?? 'newest') === 'newest')>En Yeni</option>
                        <option value="popular" @selected(($filters['sort'] ?? '') === 'popular')>En Çok Cevaplanan</option>
                        <option value="unanswered" @selected(($filters['sort'] ?? '') === 'unanswered')>Cevaplanmamış</option>
                    </select>
                </div>
                @auth
                    <button type="button" class="qna-toolbar__ask-btn" data-bs-toggle="modal" data-bs-target="#askQuestionModal">
                        <i class="fa-solid fa-plus me-2"></i>Yeni Soru Sor
                    </button>
                @else
                    <a href="{{ route('login') }}" class="qna-toolbar__ask-btn">
                        <i class="fa-solid fa-plus me-2"></i>Yeni Soru Sor
                    </a>
                @endauth
            </div>

            {{-- Questions List --}}
            <div class="qna-questions">
                @forelse($questions as $question)
                    <a href="{{ route('qna.show', ['categorySlug' => $category->slug, 'questionSlug' => $question->slug]) }}" class="qna-question qna-question--link">
                        <div class="qna-question__header">
                            <div class="qna-question__author">
                                <div class="qna-question__avatar">
                                    @if($question->user?->avatar)
                                        <img src="{{ upload_url($question->user->avatar, 'thumb') }}" alt="{{ $question->user->name }}" loading="lazy" class="img-fluid">
                                    @else
                                        <i class="fa-solid fa-user"></i>
                                    @endif
                                </div>
                                <div>
                                    <span class="qna-question__author-name">{{ $question->user?->name ?? 'Anonim' }}</span>
                                    <time class="qna-question__date" datetime="{{ $question->created_at->toDateString() }}">{{ $question->created_at->translatedFormat('d M Y') }}</time>
                                </div>
                            </div>
                            <div class="qna-question__badges">
                                @if($question->approved_answer_count > 0)
                                    <span class="qna-question__badge qna-question__badge--answered">
                                        <i class="fa-solid fa-check me-1"></i>Cevaplandı
                                    </span>
                                @else
                                    <span class="qna-question__badge qna-question__badge--waiting">
                                        <i class="fa-solid fa-hourglass-half me-1"></i>Cevap Bekleniyor
                                    </span>
                                @endif
                            </div>
                        </div>
                        <h3 class="qna-question__title">{{ $question->title }}</h3>
                        <p class="qna-question__text">{{ Str::limit(strip_tags($question->body), 200) }}</p>
                        <div class="qna-question__footer">
                            <div class="qna-question__stats">
                                <span class="qna-question__stat">
                                    <i class="fa-solid fa-message me-1"></i>{{ $question->answer_count }} Cevap
                                </span>
                                <span class="qna-question__stat">
                                    <i class="fa-solid fa-eye me-1"></i>{{ $question->view_count }}
                                </span>
                                <span class="qna-question__stat">
                                    <i class="fa-solid fa-thumbs-up me-1"></i>{{ $question->like_count }}
                                </span>
                            </div>
                            <span class="qna-question__detail-link">
                                @if($question->approved_answer_count > 0)
                                    <i class="fa-solid fa-arrow-right me-1"></i>Detaya Git
                                @else
                                    <i class="fa-solid fa-pen me-1"></i>Cevap Yaz
                                @endif
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="qna-empty">
                        <i class="fa-solid fa-circle-question qna-empty__icon"></i>
                        <p class="qna-empty__text">Bu kategoride henüz soru sorulmamış. İlk soruyu siz sorun!</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($questions->hasPages())
                <nav class="clist-pagination" aria-label="Sayfalama">
                    {{ $questions->links('vendor.pagination.bootstrap-5') }}
                </nav>
            @endif

        </div>
    </section>

    {{-- Yeni Soru Sor Modal --}}
    @auth
        <div class="modal fade" id="askQuestionModal" tabindex="-1" aria-labelledby="askQuestionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content qna-modal">
                    <div class="modal-header qna-modal__header">
                        <h2 class="modal-title qna-modal__title" id="askQuestionModalLabel">
                            <i class="fa-solid fa-circle-question me-2"></i>Yeni Soru Sor
                        </h2>
                        <button type="button" class="qna-modal__close" data-bs-dismiss="modal" aria-label="Kapat">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="modal-body qna-modal__body">
                        <form id="askQuestionForm" novalidate>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="auth-form__group">
                                <label class="auth-form__label" for="questionTitle">
                                    <i class="fa-solid fa-heading me-1"></i>Soru Başlığı
                                </label>
                                <input type="text"
                                       class="auth-form__input"
                                       id="questionTitle"
                                       name="title"
                                       placeholder="Sorunuzu kısa ve net bir şekilde özetleyin"
                                       required>
                            </div>
                            <div class="auth-form__group">
                                <label class="auth-form__label" for="questionBody">
                                    <i class="fa-solid fa-align-left me-1"></i>Soru Detayı
                                </label>
                                <textarea class="auth-form__input qna-modal__textarea"
                                          id="questionBody"
                                          name="body"
                                          rows="6"
                                          placeholder="Sorunuzu detaylı bir şekilde açıklayın. Ne bilmek istediğinizi, hangi bağlamda sorduğunuzu belirtin..."
                                          required></textarea>
                            </div>
                            <div class="auth-form__group">
                                <label class="auth-form__label" for="questionCategory">
                                    <i class="fa-solid fa-tag me-1"></i>Kategori
                                </label>
                                <select class="auth-form__input" id="questionCategory" name="qna_category_id">
                                    @foreach($allCategories as $cat)
                                        <option value="{{ $cat->id }}" @selected($cat->id === $category->id)>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer qna-modal__footer">
                        <button type="button" class="qna-modal__cancel-btn" data-bs-dismiss="modal">
                            <i class="fa-solid fa-xmark me-1"></i>Vazgeç
                        </button>
                        <button type="button" id="submitQuestionBtn" class="qna-modal__submit-btn">
                            <i class="fa-solid fa-paper-plane me-2"></i>Soruyu Gönder
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endauth

@endsection

@push('scripts')
    <script src="{{ asset('js/qna.js') }}?v=1.0"></script>
@endpush
