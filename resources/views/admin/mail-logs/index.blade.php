@extends('layouts.admin')

@section('title', 'Mail Logları — Boyalı Kelimeler Admin')

@section('content')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3" data-aos="fade-down" data-aos-duration="400">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link"><i class="bi bi-house"></i> Ana Sayfa</a></li>
            <li class="breadcrumb-item active text-teal">Mail Logları</li>
        </ol>
    </nav>

    <x-admin.page-header title="Mail Logları" subtitle="Sistemden gönderilen tüm e-postaların kayıtları" />

    <!-- Stat Cards -->
    <div class="row g-4 mb-4">
        <x-admin.stat-card color="blue" icon="bi-envelope-fill" label="Toplam Mail" :count="$stats['total']" :delay="0" />
        <x-admin.stat-card color="green" icon="bi-check-circle-fill" label="Gönderilen" :count="$stats['sent']" :delay="100" />
        <x-admin.stat-card color="red" icon="bi-x-circle-fill" label="Başarısız" :count="$stats['failed']" :delay="200" />
        <x-admin.stat-card color="orange" icon="bi-hourglass-split" label="Bekliyor" :count="$stats['pending']" :delay="300" />
    </div>

    <!-- Status Tabs -->
    <div class="cl-status-tabs mb-4" data-aos="fade-up" data-aos-delay="50">
        <a href="{{ route('admin.mail-logs.index', array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}"
           class="cl-status-tab {{ empty($filters['status']) || $filters['status'] === 'all' ? 'active' : '' }}">
            Tümü <span class="cl-tab-count">{{ $stats['total'] }}</span>
        </a>
        <a href="{{ route('admin.mail-logs.index', array_merge(request()->except('status', 'page'), ['status' => 'sent'])) }}"
           class="cl-status-tab {{ ($filters['status'] ?? '') === 'sent' ? 'active' : '' }}">
            Gönderilen <span class="cl-tab-count">{{ $stats['sent'] }}</span>
        </a>
        <a href="{{ route('admin.mail-logs.index', array_merge(request()->except('status', 'page'), ['status' => 'failed'])) }}"
           class="cl-status-tab {{ ($filters['status'] ?? '') === 'failed' ? 'active' : '' }}">
            Başarısız <span class="cl-tab-count">{{ $stats['failed'] }}</span>
        </a>
        <a href="{{ route('admin.mail-logs.index', array_merge(request()->except('status', 'page'), ['status' => 'pending'])) }}"
           class="cl-status-tab {{ ($filters['status'] ?? '') === 'pending' ? 'active' : '' }}">
            Bekliyor <span class="cl-tab-count">{{ $stats['pending'] }}</span>
        </a>
    </div>

    <!-- Filter Toolbar -->
    <div class="cl-toolbar mb-4" data-aos="fade-up" data-aos-delay="50">
        <form method="GET" action="{{ route('admin.mail-logs.index') }}" class="d-flex flex-wrap gap-3 align-items-end w-100">
            @if(!empty($filters['status']))
                <input type="hidden" name="status" value="{{ $filters['status'] }}">
            @endif

            <div class="cl-search flex-grow-1">
                <i class="bi bi-search"></i>
                <input type="text" name="search" class="cl-search-input" placeholder="E-posta, isim veya konu ara..." value="{{ $filters['search'] ?? '' }}">
            </div>

            <div class="cl-filters">
                <div class="cl-filter-select">
                    <input type="date" name="date_from" class="cl-filter-select" value="{{ $filters['date_from'] ?? '' }}" placeholder="Başlangıç">
                </div>
                <div class="cl-filter-select">
                    <input type="date" name="date_to" class="cl-filter-select" value="{{ $filters['date_to'] ?? '' }}" placeholder="Bitiş">
                </div>
                <select name="per_page" class="cl-filter-select" onchange="this.form.submit()">
                    <option value="25" {{ $perPage === 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $perPage === 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $perPage === 100 ? 'selected' : '' }}>100</option>
                </select>
            </div>

            <button type="submit" class="btn-teal"><i class="bi bi-funnel"></i> Filtrele</button>
        </form>
    </div>

    <!-- Mail Logs Table -->
    <div class="card-dark" data-aos="fade-up" data-aos-delay="50">
        <div class="table-responsive">
            <table class="cl-table">
                <thead>
                    <tr>
                        <th>Durum</th>
                        <th>Alıcı</th>
                        <th>Konu</th>
                        <th>Tür</th>
                        <th>Tarih</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>
                                @if($log->isSent())
                                    <span class="badge bg-success bg-opacity-25 text-success"><i class="bi bi-check-circle-fill me-1"></i>Gönderildi</span>
                                @elseif($log->isFailed())
                                    <span class="badge bg-danger bg-opacity-25 text-danger"><i class="bi bi-x-circle-fill me-1"></i>Başarısız</span>
                                @else
                                    <span class="badge bg-warning bg-opacity-25 text-warning"><i class="bi bi-hourglass-split me-1"></i>Bekliyor</span>
                                @endif
                            </td>
                            <td>
                                <div>
                                    <span class="d-block fw-medium">{{ $log->to_email }}</span>
                                    @if($log->to_name)
                                        <small class="text-muted">{{ $log->to_name }}</small>
                                    @endif
                                    @if($log->user)
                                        <small class="text-teal d-block"><i class="bi bi-person-fill me-1"></i>{{ $log->user->name }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="d-block text-truncate" title="{{ $log->subject }}">{{ Str::limit($log->subject, 50) }}</span>
                                @if($log->error_message)
                                    <small class="text-danger d-block"><i class="bi bi-exclamation-triangle me-1"></i>{{ Str::limit($log->error_message, 60) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($log->mailable_class)
                                    <span class="badge bg-info bg-opacity-25 text-info">{{ class_basename($log->mailable_class) }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-nowrap">{{ $log->created_at->format('d.m.Y') }}</span>
                                <small class="text-muted d-block">{{ $log->created_at->format('H:i:s') }}</small>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.mail-logs.show', $log) }}" class="usr-action-btn" title="Detay">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($log->body && ($log->isFailed() || $log->isSent()))
                                        <form id="resendMailForm-{{ $log->id }}" action="{{ route('admin.mail-logs.resend', $log) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="button" class="usr-action-btn text-teal" title="Yeniden Gönder" onclick="openConfirmModal({
                                                title: 'Maili Yeniden Gönder',
                                                message: 'Bu mail {{ $log->to_email }} adresine yeniden gönderilecek. Devam etmek istiyor musunuz?',
                                                iconClass: 'bi-envelope-arrow-up-fill',
                                                type: 'info',
                                                btnHtml: '<i class=\'bi bi-arrow-repeat\'></i> Evet, Gönder',
                                                onConfirm: function() { document.getElementById('resendMailForm-{{ $log->id }}').submit(); }
                                            })">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <button type="button" class="usr-action-btn text-danger" title="Sil" onclick="openDeleteModal({{ $log->id }}, '{{ e($log->subject) }}')">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <x-admin.table-empty :colspan="6" icon="bi-envelope-x" message="Henüz mail kaydı bulunmuyor." />
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($logs->hasPages())
        <div class="cl-pagination-wrapper mt-4" data-aos="fade-up">
            <div class="cl-pagination-info">
                <span>Toplam <strong>{{ number_format($logs->total()) }}</strong> kayıttan <strong>{{ $logs->firstItem() }}-{{ $logs->lastItem() }}</strong> arası gösteriliyor</span>
            </div>
            <nav class="cl-pagination">
                @if($logs->onFirstPage())
                    <button class="cl-page-btn" disabled><i class="bi bi-chevron-left"></i></button>
                @else
                    <a href="{{ $logs->previousPageUrl() }}" class="cl-page-btn"><i class="bi bi-chevron-left"></i></a>
                @endif

                @foreach($logs->getUrlRange(max(1, $logs->currentPage() - 2), min($logs->lastPage(), $logs->currentPage() + 2)) as $page => $url)
                    <a href="{{ $url }}" class="cl-page-btn {{ $page === $logs->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                @endforeach

                @if($logs->hasMorePages())
                    <a href="{{ $logs->nextPageUrl() }}" class="cl-page-btn"><i class="bi bi-chevron-right"></i></a>
                @else
                    <button class="cl-page-btn" disabled><i class="bi bi-chevron-right"></i></button>
                @endif
            </nav>
        </div>
    @endif

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-custom">
                <div class="modal-body text-center p-4">
                    <div class="delete-modal-icon mb-3">
                        <i class="bi bi-trash3"></i>
                    </div>
                    <h5 class="mb-2">Mail Kaydını Sil</h5>
                    <p class="text-muted mb-4">
                        <strong id="deleteItemName"></strong> konulu mail kaydı silinecek. Bu işlem geri alınamaz.
                    </p>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="d-flex gap-2 justify-content-center">
                            <button type="button" class="btn-glass" data-bs-dismiss="modal">İptal</button>
                            <button type="submit" class="btn btn-danger">Sil</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="{{ asset('assets/admin/js/mail-logs.js') }}?v={{ filemtime(public_path('assets/admin/js/mail-logs.js')) }}"></script>
@endpush
