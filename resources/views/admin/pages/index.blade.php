@extends('layouts.admin')

@section('title', 'Sayfa Yönetimi — Admin')

@section('content')

    <x-admin.page-header title="Sayfa Yönetimi" subtitle="Statik sayfaları yönetin — Hakkımızda, Gizlilik Politikası, SSS vb.">
        <a href="{{ route('admin.pages.create') }}" class="btn-teal">
            <i class="bi bi-plus-lg"></i> Yeni Sayfa
        </a>
    </x-admin.page-header>


    <!-- ==================== SECTION 1: STATS ==================== -->
    <div class="row g-4 mb-4">
        <x-admin.stat-card color="blue" icon="bi-file-earmark-richtext-fill" label="Toplam Sayfa" :count="$stats['total']" :delay="0" col-class="col-xxl-4 col-sm-6" />
        <x-admin.stat-card color="green" icon="bi-check-circle-fill" label="Aktif" :count="$stats['active']" :delay="100" col-class="col-xxl-4 col-sm-6" />
        <x-admin.stat-card color="orange" icon="bi-eye-slash-fill" label="Pasif" :count="$stats['inactive']" :delay="200" col-class="col-xxl-4 col-sm-6" />
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
                                        <button class="usr-action-btn danger" title="Sil" onclick="openDeleteModal({{ $page->id }}, '{{ addslashes($page->title) }}', '{{ route('admin.pages.destroy', $page) }}')"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-admin.table-empty :colspan="7" icon="bi-file-earmark-richtext" message="Henüz sayfa oluşturulmamış." />
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

    <x-admin.delete-modal message="Bu sayfayı silmek istediğinizden emin misiniz?" />

@endsection

@push('scripts')
<script src="{{ asset('assets/admin/js/pages.js') }}?v={{ filemtime(public_path('assets/admin/js/pages.js')) }}"></script>
@endpush
