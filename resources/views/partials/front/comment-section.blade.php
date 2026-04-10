{{-- Comment Section: Form + Approved Comments --}}
{{-- Usage: @include('partials.front.comment-section', ['commentable' => $work, 'commentableType' => 'literary_work']) --}}

<section class="cmt-section" id="comments">

    {{-- Approved Comments List --}}
    @if($commentable->approvedComments->isNotEmpty())
        <div class="cmt-list">
            <h3 class="cmt-list__title">
                <i class="fa-solid fa-comments me-2"></i>Yorumlar
                <span class="cmt-list__count">{{ $commentable->approvedComments->count() }}</span>
            </h3>

            @php
                $avgRating = round($commentable->approvedComments->avg('rating'), 1);
            @endphp
            <div class="cmt-list__avg">
                <div class="cmt-stars cmt-stars--readonly">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fa-{{ $i <= round($avgRating) ? 'solid' : 'regular' }} fa-star"></i>
                    @endfor
                </div>
                <span class="cmt-list__avg-text">{{ $avgRating }} / 5 ({{ $commentable->approvedComments->count() }} yorum)</span>
            </div>

            @foreach($commentable->approvedComments as $comment)
                <div class="cmt-card" id="comment-{{ $comment->id }}">
                    <div class="cmt-card__avatar">
                        @if($comment->isByUser() && $comment->user?->avatar)
                            <img src="{{ upload_url($comment->user->avatar, 'thumb') }}" alt="{{ $comment->fullName() }}" class="cmt-card__avatar-img" loading="lazy">
                        @else
                            {{ $comment->commenterInitials() }}
                        @endif
                    </div>
                    <div class="cmt-card__body">
                        <div class="cmt-card__header">
                            <span class="cmt-card__name">
                                @if($comment->isByUser() && $comment->user)
                                    <a href="{{ route('profile.show', $comment->user->username) }}" class="cmt-card__name-link">{{ $comment->fullName() }}</a>
                                @else
                                    {{ $comment->fullName() }}
                                @endif
                            </span>
                            <time class="cmt-card__date" datetime="{{ $comment->created_at->toDateString() }}">
                                {{ $comment->created_at->translatedFormat('d F Y, H:i') }}
                            </time>
                        </div>
                        <div class="cmt-stars cmt-stars--sm">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fa-{{ $i <= $comment->rating ? 'solid' : 'regular' }} fa-star"></i>
                            @endfor
                        </div>
                        <p class="cmt-card__text">{{ $comment->body }}</p>

                        <button type="button" class="cmt-reply-btn" data-comment-id="{{ $comment->id }}">
                            <i class="fa-solid fa-reply me-1"></i>Yanıtla
                        </button>
                    </div>
                </div>

                {{-- Approved Replies --}}
                @if($comment->approvedReplies->isNotEmpty())
                    <div class="cmt-replies" id="replies-{{ $comment->id }}">
                        @foreach($comment->approvedReplies as $reply)
                            <div class="cmt-card cmt-card--reply">
                                <div class="cmt-card__avatar cmt-card__avatar--sm">
                                    @if($reply->isByUser() && $reply->user?->avatar)
                                        <img src="{{ upload_url($reply->user->avatar, 'thumb') }}" alt="{{ $reply->fullName() }}" class="cmt-card__avatar-img" loading="lazy">
                                    @else
                                        {{ $reply->commenterInitials() }}
                                    @endif
                                </div>
                                <div class="cmt-card__body">
                                    <div class="cmt-card__header">
                                        <span class="cmt-card__name">
                                            @if($reply->isByUser() && $reply->user)
                                                <a href="{{ route('profile.show', $reply->user->username) }}" class="cmt-card__name-link">{{ $reply->fullName() }}</a>
                                            @else
                                                {{ $reply->fullName() }}
                                            @endif
                                            @if($reply->isByUser() && $reply->user_id === $commentable->user_id)
                                                <span class="cmt-author-badge"><i class="fa-solid fa-pen-nib me-1"></i>Yazar</span>
                                            @endif
                                        </span>
                                        <time class="cmt-card__date" datetime="{{ $reply->created_at->toDateString() }}">
                                            {{ $reply->created_at->translatedFormat('d F Y, H:i') }}
                                        </time>
                                    </div>
                                    <p class="cmt-card__text">{{ $reply->body }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Reply Form (hidden by default) --}}
                <div class="cmt-reply-form-wrapper" id="replyForm-{{ $comment->id }}" hidden>
                    <form class="cmt-form cmt-reply-form" data-action="{{ route('comment.reply') }}" method="POST" novalidate>
                        @csrf
                        <input type="hidden" name="comment_id" value="{{ $comment->id }}">

                        <div class="row g-2">
                            @guest
                                <div class="col-md-4">
                                    <div class="cmt-form__group">
                                        <label class="cmt-form__label">Ad <span class="cmt-form__req">*</span></label>
                                        <input type="text" class="cmt-form__input cmt-form__input--sm validate[required,maxSize[100]]" name="first_name" placeholder="Adınız" data-prompt-position="bottomLeft">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="cmt-form__group">
                                        <label class="cmt-form__label">Soyad <span class="cmt-form__req">*</span></label>
                                        <input type="text" class="cmt-form__input cmt-form__input--sm validate[required,maxSize[100]]" name="last_name" placeholder="Soyadınız" data-prompt-position="bottomLeft">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="cmt-form__group">
                                        <label class="cmt-form__label">E-posta <span class="cmt-form__req">*</span></label>
                                        <input type="email" class="cmt-form__input cmt-form__input--sm validate[required,custom[email]]" name="email" placeholder="ornek@mail.com" data-prompt-position="bottomLeft">
                                    </div>
                                </div>
                            @else
                                <div class="col-12">
                                    <div class="cmt-reply-form__auth-info">
                                        <div class="cmt-form__auth-avatar">
                                            @if(auth()->user()->avatar)
                                                <img src="{{ upload_url(auth()->user()->avatar, 'thumb') }}" alt="{{ auth()->user()->name }}" class="cmt-form__auth-avatar-img" loading="lazy">
                                            @else
                                                @php
                                                    $replyNameParts = explode(' ', auth()->user()->name);
                                                    $replyInitials = mb_strtoupper(mb_substr($replyNameParts[0] ?? '', 0, 1)) . mb_strtoupper(mb_substr($replyNameParts[1] ?? '', 0, 1));
                                                @endphp
                                                <span class="cmt-form__auth-avatar-text">{{ $replyInitials }}</span>
                                            @endif
                                        </div>
                                        <span class="cmt-reply-form__auth-name">{{ auth()->user()->name }}</span>
                                    </div>
                                </div>
                            @endguest
                            <div class="col-12">
                                <div class="cmt-form__group">
                                    <textarea class="cmt-form__textarea cmt-form__textarea--sm validate[required,minSize[10],maxSize[3000]]" name="body" rows="3" placeholder="Yanıtınızı yazın..." data-prompt-position="bottomLeft"></textarea>
                                    <span class="cmt-form__charcount"><span class="cmt-reply-charcount">0</span> / 3000</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="cmt-reply-actions">
                                    <button type="button" class="cmt-reply-cancel" data-comment-id="{{ $comment->id }}">
                                        <i class="fa-solid fa-xmark me-1"></i>Vazgeç
                                    </button>
                                    <button type="submit" class="cmt-form__submit cmt-form__submit--sm">
                                        <i class="fa-solid fa-paper-plane me-1"></i>Yanıt Gönder
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Comment Form --}}
    <div class="cmt-form-wrapper">
        <h3 class="cmt-form__title">
            <i class="fa-solid fa-pen-to-square me-2"></i>Yorum Yaz
        </h3>
        <p class="cmt-form__info">
            <i class="fa-solid fa-circle-info me-1"></i>Yorumunuz onaylandıktan sonra yayınlanacaktır.
        </p>

        <form id="commentForm" class="cmt-form" action="{{ route('comment.store') }}" method="POST" novalidate>
            @csrf
            <input type="hidden" name="commentable_type" value="{{ $commentableType }}">
            <input type="hidden" name="commentable_id" value="{{ $commentable->id }}">

            <div class="row g-3">
                @guest
                    <div class="col-md-6">
                        <div class="cmt-form__group">
                            <label for="cmt_first_name" class="cmt-form__label">Ad <span class="cmt-form__req">*</span></label>
                            <input type="text" class="cmt-form__input validate[required,maxSize[100]]" id="cmt_first_name" name="first_name" placeholder="Adınız" data-prompt-position="bottomLeft">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="cmt-form__group">
                            <label for="cmt_last_name" class="cmt-form__label">Soyad <span class="cmt-form__req">*</span></label>
                            <input type="text" class="cmt-form__input validate[required,maxSize[100]]" id="cmt_last_name" name="last_name" placeholder="Soyadınız" data-prompt-position="bottomLeft">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="cmt-form__group">
                            <label for="cmt_email" class="cmt-form__label">E-posta <span class="cmt-form__req">*</span></label>
                            <input type="email" class="cmt-form__input validate[required,custom[email]]" id="cmt_email" name="email" placeholder="ornek@mail.com" data-prompt-position="bottomLeft">
                        </div>
                    </div>
                @else
                    <div class="col-12">
                        <div class="cmt-form__auth-info">
                            <div class="cmt-form__auth-avatar">
                                @if(auth()->user()->avatar)
                                    <img src="{{ upload_url(auth()->user()->avatar, 'thumb') }}" alt="{{ auth()->user()->name }}" class="cmt-form__auth-avatar-img" loading="lazy">
                                @else
                                    @php
                                        $nameParts = explode(' ', auth()->user()->name);
                                        $initials = mb_strtoupper(mb_substr($nameParts[0] ?? '', 0, 1)) . mb_strtoupper(mb_substr($nameParts[1] ?? '', 0, 1));
                                    @endphp
                                    <span class="cmt-form__auth-avatar-text">{{ $initials }}</span>
                                @endif
                            </div>
                            <div class="cmt-form__auth-detail">
                                <span class="cmt-form__auth-name">{{ auth()->user()->name }}</span>
                                <span class="cmt-form__auth-email">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                @endguest
                <div class="col-12">
                    <div class="cmt-form__group">
                        <label class="cmt-form__label">Puan <span class="cmt-form__req">*</span></label>
                        <div class="cmt-rating" id="cmtRating">
                            <input type="hidden" name="rating" id="cmt_rating" value="" class="validate[required]" data-prompt-position="bottomLeft">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" class="cmt-rating__star" data-value="{{ $i }}" aria-label="{{ $i }} yıldız">
                                    <i class="fa-regular fa-star"></i>
                                </button>
                            @endfor
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="cmt-form__group">
                        <label for="cmt_body" class="cmt-form__label">Yorumunuz <span class="cmt-form__req">*</span></label>
                        <textarea class="cmt-form__textarea validate[required,minSize[10],maxSize[3000]]" id="cmt_body" name="body" rows="5" placeholder="Yorumunuzu buraya yazın..." data-prompt-position="bottomLeft"></textarea>
                        <span class="cmt-form__charcount"><span id="cmtCharCount">0</span> / 3000</span>
                    </div>
                </div>
                <div class="col-12">
                    <x-recaptcha />
                </div>
                <div class="col-12">
                    <button type="submit" class="cmt-form__submit" id="commentSubmitBtn">
                        <i class="fa-solid fa-paper-plane me-2"></i>Yorum Gönder
                    </button>
                </div>
            </div>
        </form>
    </div>

</section>

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/jquery-validation-engine/2.6.4/validationEngine.jquery.min.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('vendor/jquery/3.7.1/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/jquery-validation-engine/2.6.4/jquery.validationEngine-tr.js') }}"></script>
<script src="{{ asset('vendor/jquery-validation-engine/2.6.4/jquery.validationEngine.min.js') }}"></script>
<script src="{{ asset('js/comment.js') }}?v={{ filemtime(public_path('js/comment.js')) }}"></script>
@endpush
