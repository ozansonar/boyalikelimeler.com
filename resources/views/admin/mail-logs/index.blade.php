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

    <!-- Page Header -->
    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3" data-aos="fade-down">
        <div>
            <h1 class="page-title">Mail Logları</h1>
            <p class="page-subtitle">Sistemden gönderilen tüm e-postaların kayıtları</p>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-sm-6" data-aos="fade-up" data-aos-delay="0">
            <div class="usr-stat-card">
                <div class="usr-stat-icon usr-stat-icon-blue">
                    <i class="bi bi-envelope-fill"></i>
                </div>
                <div class="usr-stat-info">
                    <span class="usr-stat-label">Toplam Mail</span>
                    <h3 class="usr-stat-value" data-count="{{ $stats['total'] }}">0</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6" data-aos="fade-up" data-aos-delay="100">
            <div class="usr-stat-card">
                <div class="usr-stat-icon usr-stat-icon-green">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="usr-stat-info">
                    <span class="usr-stat-label">Gönderilen</span>
                    <h3 class="usr-stat-value" data-count="{{ $stats['sent'] }}">0</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6" data-aos="fade-up" data-aos-delay="200">
            <div class="usr-stat-card">
                <div class="usr-stat-icon usr-stat-icon-red">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
                <div class="usr-stat-info">
                    <span class="usr-stat-label">Başarısız</span>
                    <h3 class="usr-stat-value" data-count="{{ $stats['failed'] }}">0</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6" data-aos="fade-up" data-aos-delay="300">
            <div class="usr-stat-card">
                <div class="usr-stat-icon usr-stat-icon-orange">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div class="usr-stat-info">
                    <span class="usr-stat-label">Bekliyor</span>
                    <h3 class="usr-stat-value" data-count="{{ $stats['pending'] }}">0</h3>
                </div>
            </div>
        </div>
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
                                @if($log->status === 'sent')
                                    <span class="badge bg-success bg-opacity-25 text-success"><i class="bi bi-check-circle-fill me-1"></i>Gönderildi</span>
                                @elseif($log->status === 'failed')
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
                                    <button type="button" class="usr-action-btn text-danger" title="Sil" onclick="openDeleteModal({{ $log->id }}, '{{ e($log->subject) }}')">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-envelope-x fs-1 d-block mb-2"></i>
                                Henüz mail kaydı bulunmuyor.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($logs->hasPages())
        <div class="cl-pagination-wrapper mt-4" data-aos="fade-up">
            <div class="text-muted">
                Toplam {{ $logs->total() }} kayıttan {{ $logs->firstItem() }}-{{ $logs->lastItem() }} arası gösteriliyor
            </div>
            {{ $logs->links() }}
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
<script src="{{ asset('assets/admin/js/mail-logs.js') }}"></script>
@endpush
