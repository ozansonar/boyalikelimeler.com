@extends('layouts.admin')

@section('title', 'Söz Meydanı Kategorileri — Admin')

@section('content')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3" data-aos="fade-down" data-aos-duration="400">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link"><i class="bi bi-house me-1"></i>Ana Sayfa</a>
            </li>
            <li class="breadcrumb-item active text-teal">Söz Meydanı Kategorileri</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4" data-aos="fade-down">
        <div>
            <h1 class="page-title">Söz Meydanı Kategorileri</h1>
            <p class="page-subtitle">Soru/cevap kategorilerini yönetin</p>
        </div>
        <a href="{{ route('admin.qna.categories.create') }}" class="btn-teal">
            <i class="bi bi-plus-lg me-1"></i>Yeni Kategori
        </a>
    </div>

    <!-- Toolbar -->
    <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="100">
        <div class="card-body-custom">
            <form method="GET" action="{{ route('admin.qna.categories.index') }}" class="cl-toolbar">
                <div class="cl-search">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Kategori adı ile ara...">
                </div>
                <div class="cl-toolbar-actions">
                    <button type="submit" class="btn-glass"><i class="bi bi-funnel me-1"></i>Filtrele</button>
                    @if(!empty($filters['search']))
                        <a href="{{ route('admin.qna.categories.index') }}" class="cl-filter-reset" title="Filtreleri Sıfırla">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
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
                            <th>Kategori</th>
                            <th class="d-none d-md-table-cell">Soru Sayısı</th>
                            <th>Sıra</th>
                            <th>Durum</th>
                            <th class="cl-th-actions">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>
                                    <div class="cl-content-cell">
                                        <div class="cl-icon-box cl-icon-box--{{ str_replace('qna-cat-card__icon-wrap--', '', $category->color_class) }}">
                                            <i class="{{ $category->icon }}"></i>
                                        </div>
                                        <div class="cl-content-info">
                                            <span class="cl-content-title">{{ $category->name }}</span>
                                            <span class="cl-content-meta">{{ $category->slug }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <span class="usr-meta">{{ $category->approved_questions_count ?? 0 }}</span>
                                </td>
                                <td>
                                    <span class="usr-meta">{{ $category->sort_order }}</span>
                                </td>
                                <td>
                                    @if($category->is_active)
                                        <span class="usr-status-badge usr-status-badge-green">Aktif</span>
                                    @else
                                        <span class="usr-status-badge usr-status-badge-orange">Pasif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="usr-actions">
                                        <a class="usr-action-btn" title="Düzenle" href="{{ route('admin.qna.categories.edit', $category->id) }}"><i class="bi bi-pencil"></i></a>
                                        <button class="usr-action-btn danger" title="Sil" onclick="openDeleteModal({{ $category->id }}, '{{ addslashes($category->name) }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-admin.table-empty :colspan="5" icon="bi-folder" message="Henüz kategori oluşturulmamış." />
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($categories->hasPages())
                <div class="cl-pagination-wrapper">
                    <div class="cl-pagination-info">
                        <span>Toplam <strong>{{ number_format($categories->total()) }}</strong> kategoriden <strong>{{ $categories->firstItem() }}-{{ $categories->lastItem() }}</strong> arası gösteriliyor</span>
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

    <!-- Delete Confirm Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="status-modal-icon danger">
                        <i class="bi bi-trash"></i>
                    </div>
                    <h5 class="cl-modal-heading">Kategoriyi Sil</h5>
                    <p class="cl-modal-body-text">
                        <strong id="deleteItemName"></strong> kategorisini silmek istediğinize emin misiniz?
                    </p>
                    <p class="cl-modal-warning"><i class="bi bi-exclamation-triangle me-1"></i>Bu kategorideki tüm sorular da silinecektir.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn-glass" data-bs-dismiss="modal">Vazgeç</button>
                        <button type="button" class="btn-teal btn-danger-gradient" id="deleteBtn"><i class="bi bi-trash me-1"></i>Sil</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="{{ asset('assets/admin/js/qna.js') }}"></script>
@endpush
