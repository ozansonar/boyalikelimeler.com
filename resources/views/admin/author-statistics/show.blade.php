@extends('layouts.admin')
@section('title', $author->name . ' — Yazar İstatistikleri — Admin')
@section('content')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3" data-aos="fade-down" data-aos-duration="400">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link"><i class="bi bi-house me-1"></i>Ana Sayfa</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.author-statistics.index') }}" class="breadcrumb-link">Yazar İstatistikleri</a>
            </li>
            <li class="breadcrumb-item active text-teal">{{ $author->name }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3" data-aos="fade-down">
        <div class="d-flex align-items-center gap-3">
            <div class="usr-avatar usr-avatar-lg">
                @if($author->avatar)
                    <img src="{{ upload_url($author->avatar, 'thumb') }}" alt="{{ $author->name }}" class="rounded-circle" loading="lazy">
                @else
                    {{ mb_substr($author->name, 0, 2) }}
                @endif
            </div>
            <div>
                <h1 class="page-title mb-0">{{ $author->name }}</h1>
                <p class="page-subtitle mb-0">{{ '@' . $author->username }} &middot; Üyelik: {{ $author->created_at?->translatedFormat('d M Y') }}</p>
            </div>
        </div>
        <a href="{{ route('admin.author-statistics.index') }}" class="btn-glass">
            <i class="bi bi-arrow-left"></i> Listeye Dön
        </a>
    </div>

    <!-- KPI Cards - Row 1: Works & Views -->
    <div class="row g-3 mb-3">
        <div class="col-xl-3 col-sm-6" data-aos="fade-up" data-aos-delay="0">
            <div class="anl-kpi-card h-100">
                <div class="anl-kpi-header">
                    <div class="anl-kpi-icon anl-kpi-icon-teal"><i class="bi bi-journal-check"></i></div>
                </div>
                <h3 class="anl-kpi-value">{{ $workStats['approved_works'] }}</h3>
                <span class="anl-kpi-label">Yayında Olan Eser</span>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6" data-aos="fade-up" data-aos-delay="50">
            <div class="anl-kpi-card h-100">
                <div class="anl-kpi-header">
                    <div class="anl-kpi-icon anl-kpi-icon-orange"><i class="bi bi-hourglass-split"></i></div>
                </div>
                <h3 class="anl-kpi-value">{{ $workStats['pending_works'] }}</h3>
                <span class="anl-kpi-label">Onay Bekleyen Eser</span>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6" data-aos="fade-up" data-aos-delay="100">
            <div class="anl-kpi-card h-100">
                <div class="anl-kpi-header">
                    <div class="anl-kpi-icon anl-kpi-icon-blue"><i class="bi bi-eye-fill"></i></div>
                </div>
                <h3 class="anl-kpi-value">{{ number_format($workStats['total_views']) }}</h3>
                <span class="anl-kpi-label">Toplam Görüntülenme</span>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6" data-aos="fade-up" data-aos-delay="150">
            <div class="anl-kpi-card h-100">
                <div class="anl-kpi-header">
                    <div class="anl-kpi-icon anl-kpi-icon-purple"><i class="bi bi-bar-chart-line-fill"></i></div>
                </div>
                <h3 class="anl-kpi-value">{{ number_format($workStats['avg_views_per_work']) }}</h3>
                <span class="anl-kpi-label">Eser Başına Ort. Okunma</span>
            </div>
        </div>
    </div>

    <!-- KPI Cards - Row 2: Engagement -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6" data-aos="fade-up" data-aos-delay="200">
            <div class="anl-kpi-card h-100">
                <div class="anl-kpi-header">
                    <div class="anl-kpi-icon anl-kpi-icon-green"><i class="bi bi-chat-dots-fill"></i></div>
                </div>
                <h3 class="anl-kpi-value">{{ number_format($workStats['total_comments']) }}</h3>
                <span class="anl-kpi-label">Toplam Yorum Sayısı</span>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6" data-aos="fade-up" data-aos-delay="250">
            <div class="anl-kpi-card h-100">
                <div class="anl-kpi-header">
                    <div class="anl-kpi-icon anl-kpi-icon-purple"><i class="bi bi-heart-fill"></i></div>
                </div>
                <h3 class="anl-kpi-value">{{ number_format($workStats['total_favorites']) }}</h3>
                <span class="anl-kpi-label">Toplam Favori Sayısı</span>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6" data-aos="fade-up" data-aos-delay="300">
            <div class="anl-kpi-card h-100">
                <div class="anl-kpi-header">
                    <div class="anl-kpi-icon anl-kpi-icon-teal"><i class="bi bi-graph-up-arrow"></i></div>
                    @if($monthlyComparison['change_percent'] != 0)
                        <span class="anl-kpi-trend {{ $monthlyComparison['change_percent'] > 0 ? 'positive' : 'negative' }}">
                            <i class="bi bi-arrow-{{ $monthlyComparison['change_percent'] > 0 ? 'up' : 'down' }}-short"></i>%{{ abs($monthlyComparison['change_percent']) }}
                        </span>
                    @endif
                </div>
                <h3 class="anl-kpi-value">{{ number_format($monthlyComparison['this_month']) }}</h3>
                <span class="anl-kpi-label">Bu Ay Okunma <small class="text-clr-muted">(Geçen ay: {{ number_format($monthlyComparison['last_month']) }})</small></span>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6" data-aos="fade-up" data-aos-delay="350">
            <div class="anl-kpi-card h-100">
                <div class="anl-kpi-header">
                    <div class="anl-kpi-icon anl-kpi-icon-blue"><i class="bi bi-calendar-check"></i></div>
                </div>
                <h3 class="anl-kpi-value ast-kpi-value-sm">{{ $workStats['first_published']?->translatedFormat('d M Y') ?? '-' }}</h3>
                <span class="anl-kpi-label">İlk Yayın Tarihi</span>
            </div>
        </div>
    </div>

    <!-- Charts Row 1: Son 7 Gün + Son 30 Gün -->
    <div class="row g-4 mb-4">
        <div class="col-xl-6" data-aos="fade-up" data-aos-delay="100">
            <div class="card-dark h-100">
                <div class="card-header-custom">
                    <h6><i class="bi bi-calendar-week me-2 text-teal"></i>Son 7 Gün — Günlük Okunma</h6>
                </div>
                <div class="card-body-custom">
                    <canvas id="chartWeekly" height="220"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6" data-aos="fade-up" data-aos-delay="150">
            <div class="card-dark h-100">
                <div class="card-header-custom">
                    <h6><i class="bi bi-calendar-month me-2 text-teal"></i>Son 30 Gün — Günlük Okunma</h6>
                </div>
                <div class="card-body-custom">
                    <canvas id="chartMonthly" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2: Haftalık Trend + Aylık Trend -->
    <div class="row g-4 mb-4">
        <div class="col-xl-6" data-aos="fade-up" data-aos-delay="200">
            <div class="card-dark h-100">
                <div class="card-header-custom">
                    <h6><i class="bi bi-bar-chart me-2 text-teal"></i>Son 4 Hafta — Haftalık Toplam</h6>
                </div>
                <div class="card-body-custom">
                    <canvas id="chartWeeklyTrend" height="220"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6" data-aos="fade-up" data-aos-delay="250">
            <div class="card-dark h-100">
                <div class="card-header-custom">
                    <h6><i class="bi bi-graph-up me-2 text-teal"></i>Son 6 Ay — Aylık Toplam</h6>
                </div>
                <div class="card-body-custom">
                    <canvas id="chartMonthlyTrend" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 3: Category Donut + Work Comparison -->
    <div class="row g-4 mb-4">
        <div class="col-xl-4" data-aos="fade-up" data-aos-delay="100">
            <div class="card-dark h-100">
                <div class="card-header-custom">
                    <h6><i class="bi bi-pie-chart-fill me-2 text-teal"></i>Kategori Dağılımı</h6>
                </div>
                <div class="card-body-custom">
                    @if($categoryDistribution->isNotEmpty())
                        @php
                            $catColors = ['#14b8a6','#3b82f6','#f97316','#a855f7','#22c55e','#ef4444','#eab308','#ec4899'];
                            $catTotal = $categoryDistribution->sum('count');
                        @endphp
                        <div class="d-flex justify-content-center mb-3">
                            <canvas id="chartCategoryDonut" width="200" height="200"></canvas>
                        </div>
                        <div class="ast-category-list">
                            @foreach($categoryDistribution as $cat)
                                @php $percent = $catTotal > 0 ? round(($cat->count / $catTotal) * 100, 1) : 0; @endphp
                                <div class="d-flex align-items-center justify-content-between py-2 {{ !$loop->last ? 'border-bottom border-secondary border-opacity-25' : '' }}">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="ast-legend-dot" style="background: {{ $catColors[$loop->index % count($catColors)] }}"></span>
                                        <div>
                                            <div class="fw-semibold text-clr-primary" style="font-size: 0.85rem">{{ $cat->name }}</div>
                                            <small class="text-clr-muted">{{ $cat->count }} eser &middot; {{ number_format((int) $cat->total_views) }} okunma</small>
                                        </div>
                                    </div>
                                    <span class="badge bg-teal-subtle text-teal">%{{ $percent }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-clr-muted text-center mb-0 py-4">Henüz kategori verisi yok.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-8" data-aos="fade-up" data-aos-delay="150">
            <div class="card-dark h-100">
                <div class="card-header-custom">
                    <h6><i class="bi bi-trophy-fill me-2 text-teal"></i>Eser Bazlı Karşılaştırma — Okunma, Yorum & Favori</h6>
                </div>
                <div class="card-body-custom">
                    <canvas id="chartWorkComparison" height="280"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Works Table -->
    <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="250">
        <div class="card-header-custom">
            <h6><i class="bi bi-list-ol me-2 text-teal"></i>En Popüler Eserler</h6>
        </div>
        <div class="card-body-custom p-0">
            <div class="table-responsive">
                <table class="usr-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Eser Adı</th>
                            <th class="text-center">Kategori</th>
                            <th class="text-center">Tür</th>
                            <th class="text-center">Okunma</th>
                            <th class="text-center">Yorum</th>
                            <th class="text-center">Favori</th>
                            <th class="text-center">Yayın Tarihi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topWorks as $index => $work)
                            <tr>
                                <td>
                                    @if($index < 3)
                                        <span class="badge {{ $index === 0 ? 'bg-warning' : ($index === 1 ? 'bg-secondary' : 'bg-danger-subtle text-danger') }}">
                                            {{ $index + 1 }}
                                        </span>
                                    @else
                                        <span class="text-clr-muted">{{ $index + 1 }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold text-clr-primary">{{ Str::limit($work->title, 50) }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-teal-subtle text-teal">{{ $work->category?->name ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="text-clr-muted">{{ $work->work_type?->value === 'written' ? 'Yazılı' : 'Görsel' }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold text-teal">{{ number_format($work->view_count) }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="text-clr-primary">{{ $work->approved_comments_count }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="text-clr-primary">{{ $work->favorites_count }}</span>
                                </td>
                                <td class="text-center">
                                    <small class="text-clr-muted">{{ $work->published_at?->translatedFormat('d M Y') ?? '-' }}</small>
                                </td>
                            </tr>
                        @empty
                            <x-admin.table-empty icon="bi-journal-text" message="Henüz yayınlanmış eser bulunamadı." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        window.astChartData = {
            weekly:       { labels: @json($weeklyViews['labels']), values: @json($weeklyViews['values']) },
            monthly:      { labels: @json($dailyViews['labels']), values: @json($dailyViews['values']) },
            weeklyTrend:  { labels: @json($weeklyTrend['labels']), values: @json($weeklyTrend['values']) },
            monthlyTrend: { labels: @json($monthlyTrend['labels']), values: @json($monthlyTrend['values']) },
            category: {
                labels: @json($categoryDistribution->pluck('name')->values()),
                values: @json($categoryDistribution->pluck('count')->values()->map(fn($v) => (int) $v)),
                colors: @json($catColors ?? ['#14b8a6','#3b82f6','#f97316','#a855f7','#22c55e','#ef4444','#eab308','#ec4899'])
            },
            workComparison: {
                labels:    @json($workViewsChart['labels']),
                views:     @json($workViewsChart['views']),
                comments:  @json($workViewsChart['comments']),
                favorites: @json($workViewsChart['favorites'])
            }
        };
    </script>
    <script src="{{ asset('assets/admin/js/author-statistics.js') }}"></script>
@endpush
