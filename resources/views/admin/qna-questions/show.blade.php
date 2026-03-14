@extends('layouts.admin')

@section('title', 'Soru Detayı — Söz Meydanı — Admin')

@section('content')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3" data-aos="fade-down" data-aos-duration="400">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link"><i class="bi bi-house me-1"></i>Ana Sayfa</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.qna.questions.index') }}" class="breadcrumb-link">Söz Meydanı Soruları</a>
            </li>
            <li class="breadcrumb-item active text-teal">Soru Detayı</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4" data-aos="fade-down">
        <div>
            <h1 class="page-title">Soru Detayı</h1>
            <p class="page-subtitle">{{ $question->category?->name }} kategorisi</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.qna.questions.index') }}" class="btn-glass">
                <i class="bi bi-arrow-left me-1"></i>Geri Dön
            </a>
            @if($question->status->value === 'approved')
                <a href="{{ route('qna.show', ['categorySlug' => $question->category?->slug, 'questionSlug' => $question->slug]) }}" target="_blank" class="btn-glass">
                    <i class="bi bi-box-arrow-up-right me-1"></i>Sitede Gör
                </a>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <!-- Left: Question + Answers -->
        <div class="col-lg-8">
            <!-- Question -->
            <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card-body-custom">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="form-section-title mb-1">{{ $question->title }}</h5>
                            <span class="usr-meta">
                                <i class="bi bi-person me-1"></i>{{ $question->user?->name ?? 'Anonim' }}
                                <span class="ms-2"><i class="bi bi-calendar me-1"></i>{{ $question->created_at->format('d M Y H:i') }}</span>
                            </span>
                        </div>
                        @if($question->status->value === 'approved')
                            <span class="usr-status-badge usr-status-badge-green">Onaylı</span>
                        @elseif($question->status->value === 'pending')
                            <span class="usr-status-badge usr-status-badge-orange">Bekliyor</span>
                        @else
                            <span class="usr-status-badge usr-status-badge-red">Reddedildi</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        {!! nl2br(e($question->body)) !!}
                    </div>
                    <div class="d-flex gap-3 text-clr-secondary">
                        <span><i class="bi bi-eye me-1"></i>{{ $question->view_count }} görüntülenme</span>
                        <span><i class="bi bi-hand-thumbs-up me-1"></i>{{ $question->like_count }} beğeni</span>
                        <span><i class="bi bi-chat me-1"></i>{{ $question->answer_count }} cevap</span>
                    </div>

                    @if($question->status->value === 'pending')
                        <hr class="border-secondary">
                        <div class="d-flex gap-2">
                            <button class="btn-teal" onclick="openQnaApproveModal({{ $question->id }}, '{{ addslashes(Str::limit($question->title, 40)) }}', 'question')">
                                <i class="bi bi-check-circle me-1"></i>Onayla
                            </button>
                            <button class="btn-teal btn-danger-gradient" onclick="openQnaRejectModal({{ $question->id }}, '{{ addslashes(Str::limit($question->title, 40)) }}', 'question')">
                                <i class="bi bi-x-circle me-1"></i>Reddet
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Answers -->
            <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="150">
                <div class="card-body-custom">
                    <h5 class="form-section-title"><i class="bi bi-chat-left-text me-2"></i>Cevaplar ({{ $question->answers->count() }})</h5>

                    @forelse($question->answers as $answer)
                        <div class="border-bottom border-secondary pb-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="usr-meta">
                                    <i class="bi bi-person me-1"></i>{{ $answer->user?->name ?? 'Anonim' }}
                                    <span class="ms-2"><i class="bi bi-calendar me-1"></i>{{ $answer->created_at->format('d M Y H:i') }}</span>
                                </span>
                                <div class="d-flex align-items-center gap-2">
                                    @if($answer->status->value === 'approved')
                                        <span class="usr-status-badge usr-status-badge-green">Onaylı</span>
                                    @elseif($answer->status->value === 'pending')
                                        <span class="usr-status-badge usr-status-badge-orange">Bekliyor</span>
                                    @else
                                        <span class="usr-status-badge usr-status-badge-red">Reddedildi</span>
                                    @endif
                                </div>
                            </div>
                            <p class="mb-2">{!! nl2br(e($answer->body)) !!}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="usr-meta"><i class="bi bi-hand-thumbs-up me-1"></i>{{ $answer->like_count }} beğeni</span>
                                <div class="usr-actions">
                                    @if($answer->status->value === 'pending')
                                        <button class="usr-action-btn success" title="Onayla" onclick="openQnaApproveModal({{ $answer->id }}, '{{ addslashes(Str::limit($answer->body, 40)) }}', 'answer')">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button class="usr-action-btn warning" title="Reddet" onclick="openQnaRejectModal({{ $answer->id }}, '{{ addslashes(Str::limit($answer->body, 40)) }}', 'answer')">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    @endif
                                    <button class="usr-action-btn danger" title="Sil" onclick="openQnaDeleteModal({{ $answer->id }}, '{{ addslashes(Str::limit($answer->body, 40)) }}', 'answer')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-clr-secondary">Bu soruya henüz cevap yazılmamış.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right: Info -->
        <div class="col-lg-4">
            <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card-body-custom">
                    <h5 class="form-section-title"><i class="bi bi-info-circle me-2"></i>Bilgiler</h5>
                    <div class="mb-2">
                        <span class="text-clr-secondary">Kategori:</span>
                        <span class="cl-category-badge tech ms-1">{{ $question->category?->name }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-clr-secondary">IP Adresi:</span>
                        <span class="ms-1">{{ $question->ip_address ?? '-' }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-clr-secondary">Oluşturulma:</span>
                        <span class="ms-1">{{ $question->created_at->format('d M Y H:i') }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-clr-secondary">Slug:</span>
                        <span class="ms-1">{{ $question->slug }}</span>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="250">
                <div class="card-body-custom">
                    <h5 class="form-section-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Tehlikeli Bölge</h5>
                    <button class="btn-teal btn-danger-gradient w-100" onclick="openQnaDeleteModal({{ $question->id }}, '{{ addslashes(Str::limit($question->title, 40)) }}', 'question')">
                        <i class="bi bi-trash me-1"></i>Soruyu Sil
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
