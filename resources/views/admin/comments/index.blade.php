@extends('layouts.admin')

@section('title', 'Yorumlar — Admin')

@section('content')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3" data-aos="fade-down" data-aos-duration="400">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link"><i class="bi bi-house me-1"></i>Ana Sayfa</a>
            </li>
            <li class="breadcrumb-item active text-teal">Yorumlar</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4" data-aos="fade-down">
        <div>
            <h1 class="page-title">Yorumlar</h1>
            <p class="page-subtitle">İçerik ve blog yazılarına yapılan yorumları yönetin</p>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row g-4 mb-4">
        <x-admin.stat-card color="blue" icon="bi-chat-left-text-fill" label="Toplam Yorum" :count="$stats['total']" :delay="0" col-class="col-xxl-3 col-xl-6 col-sm-6" />
        <x-admin.stat-card color="orange" icon="bi-hourglass-split" label="Onay Bekleyen" :count="$stats['pending']" :delay="50" col-class="col-xxl-3 col-xl-6 col-sm-6" />
        <x-admin.stat-card color="green" icon="bi-check-circle-fill" label="Onaylanan" :count="$stats['approved']" :delay="100" col-class="col-xxl-3 col-xl-6 col-sm-6" />
        <x-admin.stat-card color="purple" icon="bi-journal-text" label="İçerik Yorumu" :count="$stats['icerik']" :delay="150" col-class="col-xxl-3 col-xl-6 col-sm-6" />
    </div>

    <!-- Status Tabs -->
    <div class="cl-status-tabs mb-4" data-aos="fade-up" data-aos-delay="100">
        <a href="{{ route('admin.comments.index', array_merge(request()->except(['status', 'page']), [])) }}" class="cl-status-tab {{ empty($filters['status']) ? 'active' : '' }}">
            <span>Tümü</span>
            <span class="cl-tab-count">{{ $stats['total'] }}</span>
        </a>
        <a href="{{ route('admin.comments.index', array_merge(request()->except('page'), ['status' => 'pending'])) }}" class="cl-status-tab {{ ($filters['status'] ?? '') === 'pending' ? 'active' : '' }}">
            <i class="bi bi-hourglass-split text-neon-orange"></i>
            <span>Onay Bekleyen</span>
            <span class="cl-tab-count">{{ $stats['pending'] }}</span>
        </a>
        <a href="{{ route('admin.comments.index', array_merge(request()->except('page'), ['status' => 'approved'])) }}" class="cl-status-tab {{ ($filters['status'] ?? '') === 'approved' ? 'active' : '' }}">
            <i class="bi bi-check-circle text-neon-green"></i>
            <span>Onaylı</span>
            <span class="cl-tab-count">{{ $stats['approved'] }}</span>
        </a>
    </div>

    <!-- Type Tabs -->
    <div class="cl-status-tabs mb-4" data-aos="fade-up" data-aos-delay="150">
        <a href="{{ route('admin.comments.index', array_merge(request()->except(['type', 'page']), [])) }}" class="cl-status-tab {{ empty($filters['type']) ? 'active' : '' }}">
            <span>Tüm Tipler</span>
        </a>
        <a href="{{ route('admin.comments.index', array_merge(request()->except('page'), ['type' => 'icerik'])) }}" class="cl-status-tab {{ ($filters['type'] ?? '') === 'icerik' ? 'active' : '' }}">
            <i class="bi bi-journal-text text-neon-purple"></i>
            <span>İçerik</span>
            <span class="cl-tab-count">{{ $stats['icerik'] }}</span>
        </a>
        <a href="{{ route('admin.comments.index', array_merge(request()->except('page'), ['type' => 'blog'])) }}" class="cl-status-tab {{ ($filters['type'] ?? '') === 'blog' ? 'active' : '' }}">
            <i class="bi bi-file-earmark-text text-neon-blue"></i>
            <span>Blog</span>
            <span class="cl-tab-count">{{ $stats['blog'] }}</span>
        </a>
    </div>

    <!-- Toolbar -->
    <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="200">
        <div class="card-body-custom">
            <form method="GET" action="{{ route('admin.comments.index') }}" class="cl-toolbar">
                <div class="cl-search">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Ad, soyad, e-posta veya yorum ile ara...">
                </div>
                <div class="cl-toolbar-actions">
                    @if(!empty($filters['status']))
                        <input type="hidden" name="status" value="{{ $filters['status'] }}">
                    @endif
                    @if(!empty($filters['type']))
                        <input type="hidden" name="type" value="{{ $filters['type'] }}">
                    @endif
                    <button type="submit" class="btn-glass"><i class="bi bi-funnel me-1"></i>Filtrele</button>
                    @if(!empty($filters['search']))
                        <a href="{{ route('admin.comments.index', array_filter(['status' => $filters['status'] ?? null, 'type' => $filters['type'] ?? null])) }}" class="cl-filter-reset" title="Filtreleri Sıfırla">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    @endif
                    <div class="cl-per-page">
                        <label>Göster:</label>
                        <select name="per_page" onchange="this.form.submit()">
                            @foreach([10, 20, 50, 100] as $pp)
                                <option value="{{ $pp }}" {{ $perPage === $pp ? 'selected' : '' }}>{{ $pp }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="250">
        <div class="card-body-custom p-0">
            <div class="table-responsive">
                <table class="table table-hover cl-table mb-0">
                    <thead>
                        <tr>
                            <th>Yorum</th>
                            <th class="d-none d-md-table-cell">İçerik</th>
                            <th class="d-none d-lg-table-cell">Puan</th>
                            <th>Durum</th>
                            <th class="d-none d-xl-table-cell">Tarih</th>
                            <th class="cl-th-actions">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($comments as $comment)
                            <tr>
                                <td>
                                    <div class="cl-content-cell">
                                        <div class="cl-content-thumb draft">
                                            <span class="cmt-admin-avatar">{{ $comment->commenterInitials() }}</span>
                                        </div>
                                        <div class="cl-content-info">
                                            <span class="cl-content-title">
                                                {{ $comment->fullName() }}
                                                @if($comment->isByUser())
                                                    <span class="usr-status-badge usr-status-badge-blue ms-1" title="Kayıtlı Kullanıcı"><i class="bi bi-person-check"></i></span>
                                                @endif
                                            </span>
                                            <span class="cl-content-meta">
                                                <i class="bi bi-envelope me-1"></i>{{ $comment->commenterEmail() }}
                                            </span>
                                            <span class="cl-content-meta">{{ Str::limit($comment->body, 80) }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <span class="cl-category-badge tech">{{ $comment->contentTypeLabel() }}</span>
                                    @if($comment->commentable)
                                        <a href="{{ $comment->contentType() === 'icerik' ? route('literary-works.show', $comment->commentable->slug) : $comment->commentable->url() }}"
                                           target="_blank" class="cl-content-meta d-block mt-1 text-teal" title="İçeriği yeni sekmede aç">
                                            {{ Str::limit($comment->contentTitle(), 30) }} <i class="bi bi-box-arrow-up-right ms-1"></i>
                                        </a>
                                    @else
                                        <span class="cl-content-meta d-block mt-1">{{ Str::limit($comment->contentTitle(), 30) }}</span>
                                    @endif
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <div class="cmt-admin-stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi {{ $i <= $comment->rating ? 'bi-star-fill text-warning' : 'bi-star text-clr-secondary' }}"></i>
                                        @endfor
                                    </div>
                                </td>
                                <td>
                                    @if($comment->is_approved)
                                        <span class="usr-status-badge usr-status-badge-green">Onaylı</span>
                                    @else
                                        <span class="usr-status-badge usr-status-badge-orange">Bekliyor</span>
                                    @endif
                                </td>
                                <td class="d-none d-xl-table-cell">
                                    <span class="usr-meta">{{ $comment->created_at->format('d M Y H:i') }}</span>
                                </td>
                                <td>
                                    <div class="usr-actions">
                                        <a class="usr-action-btn" title="Detay" href="{{ route('admin.comments.show', $comment) }}"><i class="bi bi-eye"></i></a>
                                        <a class="usr-action-btn" title="Düzenle" href="{{ route('admin.comments.edit', $comment) }}"><i class="bi bi-pencil"></i></a>
                                        @if(!$comment->is_approved)
                                            <button class="usr-action-btn success" title="Onayla" onclick="openApproveModal({{ $comment->id }}, '{{ addslashes($comment->fullName()) }}')">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        @else
                                            <button class="usr-action-btn warning" title="Reddet" onclick="openRejectModal({{ $comment->id }}, '{{ addslashes($comment->fullName()) }}')">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        @endif
                                        <button class="usr-action-btn danger" title="Sil" onclick="openDeleteCommentModal({{ $comment->id }}, '{{ addslashes($comment->fullName()) }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-admin.table-empty :colspan="6" icon="bi-chat-left-text" message="Henüz yorum yapılmamış." />
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($comments->hasPages())
                <div class="cl-pagination-wrapper">
                    <div class="cl-pagination-info">
                        <span>Toplam <strong>{{ number_format($comments->total()) }}</strong> yorumdan <strong>{{ $comments->firstItem() }}-{{ $comments->lastItem() }}</strong> arası gösteriliyor</span>
                    </div>
                    <nav class="cl-pagination">
                        @if($comments->onFirstPage())
                            <button class="cl-page-btn" disabled><i class="bi bi-chevron-left"></i></button>
                        @else
                            <a href="{{ $comments->previousPageUrl() }}" class="cl-page-btn"><i class="bi bi-chevron-left"></i></a>
                        @endif

                        @foreach($comments->getUrlRange(max(1, $comments->currentPage() - 2), min($comments->lastPage(), $comments->currentPage() + 2)) as $page => $url)
                            <a href="{{ $url }}" class="cl-page-btn {{ $page === $comments->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                        @endforeach

                        @if($comments->hasMorePages())
                            <a href="{{ $comments->nextPageUrl() }}" class="cl-page-btn"><i class="bi bi-chevron-right"></i></a>
                        @else
                            <button class="cl-page-btn" disabled><i class="bi bi-chevron-right"></i></button>
                        @endif
                    </nav>
                </div>
            @endif
        </div>
    </div>

    <!-- Approve Confirm Modal -->
    <div class="modal fade" id="approveCommentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="status-modal-icon success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <h5 class="cl-modal-heading">Yorumu Onayla</h5>
                    <p class="cl-modal-body-text">
                        <strong id="approveCommentName"></strong> adlı kişinin yorumunu onaylamak istediğinize emin misiniz?
                    </p>
                    <p class="cl-modal-body-text small text-muted">Onaylanan yorum ilgili içeriğin altında görünecek ve yazara bildirim gönderilecektir.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn-glass" data-bs-dismiss="modal">Vazgeç</button>
                        <button type="button" class="btn-teal" id="approveCommentBtn"><i class="bi bi-check-circle me-1"></i>Onayla</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Confirm Modal -->
    <div class="modal fade" id="rejectCommentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="status-modal-icon danger">
                        <i class="bi bi-x-circle"></i>
                    </div>
                    <h5 class="cl-modal-heading">Yorumu Reddet</h5>
                    <p class="cl-modal-body-text">
                        <strong id="rejectCommentName"></strong> adlı kişinin yorumunu reddetmek istediğinize emin misiniz?
                    </p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn-glass" data-bs-dismiss="modal">Vazgeç</button>
                        <button type="button" class="btn-teal btn-danger-gradient" id="rejectCommentBtn"><i class="bi bi-x-circle me-1"></i>Reddet</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirm Modal -->
    <div class="modal fade" id="deleteCommentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="status-modal-icon danger">
                        <i class="bi bi-trash"></i>
                    </div>
                    <h5 class="cl-modal-heading">Yorumu Sil</h5>
                    <p class="cl-modal-body-text">
                        <strong id="deleteCommentName"></strong> adlı kişinin yorumunu silmek istediğinize emin misiniz?
                    </p>
                    <p class="cl-modal-warning"><i class="bi bi-exclamation-triangle me-1"></i>Bu işlem geri alınamaz.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn-glass" data-bs-dismiss="modal">Vazgeç</button>
                        <button type="button" class="btn-teal btn-danger-gradient" id="deleteCommentBtn"><i class="bi bi-trash me-1"></i>Sil</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="{{ asset('assets/admin/js/comments.js') }}?v={{ filemtime(public_path('assets/admin/js/comments.js')) }}"></script>
@endpush
