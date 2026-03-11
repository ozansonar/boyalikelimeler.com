@extends('layouts.admin')

@section('title', 'Cevaplar — Günün Sorusu — Admin')

@section('content')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb breadcrumb-reset fs-13">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link"><i class="bi bi-house me-1"></i>Ana Sayfa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.daily-questions.index') }}" class="breadcrumb-link">Günün Sorusu</a></li>
            <li class="breadcrumb-item active text-teal">Cevaplar</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4" data-aos="fade-down">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.daily-questions.index') }}" class="btn-glass" title="Geri Dön"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h1 class="page-title">Gelen Cevaplar</h1>
                <p class="page-subtitle">{{ Str::limit($question->question_text, 80) }}</p>
            </div>
        </div>
        <div class="d-flex gap-2 align-items-center">
            @if($question->status === 'published')
                <span class="usr-status-badge active">Yayında</span>
            @elseif($question->status === 'draft')
                <span class="usr-status-badge pending">Taslak</span>
            @else
                <span class="usr-status-badge inactive">Arşiv</span>
            @endif
            <span class="usr-meta"><i class="bi bi-calendar me-1"></i>{{ $question->published_at->format('d.m.Y') }}</span>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card-dark" data-aos="fade-up">
                <div class="card-body-custom text-center py-4">
                    <div class="fs-2 fw-bold text-teal">{{ $answers->count() }}</div>
                    <small class="text-muted">Toplam Cevap</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-dark" data-aos="fade-up" data-aos-delay="50">
                <div class="card-body-custom text-center py-4">
                    <div class="fs-2 fw-bold text-teal">{{ $answers->whereNotNull('user_id')->count() }}</div>
                    <small class="text-muted">Kayıtlı Kullanıcı</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-dark" data-aos="fade-up" data-aos-delay="100">
                <div class="card-body-custom text-center py-4">
                    <div class="fs-2 fw-bold text-teal">{{ $answers->whereNull('user_id')->count() }}</div>
                    <small class="text-muted">Anonim Ziyaretçi</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Answers Table -->
    <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="150">
        <div class="card-body-custom p-0">
            <div class="table-responsive">
                <table class="table table-hover cl-table mb-0">
                    <thead>
                        <tr>
                            <th>Cevap</th>
                            <th class="d-none d-md-table-cell">Kullanıcı</th>
                            <th class="d-none d-lg-table-cell">IP</th>
                            <th class="d-none d-md-table-cell">Tarih</th>
                            <th class="cl-th-actions">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($answers as $answer)
                            <tr data-aos="fade-right" data-aos-delay="{{ $loop->index * 30 }}">
                                <td data-label="Cevap">
                                    <div class="cl-content-cell">
                                        <div class="cl-content-info">
                                            <span class="cl-content-title">{{ Str::limit($answer->answer_text, 80) }}</span>
                                            <span class="cl-content-meta d-md-none">
                                                <i class="bi bi-person me-1"></i>{{ $answer->user?->name ?? 'Anonim' }}
                                                <span class="cl-separator">|</span>
                                                <i class="bi bi-clock me-1"></i>{{ $answer->created_at->format('d.m.Y H:i') }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="Kullanıcı" class="d-none d-md-table-cell">
                                    @if($answer->user)
                                        <span class="usr-meta"><i class="bi bi-person-fill me-1"></i>{{ $answer->user->name }}</span>
                                    @else
                                        <span class="usr-meta text-muted"><i class="bi bi-person me-1"></i>Anonim</span>
                                    @endif
                                </td>
                                <td data-label="IP" class="d-none d-lg-table-cell">
                                    <span class="usr-meta">{{ $answer->ip_address }}</span>
                                </td>
                                <td data-label="Tarih" class="d-none d-md-table-cell">
                                    <span class="usr-meta"><i class="bi bi-clock me-1"></i>{{ $answer->created_at->format('d.m.Y H:i') }}</span>
                                </td>
                                <td data-label="İşlem">
                                    <div class="usr-actions">
                                        <button class="usr-action-btn danger" title="Sil" onclick="openDeleteAnswerModal({{ $answer->id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-admin.table-empty :colspan="5" icon="bi-chat-text" message="Henüz cevap gelmemiş." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="card-dark mb-4">
        <div class="card-body-custom">
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.daily-questions.index') }}" class="btn-glass">
                    <i class="bi bi-arrow-left me-1"></i>Listeye Dön
                </a>
                <a href="{{ route('admin.daily-questions.edit', $question) }}" class="btn-teal">
                    <i class="bi bi-pencil me-1"></i>Düzenle
                </a>
            </div>
        </div>
    </div>

    <!-- Delete Answer Modal -->
    <div class="modal fade" id="deleteAnswerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="status-modal-icon danger">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <h5 class="cl-modal-heading">Cevabı Sil</h5>
                    <p class="cl-modal-body-text">Bu cevabı silmek istediğinize emin misiniz?</p>
                    <p class="cl-modal-warning"><i class="bi bi-exclamation-circle me-1"></i>Bu işlem geri alınamaz.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn-glass" data-bs-dismiss="modal">Vazgeç</button>
                        <form id="deleteAnswerForm" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-teal btn-danger-gradient">
                                <i class="bi bi-trash me-1"></i>Sil
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
function openDeleteAnswerModal(answerId) {
    document.getElementById('deleteAnswerForm').action = '/admin/daily-questions/{{ $question->id }}/answers/' + answerId;
    var modal = new bootstrap.Modal(document.getElementById('deleteAnswerModal'));
    modal.show();
}
</script>
@endpush
