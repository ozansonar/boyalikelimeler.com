@extends('layouts.admin')
@section('title', 'Yazar İstatistikleri — Admin')
@section('content')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3" data-aos="fade-down" data-aos-duration="400">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link"><i class="bi bi-house me-1"></i>Ana Sayfa</a>
            </li>
            <li class="breadcrumb-item active text-teal">Yazar İstatistikleri</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <x-admin.page-header title="Yazar İstatistikleri" subtitle="Yazarların eser ve okunma performans analizleri">
    </x-admin.page-header>

    <!-- Work Type Toggle -->
    <div class="cl-status-tabs mb-4" data-aos="fade-up">
        <a href="{{ route('admin.author-statistics.index', request()->except(['work_type', 'page'])) }}" class="cl-status-tab {{ empty($filters['work_type']) ? 'active' : '' }}">
            <i class="bi bi-grid"></i>
            <span>Tümü</span>
        </a>
        @foreach(\App\Enums\LiteraryWorkType::cases() as $type)
            <a href="{{ route('admin.author-statistics.index', array_merge(request()->except('page'), ['work_type' => $type->value])) }}" class="cl-status-tab {{ ($filters['work_type'] ?? '') === $type->value ? 'active' : '' }}">
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
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
                <h3 class="anl-kpi-value">{{ number_format($stats['total_authors']) }}</h3>
                <span class="anl-kpi-label">Toplam Yazar</span>
            </div>
        </div>
        <div class="col-xxl-2 col-xl-4 col-sm-6" data-aos="fade-up" data-aos-delay="50">
            <div class="anl-kpi-card h-100">
                <div class="anl-kpi-header">
                    <div class="anl-kpi-icon anl-kpi-icon-purple">
                        <i class="bi bi-journal-check"></i>
                    </div>
                </div>
                <h3 class="anl-kpi-value">{{ number_format($stats['total_works']) }}</h3>
                <span class="anl-kpi-label">Toplam Onaylı Eser</span>
            </div>
        </div>
        <div class="col-xxl-2 col-xl-4 col-sm-6" data-aos="fade-up" data-aos-delay="100">
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
        <div class="col-xxl-2 col-xl-4 col-sm-6" data-aos="fade-up" data-aos-delay="150">
            <div class="anl-kpi-card h-100">
                <div class="anl-kpi-header">
                    <div class="anl-kpi-icon anl-kpi-icon-green">
                        <i class="bi bi-trophy-fill"></i>
                    </div>
                </div>
                <h3 class="anl-kpi-value ast-kpi-value-sm">{{ Str::limit($stats['top_author_name'], 20) }}</h3>
                <span class="anl-kpi-label">En Çok Okunan ({{ number_format($stats['top_author_views']) }})</span>
            </div>
        </div>
        <div class="col-xxl-2 col-xl-4 col-sm-6" data-aos="fade-up" data-aos-delay="200">
            <div class="anl-kpi-card h-100">
                <div class="anl-kpi-header">
                    <div class="anl-kpi-icon anl-kpi-icon-orange">
                        <i class="bi bi-pen-fill"></i>
                    </div>
                </div>
                <h3 class="anl-kpi-value ast-kpi-value-sm">{{ Str::limit($stats['top_publisher_name'], 20) }}</h3>
                <span class="anl-kpi-label">En Üretken — Bu Ay ({{ $stats['top_publisher_count'] }} eser)</span>
            </div>
        </div>
        <div class="col-xxl-2 col-xl-4 col-sm-6" data-aos="fade-up" data-aos-delay="250">
            <div class="anl-kpi-card h-100">
                <div class="anl-kpi-header">
                    <div class="anl-kpi-icon anl-kpi-icon-blue">
                        <i class="bi bi-bar-chart-fill"></i>
                    </div>
                </div>
                <h3 class="anl-kpi-value">{{ number_format($stats['avg_views_per_author']) }}</h3>
                <span class="anl-kpi-label">Yazar Başına Ort. Okunma</span>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="150">
        <div class="card-body-custom">
            <form method="GET" action="{{ route('admin.author-statistics.index') }}" class="usr-toolbar flex-wrap">
                @if(!empty($filters['work_type']))
                    <input type="hidden" name="work_type" value="{{ $filters['work_type'] }}">
                @endif
                <div class="usr-search">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Yazar adı veya kullanıcı adı ile ara...">
                </div>

                <div class="usr-filters flex-wrap">
                    <select class="usr-filter-select" name="activity" onchange="this.form.submit()">
                        <option value="">Aktivite Durumu</option>
                        <option value="last_7" {{ ($filters['activity'] ?? '') === 'last_7' ? 'selected' : '' }}>Son 7 gün aktif</option>
                        <option value="last_30" {{ ($filters['activity'] ?? '') === 'last_30' ? 'selected' : '' }}>Son 30 gün aktif</option>
                        <option value="last_90" {{ ($filters['activity'] ?? '') === 'last_90' ? 'selected' : '' }}>Son 90 gün aktif</option>
                        <option value="inactive" {{ ($filters['activity'] ?? '') === 'inactive' ? 'selected' : '' }}>Pasif (90+ gün)</option>
                    </select>

                    <select class="usr-filter-select" name="min_works" onchange="this.form.submit()">
                        <option value="">Min. Eser Sayısı</option>
                        <option value="1" {{ ($filters['min_works'] ?? '') == '1' ? 'selected' : '' }}>1+ eser</option>
                        <option value="5" {{ ($filters['min_works'] ?? '') == '5' ? 'selected' : '' }}>5+ eser</option>
                        <option value="10" {{ ($filters['min_works'] ?? '') == '10' ? 'selected' : '' }}>10+ eser</option>
                        <option value="20" {{ ($filters['min_works'] ?? '') == '20' ? 'selected' : '' }}>20+ eser</option>
                    </select>

                    <select class="usr-filter-select" name="joined" onchange="this.form.submit()">
                        <option value="">Kayıt Tarihi</option>
                        <option value="this_month" {{ ($filters['joined'] ?? '') === 'this_month' ? 'selected' : '' }}>Bu ay</option>
                        <option value="last_3" {{ ($filters['joined'] ?? '') === 'last_3' ? 'selected' : '' }}>Son 3 ay</option>
                        <option value="last_6" {{ ($filters['joined'] ?? '') === 'last_6' ? 'selected' : '' }}>Son 6 ay</option>
                        <option value="last_12" {{ ($filters['joined'] ?? '') === 'last_12' ? 'selected' : '' }}>Son 1 yıl</option>
                    </select>

                    <select class="usr-filter-select" name="per_page" onchange="this.form.submit()">
                        @foreach([10, 25, 50, 100] as $pp)
                            <option value="{{ $pp }}" {{ $perPage === $pp ? 'selected' : '' }}>{{ $pp }} / sayfa</option>
                        @endforeach
                    </select>
                </div>

                @php
                    $hasAnyFilter = !empty($filters['search']) || !empty($filters['activity']) || !empty($filters['min_works']) || !empty($filters['joined']);
                @endphp

                <div class="usr-toolbar-actions">
                    <button type="submit" class="btn-glass"><i class="bi bi-funnel me-1"></i>Filtrele</button>
                    @if($hasAnyFilter)
                        <a href="{{ route('admin.author-statistics.index', array_filter(['work_type' => $filters['work_type'] ?? null])) }}" class="btn-glass"><i class="bi bi-x-lg me-1"></i>Temizle</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Authors Table -->
    <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="200">
        <div class="card-body-custom p-0">
            <div class="table-responsive">
                <table class="usr-table">
                    <thead>
                        <tr>
                            <th>
                                <a href="{{ route('admin.author-statistics.index', array_merge($filters, ['sort' => 'name', 'dir' => ($filters['sort'] ?? '') === 'name' && ($filters['dir'] ?? '') === 'asc' ? 'desc' : 'asc', 'per_page' => $perPage])) }}" class="text-decoration-none text-clr-secondary">
                                    Yazar
                                    @if(($filters['sort'] ?? '') === 'name')
                                        <i class="bi bi-arrow-{{ ($filters['dir'] ?? '') === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-center">
                                <a href="{{ route('admin.author-statistics.index', array_merge($filters, ['sort' => 'approved_works_count', 'dir' => ($filters['sort'] ?? '') === 'approved_works_count' && ($filters['dir'] ?? '') === 'desc' ? 'asc' : 'desc', 'per_page' => $perPage])) }}" class="text-decoration-none text-clr-secondary">
                                    Eser
                                    @if(($filters['sort'] ?? '') === 'approved_works_count')
                                        <i class="bi bi-arrow-{{ ($filters['dir'] ?? '') === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-center">
                                <a href="{{ route('admin.author-statistics.index', array_merge($filters, ['sort' => 'views_last_7d', 'dir' => ($filters['sort'] ?? '') === 'views_last_7d' && ($filters['dir'] ?? '') === 'desc' ? 'asc' : 'desc', 'per_page' => $perPage])) }}" class="text-decoration-none text-clr-secondary">
                                    Son 7 Gün
                                    @if(($filters['sort'] ?? '') === 'views_last_7d')
                                        <i class="bi bi-arrow-{{ ($filters['dir'] ?? '') === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-center">
                                <a href="{{ route('admin.author-statistics.index', array_merge($filters, ['sort' => 'views_last_30d', 'dir' => ($filters['sort'] ?? '') === 'views_last_30d' && ($filters['dir'] ?? '') === 'desc' ? 'asc' : 'desc', 'per_page' => $perPage])) }}" class="text-decoration-none text-clr-secondary">
                                    Son 30 Gün
                                    @if(($filters['sort'] ?? '') === 'views_last_30d')
                                        <i class="bi bi-arrow-{{ ($filters['dir'] ?? '') === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-center">
                                <a href="{{ route('admin.author-statistics.index', array_merge($filters, ['sort' => 'views_last_90d', 'dir' => ($filters['sort'] ?? '') === 'views_last_90d' && ($filters['dir'] ?? '') === 'desc' ? 'asc' : 'desc', 'per_page' => $perPage])) }}" class="text-decoration-none text-clr-secondary">
                                    Son 90 Gün
                                    @if(($filters['sort'] ?? '') === 'views_last_90d')
                                        <i class="bi bi-arrow-{{ ($filters['dir'] ?? '') === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-center">
                                <a href="{{ route('admin.author-statistics.index', array_merge($filters, ['sort' => 'total_views', 'dir' => ($filters['sort'] ?? '') === 'total_views' && ($filters['dir'] ?? '') === 'desc' ? 'asc' : 'desc', 'per_page' => $perPage])) }}" class="text-decoration-none text-clr-secondary">
                                    Toplam
                                    @if(($filters['sort'] ?? 'total_views') === 'total_views')
                                        <i class="bi bi-arrow-{{ ($filters['dir'] ?? 'desc') === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-center d-none d-xl-table-cell">
                                <a href="{{ route('admin.author-statistics.index', array_merge($filters, ['sort' => 'total_comments', 'dir' => ($filters['sort'] ?? '') === 'total_comments' && ($filters['dir'] ?? '') === 'desc' ? 'asc' : 'desc', 'per_page' => $perPage])) }}" class="text-decoration-none text-clr-secondary">
                                    Yorum
                                    @if(($filters['sort'] ?? '') === 'total_comments')
                                        <i class="bi bi-arrow-{{ ($filters['dir'] ?? '') === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-center d-none d-xl-table-cell">
                                <a href="{{ route('admin.author-statistics.index', array_merge($filters, ['sort' => 'total_favorites', 'dir' => ($filters['sort'] ?? '') === 'total_favorites' && ($filters['dir'] ?? '') === 'desc' ? 'asc' : 'desc', 'per_page' => $perPage])) }}" class="text-decoration-none text-clr-secondary">
                                    Favori
                                    @if(($filters['sort'] ?? '') === 'total_favorites')
                                        <i class="bi bi-arrow-{{ ($filters['dir'] ?? '') === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-center d-none d-xl-table-cell">
                                <a href="{{ route('admin.author-statistics.index', array_merge($filters, ['sort' => 'avg_rating', 'dir' => ($filters['sort'] ?? '') === 'avg_rating' && ($filters['dir'] ?? '') === 'desc' ? 'asc' : 'desc', 'per_page' => $perPage])) }}" class="text-decoration-none text-clr-secondary">
                                    Puan
                                    @if(($filters['sort'] ?? '') === 'avg_rating')
                                        <i class="bi bi-arrow-{{ ($filters['dir'] ?? '') === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-center">Detay</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($authors as $author)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="usr-avatar usr-avatar-sm">
                                            @if($author->avatar)
                                                <img src="{{ upload_url($author->avatar, 'thumb') }}" alt="{{ $author->name }}" class="rounded-circle" loading="lazy">
                                            @else
                                                {{ mb_substr($author->name, 0, 2) }}
                                            @endif
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-clr-primary">{{ $author->name }}</div>
                                            <small class="text-clr-muted">{{ '@' . $author->username }} &middot; {{ $author->created_at?->translatedFormat('M Y') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-teal-subtle text-teal">{{ $author->approved_works_count ?? 0 }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-semibold text-clr-primary">{{ number_format((int) ($author->views_last_7d ?? 0)) }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-semibold text-clr-primary">{{ number_format((int) ($author->views_last_30d ?? 0)) }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-semibold text-clr-primary">{{ number_format((int) ($author->views_last_90d ?? 0)) }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold text-teal">{{ number_format((int) ($author->total_views ?? 0)) }}</span>
                                </td>
                                <td class="text-center d-none d-xl-table-cell">
                                    <span class="text-clr-primary">{{ number_format((int) ($author->total_comments ?? 0)) }}</span>
                                </td>
                                <td class="text-center d-none d-xl-table-cell">
                                    <span class="text-clr-primary">{{ number_format((int) ($author->total_favorites ?? 0)) }}</span>
                                </td>
                                <td class="text-center d-none d-xl-table-cell">
                                    @if($author->avg_rating)
                                        <span class="fw-semibold text-warning"><i class="bi bi-star-fill me-1"></i>{{ number_format((float) $author->avg_rating, 1) }}</span>
                                    @else
                                        <span class="text-clr-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.author-statistics.show', $author) }}" class="btn-glass btn-sm" title="Detay">
                                        <i class="bi bi-bar-chart-line"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <x-admin.table-empty icon="bi-graph-up" message="Henüz eser yayınlamış yazar bulunamadı." />
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($authors->hasPages())
                <div class="usr-pagination">
                    <div class="usr-pagination-info">
                        <span>Toplam <strong>{{ number_format($authors->total()) }}</strong> yazardan <strong>{{ $authors->firstItem() }}-{{ $authors->lastItem() }}</strong> gösteriliyor</span>
                    </div>
                    <div class="usr-pagination-controls">
                        <div class="usr-page-btns">
                            @if($authors->onFirstPage())
                                <button class="usr-page-btn" disabled><i class="bi bi-chevron-left"></i></button>
                            @else
                                <a href="{{ $authors->previousPageUrl() }}" class="usr-page-btn"><i class="bi bi-chevron-left"></i></a>
                            @endif

                            @foreach($authors->getUrlRange(max(1, $authors->currentPage() - 2), min($authors->lastPage(), $authors->currentPage() + 2)) as $page => $url)
                                <a href="{{ $url }}" class="usr-page-btn {{ $page === $authors->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                            @endforeach

                            @if($authors->hasMorePages())
                                <a href="{{ $authors->nextPageUrl() }}" class="usr-page-btn"><i class="bi bi-chevron-right"></i></a>
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
