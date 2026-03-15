@extends('layouts.front')

@section('title', $question->title . ' — Söz Meydanı — Boyalı Kelimeler')
@section('meta_description', Str::limit(strip_tags($question->body), 160))
@section('canonical', route('qna.show', ['categorySlug' => $question->category->slug, 'questionSlug' => $question->slug]))
@section('og_title', $question->title)
@section('og_description', Str::limit(strip_tags($question->body), 160))
@section('og_type', 'article')

@section('content')

    {{-- Breadcrumb Bar --}}
    <section class="qna-cat-hero qna-cat-hero--compact" aria-label="Sayfa yolu">
        <div class="container">
            <nav class="qna-breadcrumb" aria-label="Sayfa yolu">
                <a href="{{ route('qna.index') }}" class="qna-breadcrumb__link">
                    <i class="fa-solid fa-comments me-1"></i>Söz Meydanı
                </a>
                <i class="fa-solid fa-chevron-right qna-breadcrumb__sep"></i>
                <a href="{{ route('qna.category', $question->category->slug) }}" class="qna-breadcrumb__link">{{ $question->category->name }}</a>
                <i class="fa-solid fa-chevron-right qna-breadcrumb__sep"></i>
                <span class="qna-breadcrumb__current">Soru Detayı</span>
            </nav>
        </div>
    </section>

    {{-- Question Detail Section --}}
    <section class="qna-section" aria-label="Soru detayı">
        <div class="container">
            <div class="row">

                {{-- Left: Question + Answers --}}
                <div class="col-lg-8">

                    {{-- Question Full --}}
                    <article class="qna-detail">
                        <div class="qna-detail__header">
                            <div class="qna-question__author">
                                <div class="qna-question__avatar qna-detail__avatar">
                                    @if($question->user?->avatar)
                                        <img src="{{ upload_url($question->user->avatar, 'thumb') }}" alt="{{ $question->user->name }}" loading="lazy" class="img-fluid">
                                    @else
                                        <i class="fa-solid fa-user"></i>
                                    @endif
                                </div>
                                <div>
                                    <span class="qna-question__author-name">{{ $question->user?->name ?? 'Anonim' }}</span>
                                    <time class="qna-question__date" datetime="{{ $question->created_at->toDateTimeString() }}">{{ $question->created_at->translatedFormat('d F Y, H:i') }}</time>
                                </div>
                            </div>
                            @if($question->approvedAnswers->count() > 0)
                                <span class="qna-question__badge qna-question__badge--answered">
                                    <i class="fa-solid fa-check me-1"></i>Cevaplandı
                                </span>
                            @else
                                <span class="qna-question__badge qna-question__badge--waiting">
                                    <i class="fa-solid fa-hourglass-half me-1"></i>Cevap Bekleniyor
                                </span>
                            @endif
                        </div>

                        <h1 class="qna-detail__title">{{ $question->title }}</h1>

                        <div class="qna-detail__body">
                            {!! nl2br(e($question->body)) !!}
                        </div>

                        <div class="qna-detail__actions">
                            <div class="qna-detail__stats">
                                <span class="qna-question__stat">
                                    <i class="fa-solid fa-eye me-1"></i>{{ $question->view_count }} Görüntülenme
                                </span>
                                <span class="qna-question__stat">
                                    <i class="fa-solid fa-message me-1"></i>{{ $question->approvedAnswers->count() }} Cevap
                                </span>
                            </div>
                            <div class="qna-detail__action-btns">
                                <button type="button"
                                        class="qna-detail__like-btn qna-like-btn"
                                        data-type="question"
                                        data-id="{{ $question->id }}"
                                        data-liked="{{ $userLikedQuestion ? '1' : '0' }}">
                                    <i class="fa-{{ $userLikedQuestion ? 'solid' : 'regular' }} fa-thumbs-up me-1"></i>
                                    <span class="qna-like-count">{{ $question->like_count }}</span> Beğen
                                </button>
                                <button type="button" class="qna-detail__share-btn qna-share-btn">
                                    <i class="fa-solid fa-share-nodes me-1"></i>Paylaş
                                </button>
                            </div>
                        </div>
                    </article>

                    {{-- Answers Section --}}
                    <div class="qna-detail-answers">
                        <h2 class="qna-detail-answers__heading">
                            <i class="fa-solid fa-messages me-2"></i>{{ $question->approvedAnswers->count() }} Cevap
                        </h2>

                        @foreach($question->approvedAnswers as $answer)
                            <div class="qna-answer qna-detail-answer">
                                <div class="qna-answer__header">
                                    <div class="qna-question__author">
                                        <div class="qna-question__avatar qna-answer__avatar">
                                            @if($answer->user?->avatar)
                                                <img src="{{ upload_url($answer->user->avatar, 'thumb') }}" alt="{{ $answer->user->name }}" loading="lazy" class="img-fluid">
                                            @else
                                                <i class="fa-solid fa-user"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="qna-question__author-name">{{ $answer->user?->name ?? 'Anonim' }}</span>
                                            <time class="qna-question__date" datetime="{{ $answer->created_at->toDateTimeString() }}">{{ $answer->created_at->translatedFormat('d F Y, H:i') }}</time>
                                        </div>
                                    </div>
                                    <button type="button"
                                            class="qna-detail-answer__like-btn qna-like-btn"
                                            data-type="answer"
                                            data-id="{{ $answer->id }}"
                                            data-liked="{{ isset($userLikedAnswers[$answer->id]) ? '1' : '0' }}">
                                        <i class="fa-{{ isset($userLikedAnswers[$answer->id]) ? 'solid' : 'regular' }} fa-thumbs-up me-1"></i>
                                        <span class="qna-like-count">{{ $answer->like_count }}</span>
                                    </button>
                                </div>
                                <div class="qna-detail-answer__body">
                                    {!! nl2br(e($answer->body)) !!}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Write Answer Form --}}
                    <div class="qna-write-answer">
                        <h2 class="qna-write-answer__heading">
                            <i class="fa-solid fa-pen-to-square me-2"></i>Cevabınızı Yazın
                        </h2>
                        @auth
                            <form class="qna-write-answer__form" id="writeAnswerForm" novalidate>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="question_id" value="{{ $question->id }}">
                                <textarea class="auth-form__input qna-write-answer__textarea"
                                          name="body"
                                          rows="6"
                                          placeholder="Soruya cevabınızı buraya yazın. Detaylı ve açıklayıcı cevaplar diğer üyelere daha çok fayda sağlar..."
                                          required></textarea>
                                <div class="qna-write-answer__footer">
                                    <p class="qna-write-answer__hint">
                                        <i class="fa-solid fa-circle-info me-1"></i>Saygılı ve yapıcı bir dil kullanmaya özen gösterin.
                                    </p>
                                    <button type="button" id="submitAnswerBtn" class="qna-write-answer__btn">
                                        <i class="fa-solid fa-paper-plane me-2"></i>Cevabı Gönder
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="qna-write-answer__login">
                                <p class="qna-write-answer__login-text">
                                    <i class="fa-solid fa-lock me-1"></i>Cevap yazmak için
                                    <a href="{{ route('login') }}" class="qna-write-answer__login-link">giriş yapın</a> veya
                                    <a href="{{ route('register') }}" class="qna-write-answer__login-link">kayıt olun</a>.
                                </p>
                            </div>
                        @endauth
                    </div>

                </div>

                {{-- Right: Sidebar --}}
                <aside class="col-lg-4">
                    <div class="qna-sidebar">

                        {{-- Question Author Info --}}
                        <div class="qna-sidebar__card">
                            <h3 class="qna-sidebar__heading">
                                <i class="fa-solid fa-user me-2"></i>Soru Sahibi
                            </h3>
                            <div class="qna-sidebar__author">
                                <div class="qna-sidebar__author-avatar">
                                    @if($question->user?->avatar)
                                        <img src="{{ upload_url($question->user->avatar, 'thumb') }}" alt="{{ $question->user->name }}" loading="lazy" class="img-fluid">
                                    @else
                                        <i class="fa-solid fa-user"></i>
                                    @endif
                                </div>
                                <div>
                                    <span class="qna-sidebar__author-name">{{ $question->user?->name ?? 'Anonim' }}</span>
                                    <span class="qna-sidebar__author-role">Üye</span>
                                </div>
                            </div>
                            <div class="qna-sidebar__author-stats">
                                <div class="qna-sidebar__author-stat">
                                    <span class="qna-sidebar__author-stat-val">{{ $userStats['questions'] }}</span>
                                    <span class="qna-sidebar__author-stat-label">Soru</span>
                                </div>
                                <div class="qna-sidebar__author-stat">
                                    <span class="qna-sidebar__author-stat-val">{{ $userStats['answers'] }}</span>
                                    <span class="qna-sidebar__author-stat-label">Cevap</span>
                                </div>
                                <div class="qna-sidebar__author-stat">
                                    <span class="qna-sidebar__author-stat-val">{{ $userStats['likes'] }}</span>
                                    <span class="qna-sidebar__author-stat-label">Beğeni</span>
                                </div>
                            </div>
                        </div>

                        {{-- Category Info --}}
                        <div class="qna-sidebar__card">
                            <h3 class="qna-sidebar__heading">
                                <i class="fa-solid fa-folder me-2"></i>Kategori
                            </h3>
                            <a href="{{ route('qna.category', $question->category->slug) }}" class="qna-sidebar__category">
                                <div class="qna-cat-card__icon-wrap {{ $question->category->color_class }} qna-sidebar__category-icon">
                                    <i class="{{ $question->category->icon }}"></i>
                                </div>
                                <div>
                                    <span class="qna-sidebar__category-name">{{ $question->category->name }}</span>
                                    <span class="qna-sidebar__category-count">{{ $question->category->approvedQuestionCount() }} Soru</span>
                                </div>
                            </a>
                        </div>

                        {{-- Related Questions --}}
                        @if($relatedQuestions->count() > 0)
                            <div class="qna-sidebar__card">
                                <h3 class="qna-sidebar__heading">
                                    <i class="fa-solid fa-link me-2"></i>Benzer Sorular
                                </h3>
                                <div class="qna-sidebar__related">
                                    @foreach($relatedQuestions as $related)
                                        <a href="{{ route('qna.show', ['categorySlug' => $question->category->slug, 'questionSlug' => $related->slug]) }}" class="qna-sidebar__related-item">
                                            <span class="qna-sidebar__related-title">{{ $related->title }}</span>
                                            <span class="qna-sidebar__related-meta">{{ $related->approved_answer_count }} Cevap</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>
                </aside>

            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script src="{{ asset('js/qna.js') }}?v={{ filemtime(public_path('js/qna.js')) }}"></script>
@endpush
