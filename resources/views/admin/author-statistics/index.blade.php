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

    <!-- KPI Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xxl-3 col-xl-6 col-sm-6" data-aos="fade-up" data-aos-delay="0">
            <div class="anl-kpi-card">
                <div class="anl-kpi-header">
                    <div class="anl-kpi-icon anl-kpi-icon-teal">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
                <h3 class="anl-kpi-value">{{ number_format($stats['total_authors']) }}</h3>
                <span class="anl-kpi-label">Aktif Yazar Sayısı</span>
            </div>
        </div>
        <div class="col-xxl-3 col-xl-6 col-sm-6" data-aos="fade-up" data-aos-delay="100">
            <div class="anl-kpi-card">
                <div class="anl-kpi-header">
                    <div class="anl-kpi-icon anl-kpi-icon-blue">
                        <i class="bi bi-eye-fill"></i>
                    </div>
                </div>
                <h3 class="anl-kpi-value">{{ $stats['top_author_name'] }}</h3>
                <span class="anl-kpi-label">Bu Ay En Çok Okunan Yazar ({{ number_format($stats['top_author_views']) }} okunma)</span>
            </div>
        </div>
        <div class="col-xxl-3 col-xl-6 col-sm-6" data-aos="fade-up" data-aos-delay="200">
            <div class="anl-kpi-card">
                <div class="anl-kpi-header">
                    <div class="anl-kpi-icon anl-kpi-icon-green">
                        <i class="bi bi-journal-text"></i>
                    </div>
                </div>
                <h3 class="anl-kpi-value">{{ $stats['top_publisher_name'] }}</h3>
                <span class="anl-kpi-label">Bu Ay En Çok Eser Yayınlayan ({{ number_format($stats['top_publisher_count']) }} eser)</span>
            </div>
        </div>
        <div class="col-xxl-3 col-xl-6 col-sm-6" data-aos="fade-up" data-aos-delay="300">
            <div class="anl-kpi-card">
                <div class="anl-kpi-header">
                    <div class="anl-kpi-icon anl-kpi-icon-orange">
                        <i class="bi bi-bar-chart-fill"></i>
                    </div>
                </div>
                <h3 class="anl-kpi-value">{{ number_format($stats['avg_views_per_author'], 1) }}</h3>
                <span class="anl-kpi-label">Ortalama Okunma / Yazar</span>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="150">
        <div class="card-body-custom">
            <form method="GET" action="{{ route('admin.author-statistics.index') }}" class="cl-toolbar">
                <div class="cl-search">
                    <i class="bi bi-search cl-search-icon"></i>
                    <input type="text" name="search" class="cl-search-input" placeholder="Yazar ara..."
                           value="{{ $filters['search'] ?? '' }}">
                </div>
                <div class="cl-filters">
                    <select name="per_page" class="cl-select" onchange="this.form.submit()">
                        @foreach([10, 25, 50, 100] as $pp)
                            <option value="{{ $pp }}" {{ $perPage === $pp ? 'selected' : '' }}>{{ $pp }} kayıt</option>
                        @endforeach
                    </select>
                </div>
                <div class="cl-actions">
                    <button type="submit" class="btn-teal btn-sm">
                        <i class="bi bi-search"></i> Ara
                    </button>
                    <a href="{{ route('admin.author-statistics.index') }}" class="btn-glass btn-sm">
                        <i class="bi bi-arrow-counterclockwise"></i> Sıfırla
                    </a>
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
        </div>
    </div>

    <!-- Pagination -->
    @if($authors->hasPages())
        <div data-aos="fade-up" data-aos-delay="250">
            {{ $authors->links() }}
        </div>
    @endif

@endsection
