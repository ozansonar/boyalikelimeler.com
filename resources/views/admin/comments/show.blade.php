@extends('layouts.admin')

@section('title', 'Yorum Detayı — Admin')

@section('content')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb breadcrumb-reset fs-13">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link"><i class="bi bi-house me-1"></i>Ana Sayfa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.comments.index') }}" class="breadcrumb-link">Yorumlar</a></li>
            <li class="breadcrumb-item active text-teal">Yorum Detayı</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header d-flex align-items-start align-items-sm-center justify-content-between flex-column flex-sm-row gap-3 mb-4" data-aos="fade-down">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.comments.index') }}" class="btn-glass" title="Geri Dön"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h1 class="page-title mb-0">Yorum Detayı</h1>
                <p class="page-subtitle mb-0">{{ $comment->fullName() }} tarafından yapılan yorum</p>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @if($comment->is_approved)
                <span class="usr-status-badge usr-status-badge-green">Onaylı</span>
            @else
                <span class="usr-status-badge usr-status-badge-orange">Onay Bekliyor</span>
            @endif
            <a href="{{ route('admin.comments.edit', $comment) }}" class="btn-glass"><i class="bi bi-pencil me-1"></i>Düzenle</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
        </div>
    @endif

    <div class="row g-4">

        <!-- Sol: Yorum İçeriği -->
        <div class="col-lg-8">

            <!-- Yorum Bilgileri -->
            <div class="card-dark mb-4" data-aos="fade-up">
                <div class="card-header-custom">
                    <div class="form-section-header mb-0">
                        <div class="form-section-icon bg-icon-teal"><i class="bi bi-chat-left-text"></i></div>
                        <div>
                            <h6 class="mb-0">Yorum</h6>
                            <small class="text-muted">{{ $comment->created_at->translatedFormat('d F Y, H:i') }}</small>
                        </div>
                    </div>
                </div>
                <div class="card-body-custom">
                    <!-- Rating -->
                    <div class="mb-3">
                        <label class="form-label text-muted small">Puan</label>
                        <div class="d-flex align-items-center gap-2">
                            <div class="cmt-admin-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi {{ $i <= $comment->rating ? 'bi-star-fill text-warning' : 'bi-star text-clr-secondary' }}" style="font-size: 1.1rem;"></i>
                                @endfor
                            </div>
                            <span class="text-muted small">({{ $comment->rating }}/5)</span>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="mb-0">
                        <label class="form-label text-muted small">Yorum Metni</label>
                        <div class="p-3 rounded" style="background: rgba(255,255,255,.03); border: 1px solid rgba(255,255,255,.06);">
                            <p class="mb-0" style="white-space: pre-line;">{{ $comment->body }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- İçerik Bilgisi -->
            <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card-header-custom">
                    <div class="form-section-header mb-0">
                        <div class="form-section-icon bg-icon-purple"><i class="bi bi-journal-text"></i></div>
                        <div>
                            <h6 class="mb-0">Yorum Yapılan İçerik</h6>
                            <small class="text-muted">{{ $comment->contentTypeLabel() }}</small>
                        </div>
                    </div>
                </div>
                <div class="card-body-custom">
                    @if($comment->commentable)
                        <div class="d-flex align-items-start gap-3">
                            @if($comment->commentable->cover_image)
                                <div style="width: 80px; height: 60px; border-radius: 0.5rem; overflow: hidden; flex-shrink: 0;">
                                    <img src="/uploads/{{ $comment->commentable->cover_image }}" alt="" class="img-fluid" style="width: 100%; height: 100%; object-fit: cover;" loading="lazy">
                                </div>
                            @endif
                            <div>
                                <strong>{{ $comment->commentable->title }}</strong>
                                <div class="text-muted small mt-1">
                                    <span class="cl-category-badge tech">{{ $comment->contentTypeLabel() }}</span>
                                </div>
                                @php
                                    $contentUrl = $comment->contentType() === 'icerik'
                                        ? route('literary-works.show', $comment->commentable->slug)
                                        : route('blog.show', $comment->commentable->slug);
                                @endphp
                                <a href="{{ $contentUrl }}" target="_blank" class="text-teal small d-inline-block mt-2">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>İçeriği yeni sekmede görüntüle
                                </a>
                            </div>
                        </div>
                    @else
                        <p class="text-muted mb-0"><i class="bi bi-exclamation-triangle me-1"></i>İçerik silinmiş veya bulunamadı.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sağ: Kişi Bilgisi + Aksiyonlar -->
        <div class="col-lg-4">

            <!-- Yorumcu Bilgisi -->
            <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="50">
                <div class="card-header-custom">
                    <div class="form-section-header mb-0">
                        <div class="form-section-icon bg-icon-blue"><i class="bi bi-person"></i></div>
                        <div>
                            <h6 class="mb-0">Yorumcu Bilgisi</h6>
                            <small class="text-muted">Yorumu yazan kişi</small>
                        </div>
                    </div>
                </div>
                <div class="card-body-custom">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="cmt-admin-avatar-lg">{{ mb_strtoupper(mb_substr($comment->first_name, 0, 1)) }}{{ mb_strtoupper(mb_substr($comment->last_name, 0, 1)) }}</div>
                        <div>
                            <strong>{{ $comment->fullName() }}</strong>
                            <div class="text-muted small">{{ $comment->email }}</div>
                        </div>
                    </div>
                    <div class="d-flex flex-column gap-1">
                        <div class="text-muted small">
                            <i class="bi bi-calendar me-1"></i>Gönderim: {{ $comment->created_at->format('d.m.Y H:i') }}
                        </div>
                        @if($comment->ip_address)
                            <div class="text-muted small">
                                <i class="bi bi-globe me-1"></i>IP: {{ $comment->ip_address }}
                            </div>
                        @endif
                        @if($comment->is_approved && $comment->approved_at)
                            <div class="text-muted small">
                                <i class="bi bi-check-circle me-1 text-neon-green"></i>Onay: {{ $comment->approved_at->format('d.m.Y H:i') }}
                                @if($comment->approver)
                                    — {{ $comment->approver->name }}
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Durum İşlemleri -->
            <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card-header-custom">
                    <div class="form-section-header mb-0">
                        <div class="form-section-icon bg-icon-purple"><i class="bi bi-lightning"></i></div>
                        <div>
                            <h6 class="mb-0">Durum İşlemleri</h6>
                            <small class="text-muted">Yorum durumunu değiştirin</small>
                        </div>
                    </div>
                </div>
                <div class="card-body-custom">
                    <div class="d-grid gap-2">
                        @if(!$comment->is_approved)
                            <button type="button" class="btn-teal w-100" onclick="openShowActionModal('approve')">
                                <i class="bi bi-check-circle me-1"></i>Onayla
                            </button>
                        @endif
                        @if($comment->is_approved)
                            <button type="button" class="btn-glass w-100 text-danger" onclick="openShowActionModal('reject')">
                                <i class="bi bi-x-circle me-1"></i>Reddet
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tehlikeli İşlemler -->
            <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="150">
                <div class="card-header-custom">
                    <div class="form-section-header mb-0">
                        <div class="form-section-icon bg-icon-red"><i class="bi bi-shield-exclamation"></i></div>
                        <div>
                            <h6 class="mb-0">Tehlikeli İşlemler</h6>
                            <small class="text-muted">Dikkatli olun</small>
                        </div>
                    </div>
                </div>
                <div class="card-body-custom">
                    <button type="button" class="btn-glass w-100 text-danger" onclick="openShowActionModal('delete')">
                        <i class="bi bi-trash me-1"></i>Yorumu Kalıcı Olarak Sil
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="status-modal-icon success"><i class="bi bi-check-circle"></i></div>
                    <h5 class="cl-modal-heading">Yorumu Onayla</h5>
                    <p class="cl-modal-body-text">Bu yorumu onaylamak ve yayına almak istediğinize emin misiniz?</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn-glass" data-bs-dismiss="modal">Vazgeç</button>
                        <button type="button" class="btn-teal" id="showApproveBtn"><i class="bi bi-check-circle me-1"></i>Onayla</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="status-modal-icon danger"><i class="bi bi-x-circle"></i></div>
                    <h5 class="cl-modal-heading">Yorumu Reddet</h5>
                    <p class="cl-modal-body-text">Bu yorumu reddetmek istediğinize emin misiniz?</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn-glass" data-bs-dismiss="modal">Vazgeç</button>
                        <button type="button" class="btn-teal btn-danger-gradient" id="showRejectBtn"><i class="bi bi-x-circle me-1"></i>Reddet</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="status-modal-icon danger"><i class="bi bi-trash"></i></div>
                    <h5 class="cl-modal-heading">Yorumu Sil</h5>
                    <p class="cl-modal-body-text">Bu yorumu kalıcı olarak silmek istediğinize emin misiniz?</p>
                    <p class="cl-modal-warning"><i class="bi bi-exclamation-triangle me-1"></i>Bu işlem geri alınamaz.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn-glass" data-bs-dismiss="modal">Vazgeç</button>
                        <button type="button" class="btn-teal btn-danger-gradient" id="showDeleteBtn"><i class="bi bi-trash me-1"></i>Sil</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var commentId = {{ $comment->id }};

    function openShowActionModal(type) {
        var modal = document.getElementById(type + 'Modal');
        if (modal) {
            var bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        }
    }
    window.openShowActionModal = openShowActionModal;

    function ajaxAction(url, method, successMsg, redirectUrl) {
        fetch(url, {
            method: method,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                showToast(data.message || successMsg, 'success');
                setTimeout(function () { window.location.href = redirectUrl || window.location.href; }, 800);
            } else {
                showToast(data.message || 'Bir hata oluştu.', 'danger');
            }
        })
        .catch(function () {
            showToast('Bir hata oluştu. Lütfen tekrar deneyin.', 'danger');
        });
    }

    var approveBtn = document.getElementById('showApproveBtn');
    if (approveBtn) {
        approveBtn.addEventListener('click', function () {
            ajaxAction('/admin/comments/' + commentId + '/approve', 'PATCH', 'Yorum başarıyla onaylandı.', window.location.href);
        });
    }

    var rejectBtn = document.getElementById('showRejectBtn');
    if (rejectBtn) {
        rejectBtn.addEventListener('click', function () {
            ajaxAction('/admin/comments/' + commentId + '/reject', 'PATCH', 'Yorum reddedildi.', window.location.href);
        });
    }

    var deleteBtn = document.getElementById('showDeleteBtn');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function () {
            ajaxAction('/admin/comments/' + commentId, 'DELETE', 'Yorum başarıyla silindi.', '{{ route("admin.comments.index") }}');
        });
    }
})();
</script>
@endpush
