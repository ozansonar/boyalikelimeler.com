@extends('layouts.admin')

@section('title', 'Edebiyat Eserleri — Admin')

@section('content')

    <x-admin.page-header title="Edebiyat Eserleri" subtitle="Yazarların gönderdiği edebiyat eserlerini inceleyin ve yönetin">
    </x-admin.page-header>

    <!-- Work Type Toggle -->
    <div class="cl-status-tabs mb-4" data-aos="fade-up">
        <a href="{{ route('admin.literary-works.index', request()->except(['work_type', 'page'])) }}" class="cl-status-tab {{ empty($filters['work_type']) ? 'active' : '' }}">
            <i class="bi bi-grid"></i>
            <span>Tümü</span>
        </a>
        @foreach(\App\Enums\LiteraryWorkType::cases() as $type)
            <a href="{{ route('admin.literary-works.index', array_merge(request()->except('page'), ['work_type' => $type->value])) }}" class="cl-status-tab {{ ($filters['work_type'] ?? '') === $type->value ? 'active' : '' }}">
                <i class="bi {{ $type->icon() }}"></i>
                <span>{{ $type->label() }}</span>
            </a>
        @endforeach
    </div>

    <!-- Stats -->
    <div class="row g-4 mb-4">
        <x-admin.stat-card color="blue" icon="bi-journal-text" label="Toplam Eser" :count="$stats['total']" :delay="0" col-class="col-xxl col-xl-6 col-sm-6" />
        <x-admin.stat-card color="orange" icon="bi-hourglass-split" label="Beklemede" :count="$stats['pending']" :delay="100" col-class="col-xxl col-xl-6 col-sm-6" />
        <x-admin.stat-card color="green" icon="bi-check-circle-fill" label="Onaylandı" :count="$stats['approved']" :delay="200" col-class="col-xxl col-xl-6 col-sm-6" />
        <x-admin.stat-card color="purple" icon="bi-arrow-repeat" label="Revize Bekleniyor" :count="$stats['revision_requested']" :delay="300" col-class="col-xxl col-xl-6 col-sm-6" />
        <x-admin.stat-card color="yellow" icon="bi-eye-slash" label="Yayından Kaldırıldı" :count="$stats['unpublished']" :delay="400" col-class="col-xxl col-xl-6 col-sm-6" />
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
        <a href="{{ route('admin.literary-works.index', array_merge(request()->except('page'), ['status' => 'unpublished'])) }}" class="cl-status-tab {{ ($filters['status'] ?? '') === 'unpublished' ? 'active' : '' }}">
            <i class="bi bi-eye-slash text-neon-yellow"></i>
            <span>Yayından Kaldırıldı</span>
            <span class="cl-tab-count">{{ $stats['unpublished'] }}</span>
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
                    @if(!empty($filters['work_type']))
                        <input type="hidden" name="work_type" value="{{ $filters['work_type'] }}">
                    @endif

                    <select class="cl-filter-select" name="category" onchange="this.form.submit()">
                        <option value="">Tüm Kategoriler</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ ($filters['category'] ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>

                    <select class="cl-filter-select" name="author" onchange="this.form.submit()">
                        <option value="">Tüm Yazarlar</option>
                        @foreach($authors as $author)
                            <option value="{{ $author->id }}" {{ ($filters['author'] ?? '') == $author->id ? 'selected' : '' }}>{{ $author->name }}</option>
                        @endforeach
                    </select>

                    @if(!empty($filters['status']))
                        <input type="hidden" name="status" value="{{ $filters['status'] }}">
                    @endif
                </div>

                <div class="cl-toolbar-actions">
                    <button type="submit" class="btn-glass"><i class="bi bi-funnel me-1"></i>Filtrele</button>
                    @if(!empty($filters['search']) || !empty($filters['category']) || !empty($filters['author']))
                        <a href="{{ route('admin.literary-works.index', array_filter(['status' => $filters['status'] ?? null, 'work_type' => $filters['work_type'] ?? null])) }}" class="cl-filter-reset" title="Filtreleri Sıfırla">
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
                            <th class="d-none d-md-table-cell">Tür</th>
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
                                    <span class="usr-status-badge {{ $work->work_type?->badgeClass() ?? 'active' }}">{{ $work->work_type?->label() ?? 'Yazılı Eser' }}</span>
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
                                        <a class="usr-action-btn" title="Düzenle" href="{{ route('admin.literary-works.edit', $work) }}"><i class="bi bi-pencil"></i></a>
                                        @if($work->status === \App\Enums\LiteraryWorkStatus::Approved)
                                            <button class="usr-action-btn warning" title="Yayından Kaldır" onclick="openConfirmModal('unpublish', {{ $work->id }}, '{{ addslashes($work->title) }}')"><i class="bi bi-eye-slash"></i></button>
                                        @endif
                                        <button class="usr-action-btn danger" title="Sil" onclick="openConfirmModal('delete', {{ $work->id }}, '{{ addslashes($work->title) }}')"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-admin.table-empty :colspan="7" icon="bi-journal-text" message="Henüz edebiyat eseri gönderilmemiş." />
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

    <!-- Delete Confirm Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="status-modal-icon danger">
                        <i class="bi bi-trash"></i>
                    </div>
                    <h5 class="cl-modal-heading">Eseri Sil</h5>
                    <p class="cl-modal-body-text"><strong id="deleteItemName"></strong> adlı eseri silmek istediğinize emin misiniz?</p>
                    <p class="cl-modal-warning"><i class="bi bi-exclamation-triangle me-1"></i>Bu işlem geri alınamaz.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn-glass" data-bs-dismiss="modal">Vazgeç</button>
                        <form id="deleteForm" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-teal btn-danger-gradient"><i class="bi bi-trash me-1"></i>Sil</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Unpublish Confirm Modal -->
    <div class="modal fade" id="unpublishModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="status-modal-icon warning">
                        <i class="bi bi-eye-slash"></i>
                    </div>
                    <h5 class="cl-modal-heading">Yayından Kaldır</h5>
                    <p class="cl-modal-body-text"><strong id="unpublishItemName"></strong> adlı eseri yayından kaldırmak istediğinize emin misiniz?</p>
                    <p class="cl-modal-info"><i class="bi bi-info-circle me-1"></i>Eser sitede görünmeyecek, tekrar yayınlamak için onay gerekecektir.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn-glass" data-bs-dismiss="modal">Vazgeç</button>
                        <form id="unpublishForm" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-teal btn-warning-gradient"><i class="bi bi-eye-slash me-1"></i>Kaldır</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
function openConfirmModal(type, id, title) {
    var modalId, formId, nameId, baseUrl;

    if (type === 'delete') {
        modalId = 'deleteModal';
        formId = 'deleteForm';
        nameId = 'deleteItemName';
        baseUrl = '{{ url("admin/literary-works") }}/' + id;
    } else if (type === 'unpublish') {
        modalId = 'unpublishModal';
        formId = 'unpublishForm';
        nameId = 'unpublishItemName';
        baseUrl = '{{ url("admin/literary-works") }}/' + id + '/unpublish';
    }

    var modal = document.getElementById(modalId);
    var form = document.getElementById(formId);
    var nameEl = document.getElementById(nameId);

    if (nameEl) nameEl.textContent = title || '';
    if (form) form.action = baseUrl;

    if (modal) {
        var bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    }
}
</script>
@endpush
