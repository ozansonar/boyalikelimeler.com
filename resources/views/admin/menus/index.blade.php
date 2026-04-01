@extends('layouts.admin')

@section('title', 'Menü Yönetimi — Admin')

@section('content')

    <x-admin.page-header title="Menü Yönetimi" subtitle="Navbar, footer ve diğer menüleri yönetin">
        <a href="{{ route('admin.menus.create') }}" class="btn-teal">
            <i class="bi bi-plus-lg"></i> Yeni Menü
        </a>
    </x-admin.page-header>

    <!-- Stats -->
    <div class="row g-4 mb-4">
        <x-admin.stat-card color="blue" icon="bi-list-nested" label="Toplam Menü" :count="$stats['total_menus']" :delay="0" col-class="col-xxl-4 col-sm-6" />
        <x-admin.stat-card color="teal" icon="bi-link-45deg" label="Toplam Öğe" :count="$stats['total_items']" :delay="100" col-class="col-xxl-4 col-sm-6" />
        <x-admin.stat-card color="green" icon="bi-check-circle-fill" label="Aktif Öğe" :count="$stats['active_items']" :delay="200" col-class="col-xxl-4 col-sm-6" />
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
                            <button class="btn-glass" title="Sil" onclick="openDeleteModal({{ $menu->id }}, '{{ addslashes($menu->name) }}', '{{ route('admin.menus.destroy', $menu) }}')">
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

    <x-admin.delete-modal message="Bu menüyü ve tüm öğelerini silmek istediğinizden emin misiniz?" />

@endsection

@push('scripts')
<script src="{{ asset('assets/admin/js/menus.js') }}?v={{ filemtime(public_path('assets/admin/js/menus.js')) }}"></script>
@endpush
