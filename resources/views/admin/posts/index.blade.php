@extends('layouts.admin')

@section('title', 'İçerik Yönetimi — Admin')

@section('content')

    <x-admin.page-header title="İçerik Yönetimi" subtitle="Tüm içerikleri listeleyin, filtreleyin, düzenleyin ve yönetin">
        <a href="{{ route('admin.posts.create') }}" class="btn-teal">
            <i class="bi bi-plus-lg"></i> Yeni İçerik
        </a>
    </x-admin.page-header>


    <!-- ==================== SECTION 1: STATS ==================== -->
    <div class="row g-4 mb-4">
        <x-admin.stat-card color="blue" icon="bi-file-earmark-text-fill" label="Toplam İçerik" :count="$stats['total']" :delay="0" col-class="col-xxl-3 col-xl-6 col-sm-6" />
        <x-admin.stat-card color="green" icon="bi-check-circle-fill" label="Yayında" :count="$stats['published']" :delay="100" col-class="col-xxl-3 col-xl-6 col-sm-6" />
        <x-admin.stat-card color="orange" icon="bi-file-earmark-fill" label="Taslak" :count="$stats['draft']" :delay="200" col-class="col-xxl-3 col-xl-6 col-sm-6" />
        <x-admin.stat-card color="purple" icon="bi-eye-fill" label="Toplam Görüntülenme" :count="$stats['views']" :delay="300" col-class="col-xxl-3 col-xl-6 col-sm-6" />
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
                                            <a class="usr-action-btn" title="Görüntüle" href="{{ $post->url() }}" target="_blank"><i class="bi bi-eye"></i></a>
                                        @endif
                                        <a class="usr-action-btn" title="Düzenle" href="{{ route('admin.posts.edit', $post) }}"><i class="bi bi-pencil"></i></a>
                                        <button class="usr-action-btn danger" title="Sil" onclick="openDeleteModal({{ $post->id }}, '{{ addslashes($post->title) }}', '{{ route('admin.posts.destroy', $post) }}')"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-admin.table-empty :colspan="7" icon="bi-file-earmark-text" message="Henüz içerik oluşturulmamış." />
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

    <x-admin.delete-modal message="Bu içeriği silmek istediğinizden emin misiniz?" />

@endsection

@push('scripts')
<script src="{{ asset('assets/admin/js/content-list.js') }}?v={{ filemtime(public_path('assets/admin/js/content-list.js')) }}"></script>
@endpush
