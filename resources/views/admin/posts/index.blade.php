@extends('layouts.admin')

@section('title', 'İçerik Yönetimi — Admin')

@section('content')

    <!-- Page Header -->
    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3" data-aos="fade-down">
        <div>
            <h1 class="page-title">İçerik Yönetimi</h1>
            <p class="page-subtitle">Tüm içerikleri listeleyin, filtreleyin, düzenleyin ve yönetin</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.posts.create') }}" class="btn-teal">
                <i class="bi bi-plus-lg"></i> Yeni İçerik
            </a>
        </div>
    </div>


    <!-- ==================== SECTION 1: STATS ==================== -->
    <div class="row g-4 mb-4">
        <div class="col-xxl-3 col-xl-6 col-sm-6" data-aos="fade-up" data-aos-delay="0">
            <div class="usr-stat-card">
                <div class="usr-stat-icon usr-stat-icon-blue">
                    <i class="bi bi-file-earmark-text-fill"></i>
                </div>
                <div class="usr-stat-info">
                    <span class="usr-stat-label">Toplam İçerik</span>
                    <h3 class="usr-stat-value" data-count="{{ $stats['total'] }}">0</h3>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-xl-6 col-sm-6" data-aos="fade-up" data-aos-delay="100">
            <div class="usr-stat-card">
                <div class="usr-stat-icon usr-stat-icon-green">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="usr-stat-info">
                    <span class="usr-stat-label">Yayında</span>
                    <h3 class="usr-stat-value" data-count="{{ $stats['published'] }}">0</h3>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-xl-6 col-sm-6" data-aos="fade-up" data-aos-delay="200">
            <div class="usr-stat-card">
                <div class="usr-stat-icon usr-stat-icon-orange">
                    <i class="bi bi-file-earmark-fill"></i>
                </div>
                <div class="usr-stat-info">
                    <span class="usr-stat-label">Taslak</span>
                    <h3 class="usr-stat-value" data-count="{{ $stats['draft'] }}">0</h3>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-xl-6 col-sm-6" data-aos="fade-up" data-aos-delay="300">
            <div class="usr-stat-card">
                <div class="usr-stat-icon usr-stat-icon-purple">
                    <i class="bi bi-eye-fill"></i>
                </div>
                <div class="usr-stat-info">
                    <span class="usr-stat-label">Toplam Görüntülenme</span>
                    <h3 class="usr-stat-value" data-count="{{ $stats['views'] }}">0</h3>
                </div>
            </div>
        </div>
    </div>


    <!-- ==================== SECTION 2: STATUS TABS ==================== -->
    <div class="cl-status-tabs mb-4" data-aos="fade-up" data-aos-delay="100">
        <a href="{{ route('admin.posts.index', array_merge(request()->except(['status', 'page']), [])) }}" class="cl-status-tab {{ empty($filters['status']) ? 'active' : '' }}">
            <span>Tümü</span>
            <span class="cl-tab-count">{{ $statusCounts['all'] ?? 0 }}</span>
        </a>
        <a href="{{ route('admin.posts.index', array_merge(request()->except('page'), ['status' => 'published'])) }}" class="cl-status-tab {{ ($filters['status'] ?? '') === 'published' ? 'active' : '' }}">
            <i class="bi bi-check-circle text-neon-green"></i>
            <span>Yayında</span>
            <span class="cl-tab-count">{{ $statusCounts['published'] ?? 0 }}</span>
        </a>
        <a href="{{ route('admin.posts.index', array_merge(request()->except('page'), ['status' => 'draft'])) }}" class="cl-status-tab {{ ($filters['status'] ?? '') === 'draft' ? 'active' : '' }}">
            <i class="bi bi-file-earmark text-neon-orange"></i>
            <span>Taslak</span>
            <span class="cl-tab-count">{{ $statusCounts['draft'] ?? 0 }}</span>
        </a>
        <a href="{{ route('admin.posts.index', array_merge(request()->except('page'), ['status' => 'scheduled'])) }}" class="cl-status-tab {{ ($filters['status'] ?? '') === 'scheduled' ? 'active' : '' }}">
            <i class="bi bi-clock text-neon-blue"></i>
            <span>Zamanlanmış</span>
            <span class="cl-tab-count">{{ $statusCounts['scheduled'] ?? 0 }}</span>
        </a>
        <a href="{{ route('admin.posts.index', array_merge(request()->except('page'), ['status' => 'archived'])) }}" class="cl-status-tab {{ ($filters['status'] ?? '') === 'archived' ? 'active' : '' }}">
            <i class="bi bi-archive text-neon-purple"></i>
            <span>Arşiv</span>
            <span class="cl-tab-count">{{ $statusCounts['archived'] ?? 0 }}</span>
        </a>
    </div>


    <!-- ==================== SECTION 3: FILTERS & TOOLBAR ==================== -->
    <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="150">
        <div class="card-body-custom">
            <form method="GET" action="{{ route('admin.posts.index') }}" class="cl-toolbar">
                <div class="cl-search">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Başlık veya özet ile ara...">
                </div>

                <div class="cl-filters">
                    <select class="cl-filter-select" name="category_id" onchange="this.form.submit()">
                        <option value="">Tüm Kategoriler</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ ($filters['category_id'] ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>

                    <select class="cl-filter-select" name="user_id" onchange="this.form.submit()">
                        <option value="">Tüm Yazarlar</option>
                        @foreach($authors as $author)
                            <option value="{{ $author->id }}" {{ ($filters['user_id'] ?? '') == $author->id ? 'selected' : '' }}>{{ $author->name }}</option>
                        @endforeach
                    </select>

                    @if(!empty($filters['status']))
                        <input type="hidden" name="status" value="{{ $filters['status'] }}">
                    @endif
                </div>

                <div class="cl-toolbar-actions">
                    <button type="submit" class="btn-glass"><i class="bi bi-funnel me-1"></i>Filtrele</button>
                    @if(!empty($filters['search']) || !empty($filters['category_id']) || !empty($filters['user_id']))
                        <a href="{{ route('admin.posts.index', !empty($filters['status']) ? ['status' => $filters['status']] : []) }}" class="cl-filter-reset" title="Filtreleri Sıfırla">
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


    <!-- ==================== SECTION 4: TABLE ==================== -->
    <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="200">
        <div class="card-body-custom p-0">
            <div class="table-responsive">
                <table class="table table-hover cl-table mb-0">
                    <thead>
                        <tr>
                            <th>İçerik</th>
                            <th class="d-none d-md-table-cell">Kategori</th>
                            <th class="d-none d-lg-table-cell">Yazar</th>
                            <th>Durum</th>
                            <th class="d-none d-xl-table-cell">Görüntülenme</th>
                            <th class="d-none d-xxl-table-cell">Tarih</th>
                            <th class="cl-th-actions">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($posts as $post)
                            <tr>
                                <td>
                                    <div class="cl-content-cell">
                                        @if($post->cover_image)
                                            <div class="cl-content-thumb">
                                                <img src="/uploads/{{ $post->cover_image }}" alt="">
                                            </div>
                                        @else
                                            <div class="cl-content-thumb draft"><i class="bi bi-file-earmark-text"></i></div>
                                        @endif
                                        <div class="cl-content-info">
                                            <span class="cl-content-title">
                                                {{ $post->title }}
                                                @if($post->is_featured)
                                                    <span class="cl-featured-badge"><i class="bi bi-star-fill"></i></span>
                                                @endif
                                            </span>
                                            <span class="cl-content-meta">
                                                <i class="bi bi-clock me-1"></i>~{{ $post->readingTime() }} dk okuma
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <span class="cl-category-badge tech">{{ $post->category?->name ?? '-' }}</span>
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <div class="cl-author-cell">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($post->author?->name ?? 'U') }}&background=14b8a6&color=fff&size=28" alt="">
                                        <span>{{ $post->author?->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="usr-status-badge {{ $post->status->badgeClass() }}">{{ $post->status->label() }}</span>
                                </td>
                                <td class="d-none d-xl-table-cell">
                                    <div class="cl-views">
                                        <i class="bi bi-eye me-1"></i>
                                        {{ number_format($post->view_count) }}
                                    </div>
                                </td>
                                <td class="d-none d-xxl-table-cell">
                                    <span class="usr-meta">{{ $post->created_at->format('d M Y') }}</span>
                                </td>
                                <td>
                                    <div class="usr-actions">
                                        @if($post->isPublished())
                                            <a class="usr-action-btn" title="Görüntüle" href="{{ route('blog.show', $post->slug) }}" target="_blank"><i class="bi bi-eye"></i></a>
                                        @endif
                                        <a class="usr-action-btn" title="Düzenle" href="{{ route('admin.posts.edit', $post) }}"><i class="bi bi-pencil"></i></a>
                                        <button class="usr-action-btn danger" title="Sil" onclick="openDeleteModal({{ $post->id }}, '{{ addslashes($post->title) }}')"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-clr-muted">
                                    <i class="bi bi-file-earmark-text fs-1 d-block mb-2 opacity-50"></i>
                                    Henüz içerik oluşturulmamış.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($posts->hasPages())
                <div class="cl-pagination-wrapper">
                    <div class="cl-pagination-info">
                        <span>Toplam <strong>{{ number_format($posts->total()) }}</strong> içerikten <strong>{{ $posts->firstItem() }}-{{ $posts->lastItem() }}</strong> arası gösteriliyor</span>
                    </div>
                    <nav class="cl-pagination">
                        @if($posts->onFirstPage())
                            <button class="cl-page-btn" disabled><i class="bi bi-chevron-left"></i></button>
                        @else
                            <a href="{{ $posts->previousPageUrl() }}" class="cl-page-btn"><i class="bi bi-chevron-left"></i></a>
                        @endif

                        @foreach($posts->getUrlRange(max(1, $posts->currentPage() - 2), min($posts->lastPage(), $posts->currentPage() + 2)) as $page => $url)
                            <a href="{{ $url }}" class="cl-page-btn {{ $page === $posts->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                        @endforeach

                        @if($posts->hasMorePages())
                            <a href="{{ $posts->nextPageUrl() }}" class="cl-page-btn"><i class="bi bi-chevron-right"></i></a>
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
                    <p class="cl-modal-body-text">Bu içeriği silmek istediğinizden emin misiniz?</p>
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
<script src="{{ asset('assets/admin/js/content-list.js') }}"></script>
@endpush
