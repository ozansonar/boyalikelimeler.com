@extends('layouts.admin')

@section('title', 'Sayfa Yönetimi — Admin')

@section('content')

    <!-- Page Header -->
    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3" data-aos="fade-down">
        <div>
            <h1 class="page-title">Sayfa Yönetimi</h1>
            <p class="page-subtitle">Statik sayfaları yönetin — Hakkımızda, Gizlilik Politikası, SSS vb.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.pages.create') }}" class="btn-teal">
                <i class="bi bi-plus-lg"></i> Yeni Sayfa
            </a>
        </div>
    </div>


    <!-- ==================== SECTION 1: STATS ==================== -->
    <div class="row g-4 mb-4">
        <div class="col-xxl-4 col-sm-6" data-aos="fade-up" data-aos-delay="0">
            <div class="usr-stat-card">
                <div class="usr-stat-icon usr-stat-icon-blue">
                    <i class="bi bi-file-earmark-richtext-fill"></i>
                </div>
                <div class="usr-stat-info">
                    <span class="usr-stat-label">Toplam Sayfa</span>
                    <h3 class="usr-stat-value" data-count="{{ $stats['total'] }}">0</h3>
                </div>
            </div>
        </div>
        <div class="col-xxl-4 col-sm-6" data-aos="fade-up" data-aos-delay="100">
            <div class="usr-stat-card">
                <div class="usr-stat-icon usr-stat-icon-green">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="usr-stat-info">
                    <span class="usr-stat-label">Aktif</span>
                    <h3 class="usr-stat-value" data-count="{{ $stats['active'] }}">0</h3>
                </div>
            </div>
        </div>
        <div class="col-xxl-4 col-sm-6" data-aos="fade-up" data-aos-delay="200">
            <div class="usr-stat-card">
                <div class="usr-stat-icon usr-stat-icon-orange">
                    <i class="bi bi-eye-slash-fill"></i>
                </div>
                <div class="usr-stat-info">
                    <span class="usr-stat-label">Pasif</span>
                    <h3 class="usr-stat-value" data-count="{{ $stats['inactive'] }}">0</h3>
                </div>
            </div>
        </div>
    </div>


    <!-- ==================== SECTION 2: FILTERS & TOOLBAR ==================== -->
    <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="150">
        <div class="card-body-custom">
            <form method="GET" action="{{ route('admin.pages.index') }}" class="cl-toolbar">
                <div class="cl-search">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Sayfa başlığı ile ara...">
                </div>

                <div class="cl-filters">
                    <select class="cl-filter-select" name="is_active" onchange="this.form.submit()">
                        <option value="">Tüm Durumlar</option>
                        <option value="1" {{ ($filters['is_active'] ?? '') === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ ($filters['is_active'] ?? '') === '0' ? 'selected' : '' }}>Pasif</option>
                    </select>
                </div>

                <div class="cl-toolbar-actions">
                    <button type="submit" class="btn-glass"><i class="bi bi-funnel me-1"></i>Filtrele</button>
                    @if(!empty($filters['search']) || isset($filters['is_active']) && $filters['is_active'] !== '')
                        <a href="{{ route('admin.pages.index') }}" class="cl-filter-reset" title="Filtreleri Sıfırla">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    @endif
                    <div class="cl-per-page">
                        <label>Göster:</label>
                        <select name="per_page" onchange="this.form.submit()">
                            @foreach([10, 25, 50, 100] as $pp)
                                <option value="{{ $pp }}" {{ $perPage === $pp ? 'selected' : '' }}>{{ $pp }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <!-- ==================== SECTION 3: TABLE ==================== -->
    <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="200">
        <div class="card-body-custom p-0">
            <div class="table-responsive">
                <table class="table table-hover cl-table mb-0">
                    <thead>
                        <tr>
                            <th>Sayfa</th>
                            <th class="d-none d-md-table-cell">URL</th>
                            <th>Durum</th>
                            <th class="d-none d-lg-table-cell">Oluşturan</th>
                            <th class="d-none d-xl-table-cell">Sıra</th>
                            <th class="d-none d-xxl-table-cell">Tarih</th>
                            <th class="cl-th-actions">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pages as $page)
                            <tr>
                                <td>
                                    <div class="cl-content-cell">
                                        @if($page->cover_image)
                                            <div class="cl-content-thumb">
                                                <img src="/uploads/{{ $page->cover_image }}" alt="">
                                            </div>
                                        @else
                                            <div class="cl-content-thumb draft"><i class="bi bi-file-earmark-richtext"></i></div>
                                        @endif
                                        <div class="cl-content-info">
                                            <span class="cl-content-title">{{ $page->title }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <span class="usr-meta">/{{ $page->slug }}</span>
                                </td>
                                <td>
                                    @if($page->is_active)
                                        <span class="usr-status-badge badge-active">Aktif</span>
                                    @else
                                        <span class="usr-status-badge badge-passive">Pasif</span>
                                    @endif
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <div class="cl-author-cell">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($page->author?->name ?? 'U') }}&background=14b8a6&color=fff&size=28" alt="">
                                        <span>{{ $page->author?->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="d-none d-xl-table-cell">
                                    <span class="usr-meta">{{ $page->sort_order }}</span>
                                </td>
                                <td class="d-none d-xxl-table-cell">
                                    <span class="usr-meta">{{ $page->created_at->format('d M Y') }}</span>
                                </td>
                                <td>
                                    <div class="usr-actions">
                                        @if($page->is_active)
                                            <a class="usr-action-btn" title="Görüntüle" href="{{ route('page.show', $page->slug) }}" target="_blank"><i class="bi bi-eye"></i></a>
                                        @endif
                                        <a class="usr-action-btn" title="Düzenle" href="{{ route('admin.pages.edit', $page) }}"><i class="bi bi-pencil"></i></a>
                                        <button class="usr-action-btn danger" title="Sil" onclick="openDeleteModal({{ $page->id }}, '{{ addslashes($page->title) }}')"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-clr-muted">
                                    <i class="bi bi-file-earmark-richtext fs-1 d-block mb-2 opacity-50"></i>
                                    Henüz sayfa oluşturulmamış.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($pages->hasPages())
                <div class="cl-pagination-wrapper">
                    <div class="cl-pagination-info">
                        <span>Toplam <strong>{{ number_format($pages->total()) }}</strong> sayfadan <strong>{{ $pages->firstItem() }}-{{ $pages->lastItem() }}</strong> arası gösteriliyor</span>
                    </div>
                    <nav class="cl-pagination">
                        @if($pages->onFirstPage())
                            <button class="cl-page-btn" disabled><i class="bi bi-chevron-left"></i></button>
                        @else
                            <a href="{{ $pages->previousPageUrl() }}" class="cl-page-btn"><i class="bi bi-chevron-left"></i></a>
                        @endif

                        @foreach($pages->getUrlRange(max(1, $pages->currentPage() - 2), min($pages->lastPage(), $pages->currentPage() + 2)) as $p => $url)
                            <a href="{{ $url }}" class="cl-page-btn {{ $p === $pages->currentPage() ? 'active' : '' }}">{{ $p }}</a>
                        @endforeach

                        @if($pages->hasMorePages())
                            <a href="{{ $pages->nextPageUrl() }}" class="cl-page-btn"><i class="bi bi-chevron-right"></i></a>
                        @else
                            <button class="cl-page-btn" disabled><i class="bi bi-chevron-right"></i></button>
                        @endif
                    </nav>
                </div>
            @endif
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="status-modal-icon danger">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <h5 class="cl-modal-heading">Silme Onayı</h5>
                    <p class="cl-modal-body-text">Bu sayfayı silmek istediğinizden emin misiniz?</p>
                    <p class="cl-modal-content-name" id="deleteContentTitle"></p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn-glass" data-bs-dismiss="modal">Vazgeç</button>
                        <form id="deleteForm" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-teal btn-danger-gradient">
                                <i class="bi bi-trash me-1"></i>Evet, Sil
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="{{ asset('assets/admin/js/pages.js') }}"></script>
@endpush
