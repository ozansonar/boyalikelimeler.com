@extends('layouts.admin')

@section('title', 'Söz Meydanı Soruları — Admin')

@section('content')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3" data-aos="fade-down" data-aos-duration="400">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link"><i class="bi bi-house me-1"></i>Ana Sayfa</a>
            </li>
            <li class="breadcrumb-item active text-teal">Söz Meydanı Soruları</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4" data-aos="fade-down">
        <div>
            <h1 class="page-title">Söz Meydanı Soruları</h1>
            <p class="page-subtitle">Kullanıcıların sorduğu soruları yönetin ve moderasyon yapın</p>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row g-4 mb-4">
        <x-admin.stat-card color="blue" icon="bi-question-circle-fill" label="Toplam Soru" :count="$stats['total']" :delay="0" col-class="col-xxl-3 col-xl-6 col-sm-6" />
        <x-admin.stat-card color="orange" icon="bi-hourglass-split" label="Onay Bekleyen" :count="$stats['pending']" :delay="50" col-class="col-xxl-3 col-xl-6 col-sm-6" />
        <x-admin.stat-card color="green" icon="bi-check-circle-fill" label="Onaylanan" :count="$stats['approved']" :delay="100" col-class="col-xxl-3 col-xl-6 col-sm-6" />
        <x-admin.stat-card color="red" icon="bi-x-circle-fill" label="Reddedilen" :count="$stats['rejected']" :delay="150" col-class="col-xxl-3 col-xl-6 col-sm-6" />
    </div>

    <!-- Status Tabs -->
    <div class="cl-status-tabs mb-4" data-aos="fade-up" data-aos-delay="100">
        <a href="{{ route('admin.qna.questions.index', array_merge(request()->except(['status', 'page']), [])) }}" class="cl-status-tab {{ empty($filters['status']) ? 'active' : '' }}">
            <span>Tümü</span>
            <span class="cl-tab-count">{{ $stats['total'] }}</span>
        </a>
        <a href="{{ route('admin.qna.questions.index', array_merge(request()->except('page'), ['status' => 'pending'])) }}" class="cl-status-tab {{ ($filters['status'] ?? '') === 'pending' ? 'active' : '' }}">
            <i class="bi bi-hourglass-split text-neon-orange"></i>
            <span>Onay Bekleyen</span>
            <span class="cl-tab-count">{{ $stats['pending'] }}</span>
        </a>
        <a href="{{ route('admin.qna.questions.index', array_merge(request()->except('page'), ['status' => 'approved'])) }}" class="cl-status-tab {{ ($filters['status'] ?? '') === 'approved' ? 'active' : '' }}">
            <i class="bi bi-check-circle text-neon-green"></i>
            <span>Onaylı</span>
            <span class="cl-tab-count">{{ $stats['approved'] }}</span>
        </a>
        <a href="{{ route('admin.qna.questions.index', array_merge(request()->except('page'), ['status' => 'rejected'])) }}" class="cl-status-tab {{ ($filters['status'] ?? '') === 'rejected' ? 'active' : '' }}">
            <i class="bi bi-x-circle text-neon-red"></i>
            <span>Reddedilen</span>
            <span class="cl-tab-count">{{ $stats['rejected'] }}</span>
        </a>
    </div>

    <!-- Toolbar -->
    <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="200">
        <div class="card-body-custom">
            <form method="GET" action="{{ route('admin.qna.questions.index') }}" class="cl-toolbar">
                <div class="cl-search">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Soru başlığı veya içeriği ile ara...">
                </div>
                <div class="cl-toolbar-actions">
                    @if(!empty($filters['status']))
                        <input type="hidden" name="status" value="{{ $filters['status'] }}">
                    @endif
                    <select name="category" class="cl-filter-select" onchange="this.form.submit()">
                        <option value="">Tüm Kategoriler</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ ($filters['category'] ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-glass"><i class="bi bi-funnel me-1"></i>Filtrele</button>
                    @if(!empty($filters['search']) || !empty($filters['category']))
                        <a href="{{ route('admin.qna.questions.index', array_filter(['status' => $filters['status'] ?? null])) }}" class="cl-filter-reset" title="Filtreleri Sıfırla">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    @endif
                    <div class="cl-per-page">
                        <label>Göster:</label>
                        <select name="per_page" onchange="this.form.submit()">
                            @foreach([10, 20, 50, 100] as $pp)
                                <option value="{{ $pp }}" {{ $perPage === $pp ? 'selected' : '' }}>{{ $pp }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="250">
        <div class="card-body-custom p-0">
            <div class="table-responsive">
                <table class="table table-hover cl-table mb-0">
                    <thead>
                        <tr>
                            <th>Soru</th>
                            <th class="d-none d-md-table-cell">Kategori</th>
                            <th class="d-none d-lg-table-cell">İstatistik</th>
                            <th>Durum</th>
                            <th class="d-none d-xl-table-cell">Tarih</th>
                            <th class="cl-th-actions">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($questions as $question)
                            <tr>
                                <td>
                                    <div class="cl-content-cell">
                                        <div class="cl-content-info">
                                            <span class="cl-content-title">{{ Str::limit($question->title, 60) }}</span>
                                            <span class="cl-content-meta">
                                                <i class="bi bi-person me-1"></i>{{ $question->user?->name ?? 'Anonim' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <span class="cl-category-badge tech">{{ $question->category?->name }}</span>
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <span class="usr-meta">
                                        <i class="bi bi-chat me-1"></i>{{ $question->answer_count }}
                                        <i class="bi bi-eye ms-2 me-1"></i>{{ $question->view_count }}
                                        <i class="bi bi-hand-thumbs-up ms-2 me-1"></i>{{ $question->like_count }}
                                    </span>
                                </td>
                                <td>
                                    @if($question->status->value === 'approved')
                                        <span class="usr-status-badge usr-status-badge-green">Onaylı</span>
                                    @elseif($question->status->value === 'pending')
                                        <span class="usr-status-badge usr-status-badge-orange">Bekliyor</span>
                                    @else
                                        <span class="usr-status-badge usr-status-badge-red">Reddedildi</span>
                                    @endif
                                </td>
                                <td class="d-none d-xl-table-cell">
                                    <span class="usr-meta">{{ $question->created_at->format('d M Y H:i') }}</span>
                                </td>
                                <td>
                                    <div class="usr-actions">
                                        <a class="usr-action-btn" title="Detay" href="{{ route('admin.qna.questions.show', $question->id) }}"><i class="bi bi-eye"></i></a>
                                        @if($question->status->value === 'pending')
                                            <button class="usr-action-btn success" title="Onayla" onclick="openQnaApproveModal({{ $question->id }}, {{ Js::from(Str::limit(str_replace(["\r\n", "\r", "\n"], ' ', $question->title), 40)) }}, 'question')">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                            <button class="usr-action-btn warning" title="Reddet" onclick="openQnaRejectModal({{ $question->id }}, {{ Js::from(Str::limit(str_replace(["\r\n", "\r", "\n"], ' ', $question->title), 40)) }}, 'question')">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        @endif
                                        <button class="usr-action-btn danger" title="Sil" onclick="openQnaDeleteModal({{ $question->id }}, {{ Js::from(Str::limit(str_replace(["\r\n", "\r", "\n"], ' ', $question->title), 40)) }}, 'question')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-admin.table-empty :colspan="6" icon="bi-question-circle" message="Henüz soru sorulmamış." />
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($questions->hasPages())
                <div class="cl-pagination-wrapper">
                    <div class="cl-pagination-info">
                        <span>Toplam <strong>{{ number_format($questions->total()) }}</strong> sorudan <strong>{{ $questions->firstItem() }}-{{ $questions->lastItem() }}</strong> arası gösteriliyor</span>
                    </div>
                    <nav class="cl-pagination">
                        @if($questions->onFirstPage())
                            <button class="cl-page-btn" disabled><i class="bi bi-chevron-left"></i></button>
                        @else
                            <a href="{{ $questions->previousPageUrl() }}" class="cl-page-btn"><i class="bi bi-chevron-left"></i></a>
                        @endif
                        @foreach($questions->getUrlRange(max(1, $questions->currentPage() - 2), min($questions->lastPage(), $questions->currentPage() + 2)) as $page => $url)
                            <a href="{{ $url }}" class="cl-page-btn {{ $page === $questions->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                        @endforeach
                        @if($questions->hasMorePages())
                            <a href="{{ $questions->nextPageUrl() }}" class="cl-page-btn"><i class="bi bi-chevron-right"></i></a>
                        @else
                            <button class="cl-page-btn" disabled><i class="bi bi-chevron-right"></i></button>
                        @endif
                    </nav>
                </div>
            @endif
        </div>
    </div>

    @include('admin.qna-questions._modals')

@endsection

@push('scripts')
<script src="{{ asset('assets/admin/js/qna.js') }}?v={{ filemtime(public_path('assets/admin/js/qna.js')) }}"></script>
@endpush
