@extends('layouts.admin')

@section('title', 'Yazar Başvurusu Detayı')

@section('content')

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Ana Sayfa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.writer-applications.index') }}">Yazar Başvuruları</a></li>
            <li class="breadcrumb-item active" aria-current="page">Başvuru Detayı</li>
        </ol>
    </nav>

    {{-- Page Header --}}
    <div class="page-header d-flex align-items-start align-items-sm-center justify-content-between flex-column flex-sm-row gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.writer-applications.index') }}" class="btn-glass" title="Geri Dön">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="page-title">Başvuru Detayı</h1>
                <p class="page-subtitle">Yazar başvurusunu inceleyin ve değerlendirin</p>
            </div>
        </div>
        <div>
            @php
                $badgeMap = [
                    'pending' => 'usr-status-badge-orange',
                    'active' => 'usr-status-badge-green',
                    'inactive' => 'usr-status-badge-red',
                ];
                $badgeClass = $badgeMap[$application->status->badgeClass()] ?? 'usr-status-badge-orange';
            @endphp
            <span class="usr-status-badge {{ $badgeClass }}">
                {{ $application->status->label() }}
            </span>
        </div>
    </div>

    <div class="row g-4">

        {{-- Left: Motivation --}}
        <div class="col-lg-8">
            <div class="card-dark">
                <div class="card-header-custom">
                    <div class="form-section-header">
                        <div class="form-section-icon bg-icon-teal">
                            <i class="bi bi-chat-left-text"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Motivasyon Metni</h6>
                            <small class="text-clr-secondary">Başvuranın yazar olmak isteme gerekçesi</small>
                        </div>
                    </div>
                </div>
                <div class="card-body-custom">
                    <div class="p-3" style="background: rgba(255,255,255,.03); border-radius: 0.5rem; border: 1px solid rgba(255,255,255,.06);">
                        <p class="mb-0 text-white" style="line-height: 1.8; white-space: pre-wrap;">{{ $application->motivation }}</p>
                    </div>
                </div>
            </div>

            {{-- Admin Note (if rejected) --}}
            @if($application->admin_note)
                <div class="card-dark mt-4">
                    <div class="card-header-custom">
                        <div class="form-section-header">
                            <div class="form-section-icon bg-icon-orange">
                                <i class="bi bi-chat-right-text"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Değerlendirme Notu</h6>
                                <small class="text-clr-secondary">
                                    {{ $application->reviewer->name ?? 'Admin' }} tarafından
                                    {{ $application->reviewed_at?->format('d.m.Y H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body-custom">
                        <div class="p-3" style="background: rgba(231,76,60,.05); border-radius: 0.5rem; border: 1px solid rgba(231,76,60,.15);">
                            <p class="mb-0 text-white" style="white-space: pre-wrap;">{{ $application->admin_note }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Right: User info + Actions --}}
        <div class="col-lg-4">

            {{-- User Info Card --}}
            <div class="card-dark mb-4">
                <div class="card-header-custom">
                    <div class="form-section-header">
                        <div class="form-section-icon bg-icon-blue">
                            <i class="bi bi-person"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Başvuran Bilgisi</h6>
                            <small class="text-clr-secondary">Kullanıcı detayları</small>
                        </div>
                    </div>
                </div>
                <div class="card-body-custom">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($application->user->name ?? '?') }}&background=1a1a2e&color=c8a96e&bold=true&size=48"
                             alt="{{ $application->user->name }}"
                             class="rounded-circle"
                             width="48" height="48"
                             loading="lazy">
                        <div>
                            <strong class="d-block text-white">{{ $application->user->name }}</strong>
                            <small class="text-clr-secondary">{{ $application->user->email }}</small>
                        </div>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        @if($application->user->username)
                            <div class="text-clr-secondary">
                                <i class="bi bi-at me-2 text-teal"></i>{{ $application->user->username }}
                            </div>
                        @endif
                        <div class="text-clr-secondary">
                            <i class="bi bi-calendar me-2 text-teal"></i>Kayıt: {{ $application->user->created_at->format('d.m.Y') }}
                        </div>
                        <div class="text-clr-secondary">
                            <i class="bi bi-send me-2 text-teal"></i>Başvuru: {{ $application->created_at->format('d.m.Y H:i') }}
                        </div>
                        @if($application->reviewed_at)
                            <div class="text-clr-secondary">
                                <i class="bi bi-check2-square me-2 text-teal"></i>Değerlendirme: {{ $application->reviewed_at->format('d.m.Y H:i') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Action Card (only for pending) --}}
            @if($application->status === \App\Enums\WriterApplicationStatus::Pending)
                <div class="card-dark mb-4">
                    <div class="card-header-custom">
                        <div class="form-section-header">
                            <div class="form-section-icon bg-icon-purple">
                                <i class="bi bi-lightning"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">İşlemler</h6>
                                <small class="text-clr-secondary">Başvuruyu değerlendirin</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body-custom">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn-teal w-100" onclick="openShowConfirmModal('approve')">
                                <i class="bi bi-check-circle me-1"></i>Onayla
                            </button>
                            <button type="button" class="btn-glass w-100 text-danger" onclick="toggleRejectForm()">
                                <i class="bi bi-x-circle me-1"></i>Reddet
                            </button>
                        </div>

                        {{-- Reject Form (hidden by default) --}}
                        <div class="mt-3 d-none" id="rejectForm">
                            <form method="POST" action="{{ route('admin.writer-applications.reject', $application) }}">
                                @csrf
                                @method('PATCH')
                                <div class="mb-2">
                                    <label class="form-label text-white">
                                        Red Gerekçesi <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control"
                                              name="admin_note"
                                              rows="4"
                                              required
                                              minlength="10"
                                              maxlength="1000"
                                              placeholder="Başvurunun neden reddedildiğini açıklayın..."></textarea>
                                    <small class="text-clr-secondary">Bu not kullanıcıya e-posta ile gönderilecektir.</small>
                                </div>
                                @error('admin_note')
                                    <div class="text-danger small mb-2">{{ $message }}</div>
                                @enderror
                                <button type="submit" class="btn-teal btn-danger-gradient w-100">
                                    <i class="bi bi-x-circle me-1"></i>Reddet ve Bildirim Gönder
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Reviewed Info (if already reviewed) --}}
            @if($application->reviewer)
                <div class="card-dark">
                    <div class="card-header-custom">
                        <div class="form-section-header">
                            <div class="form-section-icon bg-icon-orange">
                                <i class="bi bi-person-check"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Değerlendiren</h6>
                                <small class="text-clr-secondary">{{ $application->reviewed_at?->format('d.m.Y H:i') }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body-custom">
                        <strong class="text-white">{{ $application->reviewer->name }}</strong>
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- Approve Modal --}}
    @if($application->status === \App\Enums\WriterApplicationStatus::Pending)
        <div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center p-4">
                        <div class="status-modal-icon success mb-3">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <h5 class="cl-modal-heading">Başvuruyu Onayla</h5>
                        <p class="cl-modal-body-text">
                            <strong>{{ $application->user->name }}</strong> adlı kullanıcının yazar başvurusunu onaylamak istediğinize emin misiniz?
                        </p>
                        <p class="cl-modal-body-text small text-clr-secondary">
                            Kullanıcının rolü otomatik olarak "Yazar" olarak yükseltilecek ve bildirim e-postası gönderilecektir.
                        </p>
                        <div class="d-flex gap-2 justify-content-center mt-3">
                            <button type="button" class="btn-glass" data-bs-dismiss="modal">Vazgeç</button>
                            <form method="POST" action="{{ route('admin.writer-applications.approve', $application) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn-teal">
                                    <i class="bi bi-check-circle me-1"></i>Onayla
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
<script>
    function toggleRejectForm() {
        var form = document.getElementById('rejectForm');
        if (form) {
            form.classList.toggle('d-none');
            if (!form.classList.contains('d-none')) {
                form.querySelector('textarea').focus();
            }
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
