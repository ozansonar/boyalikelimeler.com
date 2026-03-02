@extends('layouts.admin')

@section('title', 'Kategoriler — Admin')

@section('content')

    <!-- Page Header -->
    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3" data-aos="fade-down">
        <div>
            <h1 class="page-title">Kategoriler</h1>
            <p class="page-subtitle">İçerik kategorilerini yönetin, düzenleyin ve sıralayın</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.categories.create') }}" class="btn-teal">
                <i class="bi bi-plus-lg me-1"></i> Yeni Kategori
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="100">
        <div class="card-body-custom">
            <form method="GET" action="{{ route('admin.categories.index') }}" class="cl-toolbar">
                <div class="cl-search">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Kategori adı ile ara...">
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
                        <a href="{{ route('admin.categories.index') }}" class="btn-glass"><i class="bi bi-x-lg me-1"></i>Temizle</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="150">
        <div class="card-body-custom p-0">
            <div class="table-responsive">
                <table class="table table-hover cl-table mb-0">
                    <thead>
                        <tr>
                            <th>Kategori Adı</th>
                            <th class="d-none d-md-table-cell">Slug</th>
                            <th class="d-none d-lg-table-cell">Açıklama</th>
                            <th>İçerik Sayısı</th>
                            <th>Durum</th>
                            <th>Sıra</th>
                            <th class="cl-th-actions">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $category->name }}</span>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <span class="usr-meta">{{ $category->slug }}</span>
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <span class="usr-meta">{{ Str::limit($category->description, 50) ?: '-' }}</span>
                                </td>
                                <td>
                                    <span class="cl-category-badge tech">{{ $category->posts_count }}</span>
                                </td>
                                <td>
                                    @if($category->is_active)
                                        <span class="usr-status-badge active">Aktif</span>
                                    @else
                                        <span class="usr-status-badge inactive">Pasif</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="usr-meta">{{ $category->sort_order }}</span>
                                </td>
                                <td>
                                    <div class="usr-actions">
                                        <a class="usr-action-btn" title="Düzenle" href="{{ route('admin.categories.edit', $category) }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button class="usr-action-btn danger" title="Sil" onclick="openDeleteModal({{ $category->id }}, '{{ addslashes($category->name) }}', {{ $category->posts_count }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-clr-muted">
                                    <i class="bi bi-folder2-open fs-1 d-block mb-2 opacity-50"></i>
                                    Henüz kategori oluşturulmamış.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($categories->hasPages())
                <div class="cl-pagination-wrapper">
                    <div class="cl-pagination-info">
                        <span>Toplam <strong>{{ $categories->total() }}</strong> kategoriden <strong>{{ $categories->firstItem() }}-{{ $categories->lastItem() }}</strong> gösteriliyor</span>
                    </div>
                    <nav class="cl-pagination">
                        @if($categories->onFirstPage())
                            <button class="cl-page-btn" disabled><i class="bi bi-chevron-left"></i></button>
                        @else
                            <a href="{{ $categories->previousPageUrl() }}" class="cl-page-btn"><i class="bi bi-chevron-left"></i></a>
                        @endif

                        @foreach($categories->getUrlRange(max(1, $categories->currentPage() - 2), min($categories->lastPage(), $categories->currentPage() + 2)) as $page => $url)
                            <a href="{{ $url }}" class="cl-page-btn {{ $page === $categories->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                        @endforeach

                        @if($categories->hasMorePages())
                            <a href="{{ $categories->nextPageUrl() }}" class="cl-page-btn"><i class="bi bi-chevron-right"></i></a>
                        @else
                            <button class="cl-page-btn" disabled><i class="bi bi-chevron-right"></i></button>
                        @endif
                    </nav>
                </div>
            @endif
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="status-modal-icon danger">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <h5 class="cl-modal-heading">Kategori Sil</h5>
                    <p class="cl-modal-body-text"><strong id="deleteItemName"></strong> kategorisini silmek istediğinize emin misiniz?</p>
                    <p class="cl-modal-warning d-none" id="deleteWarning"><i class="bi bi-exclamation-circle me-1"></i>Bu kategoride içerikler bulunuyor, silinemez.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn-glass" data-bs-dismiss="modal">Vazgeç</button>
                        <form id="deleteForm" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-teal btn-danger-gradient" id="deleteBtn">
                                <i class="bi bi-trash me-1"></i>Sil
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
function openDeleteModal(id, name, postCount) {
    document.getElementById('deleteItemName').textContent = name;
    document.getElementById('deleteForm').action = '/admin/categories/' + id;

    var warning = document.getElementById('deleteWarning');
    var deleteBtn = document.getElementById('deleteBtn');

    if (postCount > 0) {
        warning.classList.remove('d-none');
        deleteBtn.disabled = true;
    } else {
        warning.classList.add('d-none');
        deleteBtn.disabled = false;
    }

    var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush
