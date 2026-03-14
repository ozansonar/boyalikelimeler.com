@extends('layouts.admin')

@section('title', $menu->name . ' — Menü Öğeleri')

@section('content')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link"><i class="bi bi-house me-1"></i>Ana Sayfa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.menus.index') }}" class="breadcrumb-link">Menüler</a></li>
            <li class="breadcrumb-item active text-teal">{{ $menu->name }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4" data-aos="fade-down">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.menus.index') }}" class="btn-glass" title="Geri Dön"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h1 class="page-title mb-0">{{ $menu->name }}</h1>
                <p class="page-subtitle mb-0">
                    <code class="text-teal">{{ $menu->location }}</code>
                    @if($menu->description)
                        <span class="mx-1">—</span>{{ $menu->description }}
                    @endif
                </p>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn-teal" data-bs-toggle="modal" data-bs-target="#addItemModal">
                <i class="bi bi-plus-lg me-1"></i>Yeni Öğe Ekle
            </button>
        </div>
    </div>

    <!-- Menu Items List -->
    <div class="card-dark mb-4" data-aos="fade-up">
        <div class="card-header-custom d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="bi bi-list-ul me-2 text-teal"></i>Menü Öğeleri</h6>
            <small class="text-muted"><i class="bi bi-arrows-move me-1"></i>Sıralamak için sürükleyin</small>
        </div>
        <div class="card-body-custom p-0">
            @if($items->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover cl-table mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3" style="width:40px"></th>
                                <th>Başlık</th>
                                <th class="d-none d-md-table-cell">URL</th>
                                <th class="d-none d-lg-table-cell">İkon</th>
                                <th>Durum</th>
                                <th class="cl-th-actions">İşlem</th>
                            </tr>
                        </thead>
                        <tbody id="sortableItems">
                            @foreach($items as $item)
                                <tr data-id="{{ $item->id }}">
                                    <td class="ps-3 sortable-handle" style="cursor:grab">
                                        <i class="bi bi-grip-vertical text-muted"></i>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($item->icon)
                                                <i class="{{ $item->icon }} text-teal"></i>
                                            @endif
                                            <span class="fw-medium">{{ $item->title }}</span>
                                            @if($item->target === App\Enums\LinkTarget::Blank)
                                                <i class="bi bi-box-arrow-up-right text-muted small" title="Yeni sekmede açılır"></i>
                                            @endif
                                        </div>
                                        @if($item->children->isNotEmpty())
                                            <div class="mt-1">
                                                @foreach($item->children as $child)
                                                    <small class="text-muted d-block ms-3">
                                                        <i class="bi bi-arrow-return-right me-1"></i>{{ $child->title }}
                                                        @if(!$child->is_active) <span class="text-danger">(Pasif)</span> @endif
                                                    </small>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <code class="small">{{ Str::limit($item->url, 35) }}</code>
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        @if($item->icon)
                                            <code class="small">{{ $item->icon }}</code>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->is_active)
                                            <span class="usr-status-badge badge-active">Aktif</span>
                                        @else
                                            <span class="usr-status-badge badge-passive">Pasif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="usr-actions">
                                            <button class="usr-action-btn" title="Düzenle" onclick="openEditModal({{ $item->id }}, '{{ addslashes($item->title) }}', '{{ addslashes($item->url) }}', '{{ addslashes($item->icon ?? '') }}', '{{ $item->target }}', {{ $item->is_active ? 'true' : 'false' }}, {{ $item->sort_order }}, {{ $item->parent_id ?? 'null' }})"><i class="bi bi-pencil"></i></button>
                                            <button class="usr-action-btn danger" title="Sil" onclick="openDeleteModal({{ $item->id }}, '{{ addslashes($item->title) }}', '{{ route('admin.menus.items.destroy', [$menu, $item]) }}')"><i class="bi bi-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-link-45deg fs-1 d-block mb-2 opacity-50"></i>
                    <p class="text-muted mb-3">Bu menüde henüz öğe yok.</p>
                    <button class="btn-teal" data-bs-toggle="modal" data-bs-target="#addItemModal">
                        <i class="bi bi-plus-lg me-1"></i>İlk Öğeyi Ekle
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- İkon Referans Kartı -->
    <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="100">
        <div class="card-header-custom">
            <h6 class="mb-0"><i class="bi bi-info-circle me-2 text-teal"></i>İkon Rehberi</h6>
        </div>
        <div class="card-body-custom">
            <p class="text-muted small mb-2">Font Awesome ikon sınıflarını kullanın. Örnekler:</p>
            <div class="d-flex flex-wrap gap-3">
                <code class="small"><i class="fa-solid fa-house me-1"></i>fa-solid fa-house</code>
                <code class="small"><i class="fa-solid fa-book-open me-1"></i>fa-solid fa-book-open</code>
                <code class="small"><i class="fa-solid fa-palette me-1"></i>fa-solid fa-palette</code>
                <code class="small"><i class="fa-solid fa-newspaper me-1"></i>fa-solid fa-newspaper</code>
                <code class="small"><i class="fa-solid fa-paper-plane me-1"></i>fa-solid fa-paper-plane</code>
                <code class="small"><i class="fa-solid fa-comments me-1"></i>fa-solid fa-comments</code>
                <code class="small"><i class="fa-solid fa-envelope me-1"></i>fa-solid fa-envelope</code>
                <code class="small"><i class="fa-solid fa-shield-halved me-1"></i>fa-solid fa-shield-halved</code>
            </div>
        </div>
    </div>

    <!-- ==================== ADD ITEM MODAL ==================== -->
    <div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.menus.items.store', $menu) }}">
                    @csrf
                    <div class="modal-header border-0">
                        <h5 class="modal-title"><i class="bi bi-plus-circle me-2 text-teal"></i>Yeni Menü Öğesi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Başlık <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="title" placeholder="Örn: Ana Sayfa" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">URL <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="url" placeholder="Örn: / veya /blog veya https://..." required>
                                <div class="form-text">Site içi: /hakkimizda — Dış link: https://example.com — Boş: #</div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">İkon</label>
                                <input type="text" class="form-control" name="icon" placeholder="fa-solid fa-house">
                                <div class="form-text">Font Awesome class adı</div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Hedef</label>
                                <select class="form-select" name="target">
                                    @foreach(App\Enums\LinkTarget::cases() as $lt)
                                        <option value="{{ $lt->value }}">{{ $lt->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Üst Öğe</label>
                                <select class="form-select" name="parent_id">
                                    <option value="">— Ana Menü —</option>
                                    @foreach($items as $parentItem)
                                        <option value="{{ $parentItem->id }}">{{ $parentItem->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Sıralama</label>
                                <input type="number" class="form-control" name="sort_order" value="0" min="0" max="999">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Durum</label>
                                <select class="form-select" name="is_active">
                                    <option value="1">Aktif</option>
                                    <option value="0">Pasif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn-glass" data-bs-dismiss="modal">Vazgeç</button>
                        <button type="submit" class="btn-teal"><i class="bi bi-plus-lg me-1"></i>Ekle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ==================== EDIT ITEM MODAL ==================== -->
    <div class="modal fade" id="editItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="editItemForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-0">
                        <h5 class="modal-title"><i class="bi bi-pencil me-2 text-teal"></i>Menü Öğesini Düzenle</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Başlık <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="title" id="editTitle" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">URL <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="url" id="editUrl" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">İkon</label>
                                <input type="text" class="form-control" name="icon" id="editIcon">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Hedef</label>
                                <select class="form-select" name="target" id="editTarget">
                                    @foreach(App\Enums\LinkTarget::cases() as $lt)
                                        <option value="{{ $lt->value }}">{{ $lt->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Üst Öğe</label>
                                <select class="form-select" name="parent_id" id="editParentId">
                                    <option value="">— Ana Menü —</option>
                                    @foreach($items as $parentItem)
                                        <option value="{{ $parentItem->id }}">{{ $parentItem->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Sıralama</label>
                                <input type="number" class="form-control" name="sort_order" id="editSortOrder" min="0" max="999">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Durum</label>
                                <select class="form-select" name="is_active" id="editIsActive">
                                    <option value="1">Aktif</option>
                                    <option value="0">Pasif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn-glass" data-bs-dismiss="modal">Vazgeç</button>
                        <button type="submit" class="btn-teal"><i class="bi bi-check2 me-1"></i>Güncelle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ==================== DELETE ITEM MODAL ==================== -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="status-modal-icon danger">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <h5 class="cl-modal-heading">Silme Onayı</h5>
                    <p class="cl-modal-body-text">Bu öğeyi silmek istediğinizden emin misiniz?</p>
                    <p class="cl-modal-content-name" id="deleteContentTitle"></p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn-glass" data-bs-dismiss="modal">Vazgeç</button>
                        <form id="deleteForm" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-teal btn-danger-gradient">
                                <i class="bi bi-trash me-1"></i>Evet, Sil
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="{{ asset('assets/admin/js/menus.js') }}?v={{ filemtime(public_path('assets/admin/js/menus.js')) }}"></script>
@php
    $menuId = $menu->id;
    $reorderUrl = route('admin.menus.items.reorder', $menu);
    $itemBaseUrl = route('admin.menus.items', $menu);
@endphp
<script>
    var MENU_ID = {{ $menuId }};
    var REORDER_URL = '{{ $reorderUrl }}';
    var ITEM_BASE_URL = '{{ $itemBaseUrl }}';
    var CSRF_TOKEN = '{{ csrf_token() }}';
    initSortable();
</script>
@endpush
