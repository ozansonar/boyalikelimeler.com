@extends('layouts.admin')

@section('title', 'Cevap Detayı — Söz Meydanı — Admin')

@section('content')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3" data-aos="fade-down" data-aos-duration="400">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link"><i class="bi bi-house me-1"></i>Ana Sayfa</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.qna.answers.index') }}" class="breadcrumb-link">Söz Meydanı Cevapları</a>
            </li>
            <li class="breadcrumb-item active text-teal">Cevap Detayı</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4" data-aos="fade-down">
        <div>
            <h1 class="page-title">Cevap Detayı</h1>
            <p class="page-subtitle">{{ $answer->user?->name ?? 'Anonim' }} tarafından yazıldı</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.qna.answers.index') }}" class="btn-glass">
                <i class="bi bi-arrow-left me-1"></i>Geri Dön
            </a>
            @if($answer->question && $answer->question->status->value === 'approved')
                <a href="{{ route('qna.show', ['categorySlug' => $answer->question->category?->slug, 'questionSlug' => $answer->question->slug]) }}" target="_blank" class="btn-glass">
                    <i class="bi bi-box-arrow-up-right me-1"></i>Sitede Gör
                </a>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <!-- Left: Answer + Question -->
        <div class="col-lg-8">
            <!-- Answer Content -->
            <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card-body-custom">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="form-section-title mb-1"><i class="bi bi-chat-left-text me-2"></i>Cevap İçeriği</h5>
                        @if($answer->status->value === 'approved')
                            <span class="usr-status-badge usr-status-badge-green">Onaylı</span>
                        @elseif($answer->status->value === 'pending')
                            <span class="usr-status-badge usr-status-badge-orange">Bekliyor</span>
                        @else
                            <span class="usr-status-badge usr-status-badge-red">Reddedildi</span>
                        @endif
                    </div>
                    <div class="mb-3" data-aos="fade-in">
                        {!! nl2br(e($answer->body)) !!}
                    </div>

                    @if($answer->status->value === 'pending')
                        <hr class="border-secondary">
                        <div class="d-flex gap-2">
                            <button class="btn-teal" onclick="openQnaApproveModal({{ $answer->id }}, {{ Js::from(Str::limit(str_replace(["\r\n", "\r", "\n"], ' ', strip_tags($answer->body)), 40)) }}, 'answer')">
                                <i class="bi bi-check-circle me-1"></i>Onayla
                            </button>
                            <button class="btn-teal btn-danger-gradient" onclick="openQnaRejectModal({{ $answer->id }}, {{ Js::from(Str::limit(str_replace(["\r\n", "\r", "\n"], ' ', strip_tags($answer->body)), 40)) }}, 'answer')">
                                <i class="bi bi-x-circle me-1"></i>Reddet
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Related Question -->
            @if($answer->question)
                <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="150">
                    <div class="card-body-custom">
                        <h5 class="form-section-title"><i class="bi bi-question-circle me-2"></i>İlgili Soru</h5>
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-600-primary mb-1">{{ $answer->question->title }}</h6>
                                <span class="usr-meta">
                                    <i class="bi bi-person me-1"></i>{{ $answer->question->user?->name ?? 'Anonim' }}
                                    <span class="ms-2"><i class="bi bi-calendar me-1"></i>{{ $answer->question->created_at->format('d M Y H:i') }}</span>
                                </span>
                            </div>
                            @if($answer->question->status->value === 'approved')
                                <span class="usr-status-badge usr-status-badge-green">Onaylı</span>
                            @elseif($answer->question->status->value === 'pending')
                                <span class="usr-status-badge usr-status-badge-orange">Bekliyor</span>
                            @else
                                <span class="usr-status-badge usr-status-badge-red">Reddedildi</span>
                            @endif
                        </div>
                        @if($answer->question->body)
                            <div class="mt-2 text-clr-secondary">
                                {!! nl2br(e($answer->question->body)) !!}
                            </div>
                        @endif
                        <div class="d-flex gap-3 mt-3 text-clr-secondary">
                            <span><i class="bi bi-eye me-1"></i>{{ $answer->question->view_count }} görüntülenme</span>
                            <span><i class="bi bi-hand-thumbs-up me-1"></i>{{ $answer->question->like_count }} beğeni</span>
                            <span><i class="bi bi-chat me-1"></i>{{ $answer->question->answer_count }} cevap</span>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('admin.qna.questions.show', $answer->question->id) }}" class="btn-glass">
                                <i class="bi bi-eye me-1"></i>Soru Detayına Git
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right: Info -->
        <div class="col-lg-4">
            <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card-body-custom">
                    <h5 class="form-section-title"><i class="bi bi-info-circle me-2"></i>Bilgiler</h5>
                    <div class="mb-2">
                        <span class="text-clr-secondary">Yazan:</span>
                        <span class="ms-1">{{ $answer->user?->name ?? 'Anonim' }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-clr-secondary">Beğeni:</span>
                        <span class="ms-1"><i class="bi bi-hand-thumbs-up me-1"></i>{{ $answer->like_count }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-clr-secondary">Durum:</span>
                        <span class="ms-1">
                            @if($answer->status->value === 'approved')
                                Onaylı
                            @elseif($answer->status->value === 'pending')
                                Bekliyor
                            @else
                                Reddedildi
                            @endif
                        </span>
                    </div>
                    <div class="mb-2">
                        <span class="text-clr-secondary">Oluşturulma:</span>
                        <span class="ms-1">{{ $answer->created_at->format('d M Y H:i') }}</span>
                    </div>
                    @if($answer->updated_at && $answer->updated_at->ne($answer->created_at))
                        <div class="mb-2">
                            <span class="text-clr-secondary">Güncellenme:</span>
                            <span class="ms-1">{{ $answer->updated_at->format('d M Y H:i') }}</span>
                        </div>
                    @endif
                    <div class="mb-2">
                        <span class="text-clr-secondary">IP Adresi:</span>
                        <span class="ms-1">{{ $answer->ip_address ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="250">
                <div class="card-body-custom">
                    <h5 class="form-section-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Tehlikeli Bölge</h5>
                    <button class="btn-teal btn-danger-gradient w-100" onclick="openQnaDeleteModal({{ $answer->id }}, {{ Js::from(Str::limit(str_replace(["\r\n", "\r", "\n"], ' ', strip_tags($answer->body)), 40)) }}, 'answer')">
                        <i class="bi bi-trash me-1"></i>Cevabı Sil
                    </button>
                </div>
            </div>
        </div>
    </div>

    @include('admin.qna-questions._modals')

@endsection

@push('scripts')
<script src="{{ asset('assets/admin/js/qna.js') }}?v={{ filemtime(public_path('assets/admin/js/qna.js')) }}"></script>
@endpush
