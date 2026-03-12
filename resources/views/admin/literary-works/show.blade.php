@extends('layouts.admin')

@section('title', $work->title . ' — Edebiyat Eseri')

@section('content')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb breadcrumb-reset fs-13">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link"><i class="bi bi-house me-1"></i>Ana Sayfa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.literary-works.index') }}" class="breadcrumb-link">Edebiyat Eserleri</a></li>
            <li class="breadcrumb-item active text-teal">{{ Str::limit($work->title, 30) }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header d-flex align-items-start align-items-sm-center justify-content-between flex-column flex-sm-row gap-3 mb-4" data-aos="fade-down">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.literary-works.index') }}" class="btn-glass" title="Geri Dön"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h1 class="page-title mb-0">Eser Detayı</h1>
                <p class="page-subtitle mb-0">Eseri inceleyin ve yönetin</p>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <span class="usr-status-badge {{ $work->status->badgeClass() }}">{{ $work->status->label() }}</span>
            <a href="{{ route('admin.literary-works.edit', $work) }}" class="btn-glass"><i class="bi bi-pencil me-1"></i>Düzenle</a>
        </div>
    </div>

    <div class="row g-4">

        <!-- Sol: Eser İçeriği -->
        <div class="col-lg-8">

            <!-- Eser Bilgileri -->
            <div class="card-dark mb-4" data-aos="fade-up">
                <div class="card-header-custom">
                    <div class="form-section-header mb-0">
                        <div class="form-section-icon bg-icon-teal"><i class="bi bi-journal-text"></i></div>
                        <div>
                            <h6 class="mb-0">{{ $work->title }}</h6>
                            <small class="text-muted">{{ $work->category?->name ?? '-' }} &bull; ~{{ $work->readingTime() }} dk okuma</small>
                        </div>
                    </div>
                </div>
                <div class="card-body-custom">
                    @if($work->cover_image)
                        <div class="mb-3">
                            <img src="/uploads/{{ $work->cover_image }}" alt="{{ $work->title }}" class="img-fluid rounded" loading="lazy">
                        </div>
                    @endif

                    @if($work->excerpt)
                        <div class="mb-3 p-3 rounded" style="background: rgba(255,255,255,.03); border-left: 3px solid var(--neon-teal);">
                            <small class="text-muted d-block mb-1">Kısa Özet</small>
                            <p class="mb-0">{{ $work->excerpt }}</p>
                        </div>
                    @endif

                    <div class="literary-work-body clearfix">
                        {!! $work->body !!}
                    </div>
                </div>
            </div>

            <!-- Revizyon Geçmişi -->
            @if($work->revisions->isNotEmpty())
                <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-header-custom">
                        <div class="form-section-header mb-0">
                            <div class="form-section-icon bg-icon-orange"><i class="bi bi-clock-history"></i></div>
                            <div>
                                <h6 class="mb-0">Revizyon Geçmişi</h6>
                                <small class="text-muted">Admin talepleri ve yazar yanıtları</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body-custom">
                        @foreach($work->revisions as $revision)
                            <div class="mb-3 p-3 rounded" style="background: rgba(255,255,255,.03); border: 1px solid rgba(255,255,255,.06);">
                                <div class="d-flex justify-content-between mb-2">
                                    <div>
                                        <strong>{{ $revision->admin?->name ?? 'Admin' }}</strong>
                                        <span class="text-muted ms-2">revize istedi</span>
                                    </div>
                                    <small class="text-muted">{{ $revision->created_at->format('d.m.Y H:i') }}</small>
                                </div>
                                <p class="mb-1"><i class="bi bi-chat-left-text me-1 text-neon-orange"></i>{{ $revision->reason }}</p>
                                @if($revision->author_note)
                                    <p class="mb-0 mt-2"><i class="bi bi-reply me-1 text-neon-green"></i><strong>Yazar notu:</strong> {{ $revision->author_note }}</p>
                                @endif
                                @if($revision->is_resolved)
                                    <span class="usr-status-badge active mt-2 d-inline-block"><i class="bi bi-check me-1"></i>Çözümlendi</span>
                                @else
                                    <span class="usr-status-badge pending mt-2 d-inline-block"><i class="bi bi-hourglass me-1"></i>Bekliyor</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sağ: Yazar Bilgisi + İstatistik + Aksiyonlar -->
        <div class="col-lg-4">

            <!-- Yazar Bilgisi -->
            <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="50">
                <div class="card-header-custom">
                    <div class="form-section-header mb-0">
                        <div class="form-section-icon bg-icon-blue"><i class="bi bi-person"></i></div>
                        <div>
                            <h6 class="mb-0">Yazar Bilgisi</h6>
                            <small class="text-muted">Eseri gönderen yazar</small>
                        </div>
                    </div>
                </div>
                <div class="card-body-custom">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($work->author?->name ?? 'U') }}&background=14b8a6&color=fff&size=48" alt="" class="rounded-circle">
                        <div>
                            <strong>{{ $work->author?->name ?? '-' }}</strong>
                            <div class="text-muted small">{{ $work->author?->email ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="d-flex flex-column gap-1">
                        <div class="text-muted small">
                            <i class="bi bi-bookmark me-1"></i>Tür: <span class="usr-status-badge {{ $work->work_type?->badgeClass() ?? 'active' }}">{{ $work->work_type?->label() ?? 'Yazılı Eser' }}</span>
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-calendar me-1"></i>Gönderim: {{ $work->created_at->format('d.m.Y H:i') }}
                        </div>
                        @if($work->published_at)
                            <div class="text-muted small">
                                <i class="bi bi-check-circle me-1"></i>Yayın: {{ $work->published_at->format('d.m.Y H:i') }}
                            </div>
                        @endif
                        <div class="text-muted small">
                            <i class="bi bi-eye me-1"></i>Görüntülenme: <strong class="text-teal">{{ number_format($work->view_count) }}</strong>
                        </div>
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
                            <small class="text-muted">Eser durumunu değiştirin</small>
                        </div>
                    </div>
                </div>
                <div class="card-body-custom">
                    @if($work->status === \App\Enums\LiteraryWorkStatus::Unpublished)
                        <div class="mb-3 p-3 rounded" style="background: rgba(234, 179, 8, .08); border: 1px solid rgba(234, 179, 8, .2);">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-eye-slash text-neon-yellow mt-1"></i>
                                <div>
                                    <strong class="text-neon-yellow">Yazar Tarafından Kaldırıldı</strong>
                                    <p class="text-muted small mb-0 mt-1">Bu eser yazar tarafından yayından kaldırılmıştır.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="d-grid gap-2">
                        @if($work->status !== \App\Enums\LiteraryWorkStatus::Approved)
                            <button type="button" class="btn-teal w-100" onclick="openShowConfirmModal('approve')">
                                <i class="bi bi-check-circle me-1"></i>Onayla
                            </button>
                        @endif

                        @if($work->status === \App\Enums\LiteraryWorkStatus::Approved)
                            <button type="button" class="btn-glass w-100 text-neon-yellow" onclick="openShowConfirmModal('unpublish')">
                                <i class="bi bi-eye-slash me-1"></i>Yayından Kaldır
                            </button>
                        @endif

                        @if(!in_array($work->status, [\App\Enums\LiteraryWorkStatus::Rejected, \App\Enums\LiteraryWorkStatus::Unpublished]))
                            <button type="button" class="btn-glass w-100 text-danger" onclick="openShowConfirmModal('reject')">
                                <i class="bi bi-x-circle me-1"></i>Reddet
                            </button>
                        @endif

                        @if($work->status !== \App\Enums\LiteraryWorkStatus::Unpublished)
                            <button type="button" class="btn-glass w-100" onclick="toggleRevisionForm()">
                                <i class="bi bi-arrow-repeat me-1"></i>Revize İste
                            </button>
                        @endif
                    </div>

                    <!-- Revize Form (hidden by default) -->
                    <div class="mt-3 d-none" id="revisionForm">
                        <form method="POST" action="{{ route('admin.literary-works.revision', $work) }}">
                            @csrf
                            <div class="mb-2">
                                <label class="form-label">Revize Sebebi <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('reason') is-invalid @enderror"
                                          name="reason" rows="4"
                                          placeholder="Yazarın düzeltmesi gereken noktaları açıklayın..."
                                          required minlength="10" maxlength="2000">{{ old('reason') }}</textarea>
                                @error('reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">En az 10, en fazla 2000 karakter</div>
                            </div>
                            <button type="submit" class="btn-teal w-100">
                                <i class="bi bi-send me-1"></i>Revize Talebini Gönder
                            </button>
                        </form>
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
                            <small class="text-muted">Dikkatli olun, bu işlemler geri alınamaz</small>
                        </div>
                    </div>
                </div>
                <div class="card-body-custom">
                    <button type="button" class="btn-glass w-100 text-danger" onclick="openShowConfirmModal('delete')">
                        <i class="bi bi-trash me-1"></i>Eseri Kalıcı Olarak Sil
                    </button>
                </div>
            </div>

        </div>

    </div>

    <!-- Approve Confirm Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="status-modal-icon success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <h5 class="cl-modal-heading">Eseri Onayla</h5>
                    <p class="cl-modal-body-text">Bu eseri onaylamak ve yayına almak istediğinize emin misiniz?</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn-glass" data-bs-dismiss="modal">Vazgeç</button>
                        <form method="POST" action="{{ route('admin.literary-works.approve', $work) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-teal"><i class="bi bi-check-circle me-1"></i>Onayla</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Confirm Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="status-modal-icon danger">
                        <i class="bi bi-x-circle"></i>
                    </div>
                    <h5 class="cl-modal-heading">Eseri Reddet</h5>
                    <p class="cl-modal-body-text">Bu eseri reddetmek istediğinize emin misiniz?</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn-glass" data-bs-dismiss="modal">Vazgeç</button>
                        <form method="POST" action="{{ route('admin.literary-works.reject', $work) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-teal btn-danger-gradient"><i class="bi bi-x-circle me-1"></i>Reddet</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Unpublish Confirm Modal -->
    <div class="modal fade" id="unpublishModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="status-modal-icon warning">
                        <i class="bi bi-eye-slash"></i>
                    </div>
                    <h5 class="cl-modal-heading">Yayından Kaldır</h5>
                    <p class="cl-modal-body-text">Bu eseri yayından kaldırmak istediğinize emin misiniz?</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn-glass" data-bs-dismiss="modal">Vazgeç</button>
                        <form method="POST" action="{{ route('admin.literary-works.unpublish', $work) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-teal btn-warning-gradient"><i class="bi bi-eye-slash me-1"></i>Kaldır</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirm Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="status-modal-icon danger">
                        <i class="bi bi-trash"></i>
                    </div>
                    <h5 class="cl-modal-heading">Eseri Sil</h5>
                    <p class="cl-modal-body-text">Bu eseri kalıcı olarak silmek istediğinize emin misiniz?</p>
                    <p class="cl-modal-warning"><i class="bi bi-exclamation-triangle me-1"></i>Bu işlem geri alınamaz.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn-glass" data-bs-dismiss="modal">Vazgeç</button>
                        <form method="POST" action="{{ route('admin.literary-works.destroy', $work) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-teal btn-danger-gradient"><i class="bi bi-trash me-1"></i>Sil</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
function toggleRevisionForm() {
    var form = document.getElementById('revisionForm');
    form.classList.toggle('d-none');
    if (!form.classList.contains('d-none')) {
        form.querySelector('textarea').focus();
    }
}

function openShowConfirmModal(type) {
    var modal = document.getElementById(type + 'Modal');
    if (modal) {
        var bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    }
}
</script>
@endpush
