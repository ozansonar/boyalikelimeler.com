@extends('layouts.admin')

@section('title', 'Edebiyat Eserleri — Admin')

@section('content')

    <x-admin.page-header title="Edebiyat Eserleri" subtitle="Yazarların gönderdiği edebiyat eserlerini inceleyin ve yönetin">
    </x-admin.page-header>

    <!-- Stats -->
    <div class="row g-4 mb-4">
        <x-admin.stat-card color="blue" icon="bi-journal-text" label="Toplam Eser" :count="$stats['total']" :delay="0" col-class="col-xxl-3 col-xl-6 col-sm-6" />
        <x-admin.stat-card color="orange" icon="bi-hourglass-split" label="Beklemede" :count="$stats['pending']" :delay="100" col-class="col-xxl-3 col-xl-6 col-sm-6" />
        <x-admin.stat-card color="green" icon="bi-check-circle-fill" label="Onaylandı" :count="$stats['approved']" :delay="200" col-class="col-xxl-3 col-xl-6 col-sm-6" />
        <x-admin.stat-card color="purple" icon="bi-arrow-repeat" label="Revize Bekleniyor" :count="$stats['revision_requested']" :delay="300" col-class="col-xxl-3 col-xl-6 col-sm-6" />
    </div>

    <!-- Status Tabs -->
    <div class="cl-status-tabs mb-4" data-aos="fade-up" data-aos-delay="100">
        <a href="{{ route('admin.literary-works.index', request()->except(['status', 'page'])) }}" class="cl-status-tab {{ empty($filters['status']) ? 'active' : '' }}">
            <span>Tümü</span>
            <span class="cl-tab-count">{{ $stats['total'] }}</span>
        </a>
        <a href="{{ route('admin.literary-works.index', array_merge(request()->except('page'), ['status' => 'pending'])) }}" class="cl-status-tab {{ ($filters['status'] ?? '') === 'pending' ? 'active' : '' }}">
            <i class="bi bi-hourglass-split text-neon-orange"></i>
            <span>Beklemede</span>
            <span class="cl-tab-count">{{ $stats['pending'] }}</span>
        </a>
        <a href="{{ route('admin.literary-works.index', array_merge(request()->except('page'), ['status' => 'approved'])) }}" class="cl-status-tab {{ ($filters['status'] ?? '') === 'approved' ? 'active' : '' }}">
            <i class="bi bi-check-circle text-neon-green"></i>
            <span>Onaylandı</span>
            <span class="cl-tab-count">{{ $stats['approved'] }}</span>
        </a>
        <a href="{{ route('admin.literary-works.index', array_merge(request()->except('page'), ['status' => 'revision_requested'])) }}" class="cl-status-tab {{ ($filters['status'] ?? '') === 'revision_requested' ? 'active' : '' }}">
            <i class="bi bi-arrow-repeat text-neon-blue"></i>
            <span>Revize</span>
            <span class="cl-tab-count">{{ $stats['revision_requested'] }}</span>
        </a>
        <a href="{{ route('admin.literary-works.index', array_merge(request()->except('page'), ['status' => 'rejected'])) }}" class="cl-status-tab {{ ($filters['status'] ?? '') === 'rejected' ? 'active' : '' }}">
            <i class="bi bi-x-circle text-neon-red"></i>
            <span>Reddedildi</span>
            <span class="cl-tab-count">{{ $stats['rejected'] }}</span>
        </a>
    </div>

    <!-- Filters -->
    <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="150">
        <div class="card-body-custom">
            <form method="GET" action="{{ route('admin.literary-works.index') }}" class="cl-toolbar">
                <div class="cl-search">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Başlık veya yazar adı ile ara...">
                </div>

                <div class="cl-filters">
                    <select class="cl-filter-select" name="category" onchange="this.form.submit()">
                        <option value="">Tüm Kategoriler</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ ($filters['category'] ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>

                    @if(!empty($filters['status']))
                        <input type="hidden" name="status" value="{{ $filters['status'] }}">
                    @endif
                </div>

                <div class="cl-toolbar-actions">
                    <button type="submit" class="btn-glass"><i class="bi bi-funnel me-1"></i>Filtrele</button>
                    @if(!empty($filters['search']) || !empty($filters['category']))
                        <a href="{{ route('admin.literary-works.index', !empty($filters['status']) ? ['status' => $filters['status']] : []) }}" class="cl-filter-reset" title="Filtreleri Sıfırla">
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

    <!-- Table -->
    <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="200">
        <div class="card-body-custom p-0">
            <div class="table-responsive">
                <table class="table table-hover cl-table mb-0">
                    <thead>
                        <tr>
                            <th>Eser</th>
                            <th class="d-none d-md-table-cell">Kategori</th>
                            <th class="d-none d-lg-table-cell">Yazar</th>
                            <th>Durum</th>
                            <th class="d-none d-xxl-table-cell">Tarih</th>
                            <th class="cl-th-actions">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($works as $work)
                            <tr>
                                <td>
                                    <div class="cl-content-cell">
                                        @if($work->cover_image)
                                            <div class="cl-content-thumb">
                                                <img src="/uploads/{{ $work->cover_image }}" alt="">
                                            </div>
                                        @else
                                            <div class="cl-content-thumb draft"><i class="bi bi-journal-text"></i></div>
                                        @endif
                                        <div class="cl-content-info">
                                            <span class="cl-content-title">{{ $work->title }}</span>
                                            <span class="cl-content-meta">
                                                <i class="bi bi-clock me-1"></i>~{{ $work->readingTime() }} dk okuma
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <span class="cl-category-badge tech">{{ $work->category?->name ?? '-' }}</span>
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <div class="cl-author-cell">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($work->author?->name ?? 'U') }}&background=14b8a6&color=fff&size=28" alt="">
                                        <span>{{ $work->author?->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="usr-status-badge {{ $work->status->badgeClass() }}">{{ $work->status->label() }}</span>
                                </td>
                                <td class="d-none d-xxl-table-cell">
                                    <span class="usr-meta">{{ $work->created_at->format('d M Y') }}</span>
                                </td>
                                <td>
                                    <div class="usr-actions">
                                        <a class="usr-action-btn" title="İncele" href="{{ route('admin.literary-works.show', $work->id) }}"><i class="bi bi-eye"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-admin.table-empty :colspan="6" icon="bi-journal-text" message="Henüz edebiyat eseri gönderilmemiş." />
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($works->hasPages())
                <div class="cl-pagination-wrapper">
                    <div class="cl-pagination-info">
                        <span>Toplam <strong>{{ number_format($works->total()) }}</strong> eserden <strong>{{ $works->firstItem() }}-{{ $works->lastItem() }}</strong> arası gösteriliyor</span>
                    </div>
                    <nav class="cl-pagination">
                        @if($works->onFirstPage())
                            <button class="cl-page-btn" disabled><i class="bi bi-chevron-left"></i></button>
                        @else
                            <a href="{{ $works->previousPageUrl() }}" class="cl-page-btn"><i class="bi bi-chevron-left"></i></a>
                        @endif

                        @foreach($works->getUrlRange(max(1, $works->currentPage() - 2), min($works->lastPage(), $works->currentPage() + 2)) as $page => $url)
                            <a href="{{ $url }}" class="cl-page-btn {{ $page === $works->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                        @endforeach

                        @if($works->hasMorePages())
                            <a href="{{ $works->nextPageUrl() }}" class="cl-page-btn"><i class="bi bi-chevron-right"></i></a>
                        @else
                            <button class="cl-page-btn" disabled><i class="bi bi-chevron-right"></i></button>
                        @endif
                    </nav>
                </div>
            @endif
        </div>
    </div>

@endsection
