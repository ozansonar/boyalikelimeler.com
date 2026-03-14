@extends('layouts.admin')
@section('title', 'Eser İstatistikleri — Admin')
@section('content')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3" data-aos="fade-down" data-aos-duration="400">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link"><i class="bi bi-house me-1"></i>Ana Sayfa</a>
            </li>
            <li class="breadcrumb-item active text-teal">Eser İstatistikleri</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <x-admin.page-header title="Eser İstatistikleri" subtitle="Eserlerin okunma ve etkileşim performans analizleri">
    </x-admin.page-header>

    <!-- Work Type Toggle -->
    <div class="cl-status-tabs mb-4" data-aos="fade-up">
        <a href="{{ route('admin.work-statistics.index', request()->except(['work_type', 'page'])) }}" class="cl-status-tab {{ empty($filters['work_type']) ? 'active' : '' }}">
            <i class="bi bi-grid"></i>
            <span>Tümü</span>
        </a>
        @foreach(\App\Enums\LiteraryWorkType::cases() as $type)
            <a href="{{ route('admin.work-statistics.index', array_merge(request()->except('page'), ['work_type' => $type->value])) }}" class="cl-status-tab {{ ($filters['work_type'] ?? '') === $type->value ? 'active' : '' }}">
                <i class="bi {{ $type->icon() }}"></i>
                <span>{{ $type->label() }}</span>
            </a>
        @endforeach
    </div>

    <!-- KPI Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xxl-2 col-xl-4 col-sm-6" data-aos="fade-up" data-aos-delay="0">
            <div class="anl-kpi-card h-100">
                <div class="anl-kpi-header">
                    <div class="anl-kpi-icon anl-kpi-icon-teal">
                        <i class="bi bi-journal-check"></i>
                    </div>
                </div>
                <h3 class="anl-kpi-value">{{ number_format($stats['total_works']) }}</h3>
                <span class="anl-kpi-label">Toplam Onaylı Eser</span>
            </div>
        </div>
        <div class="col-xxl-2 col-xl-4 col-sm-6" data-aos="fade-up" data-aos-delay="50">
            <div class="anl-kpi-card h-100">
                <div class="anl-kpi-header">
                    <div class="anl-kpi-icon anl-kpi-icon-blue">
                        <i class="bi bi-eye-fill"></i>
                    </div>
                </div>
                <h3 class="anl-kpi-value">{{ number_format($stats['total_views']) }}</h3>
                <span class="anl-kpi-label">Toplam Okunma</span>
            </div>
        </div>
        <div class="col-xxl-2 col-xl-4 col-sm-6" data-aos="fade-up" data-aos-delay="100">
            <div class="anl-kpi-card h-100">
                <div class="anl-kpi-header">
                    <div class="anl-kpi-icon anl-kpi-icon-green">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                </div>
                <h3 class="anl-kpi-value">{{ number_format($stats['today_views']) }}</h3>
                <span class="anl-kpi-label">Bugünkü Okunma</span>
            </div>
        </div>
        <div class="col-xxl-2 col-xl-4 col-sm-6" data-aos="fade-up" data-aos-delay="150">
            <div class="anl-kpi-card h-100">
                <div class="anl-kpi-header">
                    <div class="anl-kpi-icon anl-kpi-icon-purple">
                        <i class="bi bi-bar-chart-line-fill"></i>
                    </div>
                </div>
                <h3 class="anl-kpi-value">{{ number_format($stats['avg_views_per_work']) }}</h3>
                <span class="anl-kpi-label">Eser Başına Ort. Okunma</span>
            </div>
        </div>
        <div class="col-xxl-4 col-xl-8 col-sm-12" data-aos="fade-up" data-aos-delay="200">
            <div class="anl-kpi-card h-100">
                <div class="anl-kpi-header">
                    <div class="anl-kpi-icon anl-kpi-icon-orange">
                        <i class="bi bi-trophy-fill"></i>
                    </div>
                </div>
                <h3 class="anl-kpi-value ast-kpi-value-sm">{{ Str::limit($stats['most_viewed_title'], 40) }}</h3>
                <span class="anl-kpi-label">En Çok Okunan Eser ({{ number_format($stats['most_viewed_views']) }})</span>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="150">
        <div class="card-body-custom">
            <form method="GET" action="{{ route('admin.work-statistics.index') }}" class="usr-toolbar flex-wrap">
                @if(!empty($filters['work_type']))
                    <input type="hidden" name="work_type" value="{{ $filters['work_type'] }}">
                @endif

                <div class="usr-search">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Eser adı veya yazar adı ile ara...">
                </div>

                <div class="usr-filters flex-wrap">
                    <select class="usr-filter-select" name="category" onchange="this.form.submit()">
                        <option value="">Tüm Kategoriler</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ ($filters['category'] ?? '') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>

                    <select class="usr-filter-select" name="author" onchange="this.form.submit()">
                        <option value="">Tüm Yazarlar</option>
                        @foreach($authors as $author)
                            <option value="{{ $author->id }}" {{ ($filters['author'] ?? '') == $author->id ? 'selected' : '' }}>{{ $author->name }}</option>
                        @endforeach
                    </select>

                    <input type="date" class="usr-filter-select" name="date_from" value="{{ $filters['date_from'] ?? '' }}" placeholder="Başlangıç" title="Başlangıç tarihi">
                    <input type="date" class="usr-filter-select" name="date_to" value="{{ $filters['date_to'] ?? '' }}" placeholder="Bitiş" title="Bitiş tarihi">

                    <select class="usr-filter-select" name="per_page" onchange="this.form.submit()">
                        @foreach([10, 25, 50, 100] as $pp)
                            <option value="{{ $pp }}" {{ $perPage === $pp ? 'selected' : '' }}>{{ $pp }} / sayfa</option>
                        @endforeach
                    </select>
                </div>

                <div class="usr-toolbar-actions">
                    <button type="submit" class="btn-glass"><i class="bi bi-funnel me-1"></i>Filtrele</button>
                    @if(!empty($filters['search']) || !empty($filters['category']) || !empty($filters['author']) || !empty($filters['date_from']) || !empty($filters['date_to']))
                        <a href="{{ route('admin.work-statistics.index', array_filter(['work_type' => $filters['work_type'] ?? null])) }}" class="btn-glass"><i class="bi bi-x-lg me-1"></i>Temizle</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Works Table -->
    <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="200">
        <div class="card-body-custom p-0">
            <div class="table-responsive">
                <table class="usr-table">
                    <thead>
                        <tr>
                            <th>
                                <a href="{{ route('admin.work-statistics.index', array_merge($filters, ['sort' => 'title', 'dir' => ($filters['sort'] ?? '') === 'title' && ($filters['dir'] ?? '') === 'asc' ? 'desc' : 'asc', 'per_page' => $perPage])) }}" class="text-decoration-none text-clr-secondary">
                                    Eser
                                    @if(($filters['sort'] ?? '') === 'title')
                                        <i class="bi bi-arrow-{{ ($filters['dir'] ?? '') === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-center d-none d-md-table-cell">Kategori</th>
                            <th class="text-center d-none d-lg-table-cell">Yazar</th>
                            <th class="text-center">
                                <a href="{{ route('admin.work-statistics.index', array_merge($filters, ['sort' => 'views_last_7d', 'dir' => ($filters['sort'] ?? '') === 'views_last_7d' && ($filters['dir'] ?? '') === 'desc' ? 'asc' : 'desc', 'per_page' => $perPage])) }}" class="text-decoration-none text-clr-secondary">
                                    Son 7 Gün
                                    @if(($filters['sort'] ?? '') === 'views_last_7d')
                                        <i class="bi bi-arrow-{{ ($filters['dir'] ?? '') === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-center">
                                <a href="{{ route('admin.work-statistics.index', array_merge($filters, ['sort' => 'views_last_30d', 'dir' => ($filters['sort'] ?? '') === 'views_last_30d' && ($filters['dir'] ?? '') === 'desc' ? 'asc' : 'desc', 'per_page' => $perPage])) }}" class="text-decoration-none text-clr-secondary">
                                    Son 30 Gün
                                    @if(($filters['sort'] ?? '') === 'views_last_30d')
                                        <i class="bi bi-arrow-{{ ($filters['dir'] ?? '') === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-center">
                                <a href="{{ route('admin.work-statistics.index', array_merge($filters, ['sort' => 'view_count', 'dir' => ($filters['sort'] ?? '') === 'view_count' && ($filters['dir'] ?? '') === 'desc' ? 'asc' : 'desc', 'per_page' => $perPage])) }}" class="text-decoration-none text-clr-secondary">
                                    Toplam
                                    @if(($filters['sort'] ?? 'view_count') === 'view_count')
                                        <i class="bi bi-arrow-{{ ($filters['dir'] ?? 'desc') === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-center d-none d-lg-table-cell">
                                <a href="{{ route('admin.work-statistics.index', array_merge($filters, ['sort' => 'approved_comments_count', 'dir' => ($filters['sort'] ?? '') === 'approved_comments_count' && ($filters['dir'] ?? '') === 'desc' ? 'asc' : 'desc', 'per_page' => $perPage])) }}" class="text-decoration-none text-clr-secondary">
                                    Yorum
                                    @if(($filters['sort'] ?? '') === 'approved_comments_count')
                                        <i class="bi bi-arrow-{{ ($filters['dir'] ?? '') === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-center d-none d-lg-table-cell">
                                <a href="{{ route('admin.work-statistics.index', array_merge($filters, ['sort' => 'favorites_count', 'dir' => ($filters['sort'] ?? '') === 'favorites_count' && ($filters['dir'] ?? '') === 'desc' ? 'asc' : 'desc', 'per_page' => $perPage])) }}" class="text-decoration-none text-clr-secondary">
                                    Favori
                                    @if(($filters['sort'] ?? '') === 'favorites_count')
                                        <i class="bi bi-arrow-{{ ($filters['dir'] ?? '') === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-center d-none d-md-table-cell">
                                <a href="{{ route('admin.work-statistics.index', array_merge($filters, ['sort' => 'published_at', 'dir' => ($filters['sort'] ?? '') === 'published_at' && ($filters['dir'] ?? '') === 'desc' ? 'asc' : 'desc', 'per_page' => $perPage])) }}" class="text-decoration-none text-clr-secondary">
                                    Yayın Tarihi
                                    @if(($filters['sort'] ?? '') === 'published_at')
                                        <i class="bi bi-arrow-{{ ($filters['dir'] ?? '') === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($works as $index => $work)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @php $rank = ($works->currentPage() - 1) * $works->perPage() + $index + 1; @endphp
                                        @if($rank <= 3 && empty($filters['sort']) || ($filters['sort'] ?? 'view_count') === 'view_count' && ($filters['dir'] ?? 'desc') === 'desc' && $works->currentPage() === 1)
                                            <span class="badge {{ $rank === 1 ? 'bg-warning' : ($rank === 2 ? 'bg-secondary' : 'bg-danger-subtle text-danger') }} flex-shrink-0">
                                                {{ $rank }}
                                            </span>
                                        @endif
                                        <div>
                                            <div class="fw-semibold text-clr-primary">{{ Str::limit($work->title, 50) }}</div>
                                            <small class="text-clr-muted">
                                                <i class="bi {{ $work->work_type?->icon() ?? 'bi-journal-text' }} me-1"></i>{{ $work->work_type?->label() ?? 'Yazılı Eser' }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center d-none d-md-table-cell">
                                    <span class="badge bg-teal-subtle text-teal">{{ $work->category?->name ?? '-' }}</span>
                                </td>
                                <td class="text-center d-none d-lg-table-cell">
                                    <div class="fw-semibold text-clr-primary">{{ $work->author?->name ?? '-' }}</div>
                                    <small class="text-clr-muted">{{ $work->author ? '@' . $work->author->username : '' }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="fw-semibold text-clr-primary">{{ number_format((int) ($work->views_last_7d ?? 0)) }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-semibold text-clr-primary">{{ number_format((int) ($work->views_last_30d ?? 0)) }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold text-teal">{{ number_format($work->view_count) }}</span>
                                </td>
                                <td class="text-center d-none d-lg-table-cell">
                                    <span class="text-clr-primary">{{ $work->approved_comments_count }}</span>
                                </td>
                                <td class="text-center d-none d-lg-table-cell">
                                    <span class="text-clr-primary">{{ $work->favorites_count }}</span>
                                </td>
                                <td class="text-center d-none d-md-table-cell">
                                    <small class="text-clr-muted">{{ $work->published_at?->translatedFormat('d M Y') ?? '-' }}</small>
                                </td>
                            </tr>
                        @empty
                            <x-admin.table-empty icon="bi-journal-text" message="Filtrelere uygun eser bulunamadı." />
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($works->hasPages())
                <div class="usr-pagination">
                    <div class="usr-pagination-info">
                        <span>Toplam <strong>{{ number_format($works->total()) }}</strong> eserden <strong>{{ $works->firstItem() }}-{{ $works->lastItem() }}</strong> gösteriliyor</span>
                    </div>
                    <div class="usr-pagination-controls">
                        <div class="usr-page-btns">
                            @if($works->onFirstPage())
                                <button class="usr-page-btn" disabled><i class="bi bi-chevron-left"></i></button>
                            @else
                                <a href="{{ $works->previousPageUrl() }}" class="usr-page-btn"><i class="bi bi-chevron-left"></i></a>
                            @endif

                            @foreach($works->getUrlRange(max(1, $works->currentPage() - 2), min($works->lastPage(), $works->currentPage() + 2)) as $page => $url)
                                <a href="{{ $url }}" class="usr-page-btn {{ $page === $works->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                            @endforeach

                            @if($works->hasMorePages())
                                <a href="{{ $works->nextPageUrl() }}" class="usr-page-btn"><i class="bi bi-chevron-right"></i></a>
                            @else
                                <button class="usr-page-btn" disabled><i class="bi bi-chevron-right"></i></button>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection
