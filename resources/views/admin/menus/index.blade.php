@extends('layouts.admin')

@section('title', 'Menü Yönetimi — Admin')

@section('content')

    <!-- Page Header -->
    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3" data-aos="fade-down">
        <div>
            <h1 class="page-title">Menü Yönetimi</h1>
            <p class="page-subtitle">Navbar, footer ve diğer menüleri yönetin</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.menus.create') }}" class="btn-teal">
                <i class="bi bi-plus-lg"></i> Yeni Menü
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-4 mb-4">
        <div class="col-xxl-4 col-sm-6" data-aos="fade-up" data-aos-delay="0">
            <div class="usr-stat-card">
                <div class="usr-stat-icon usr-stat-icon-blue">
                    <i class="bi bi-list-nested"></i>
                </div>
                <div class="usr-stat-info">
                    <span class="usr-stat-label">Toplam Menü</span>
                    <h3 class="usr-stat-value" data-count="{{ $stats['total_menus'] }}">0</h3>
                </div>
            </div>
        </div>
        <div class="col-xxl-4 col-sm-6" data-aos="fade-up" data-aos-delay="100">
            <div class="usr-stat-card">
                <div class="usr-stat-icon usr-stat-icon-teal">
                    <i class="bi bi-link-45deg"></i>
                </div>
                <div class="usr-stat-info">
                    <span class="usr-stat-label">Toplam Öğe</span>
                    <h3 class="usr-stat-value" data-count="{{ $stats['total_items'] }}">0</h3>
                </div>
            </div>
        </div>
        <div class="col-xxl-4 col-sm-6" data-aos="fade-up" data-aos-delay="200">
            <div class="usr-stat-card">
                <div class="usr-stat-icon usr-stat-icon-green">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="usr-stat-info">
                    <span class="usr-stat-label">Aktif Öğe</span>
                    <h3 class="usr-stat-value" data-count="{{ $stats['active_items'] }}">0</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Cards -->
    <div class="row g-4">
        @forelse($menus as $menu)
            <div class="col-lg-6 col-xl-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                <div class="card-dark h-100">
                    <div class="card-header-custom d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="bi bi-list-nested me-2 text-teal"></i>{{ $menu->name }}
                        </h6>
                        @if($menu->is_active)
                            <span class="usr-status-badge badge-active">Aktif</span>
                        @else
                            <span class="usr-status-badge badge-passive">Pasif</span>
                        @endif
                    </div>
                    <div class="card-body-custom">
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Konum Kodu</small>
                            <code class="text-teal">{{ $menu->location }}</code>
                        </div>
                        @if($menu->description)
                            <p class="text-muted small mb-3">{{ $menu->description }}</p>
                        @endif
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="text-muted small">
                                <i class="bi bi-link-45deg me-1"></i>{{ $menu->items_count }} öğe
                            </span>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.menus.items', $menu) }}" class="btn-teal flex-grow-1 text-center">
                                <i class="bi bi-gear me-1"></i>Öğeleri Yönet
                            </a>
                            <a href="{{ route('admin.menus.edit', $menu) }}" class="btn-glass" title="Düzenle">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button class="btn-glass" title="Sil" onclick="openDeleteModal({{ $menu->id }}, '{{ addslashes($menu->name) }}')">
                                <i class="bi bi-trash text-danger"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12" data-aos="fade-up">
                <div class="card-dark">
                    <div class="card-body-custom text-center py-5">
                        <i class="bi bi-list-nested fs-1 d-block mb-2 opacity-50"></i>
                        <p class="text-muted mb-3">Henüz menü oluşturulmamış.</p>
                        <a href="{{ route('admin.menus.create') }}" class="btn-teal">
                            <i class="bi bi-plus-lg me-1"></i>İlk Menüyü Oluştur
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="status-modal-icon danger">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <h5 class="cl-modal-heading">Silme Onayı</h5>
                    <p class="cl-modal-body-text">Bu menüyü ve tüm öğelerini silmek istediğinizden emin misiniz?</p>
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
<script src="{{ asset('assets/admin/js/menus.js') }}"></script>
@endpush
