@extends('layouts.admin')

@section('title', 'Ana Sayfa Slider — Admin')

@section('content')

    <x-admin.page-header title="Ana Sayfa Slider" subtitle="Ana sayfadaki slider öğelerini yönetin, sıralayın ve düzenleyin">
        <a href="{{ route('admin.home-sliders.create') }}" class="btn-teal">
            <i class="bi bi-plus-lg me-1"></i> Yeni Slide
        </a>
    </x-admin.page-header>

    <!-- Table -->
    <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="100">
        <div class="card-body-custom p-0">
            <div class="table-responsive">
                <table class="table table-hover cl-table mb-0">
                    <thead>
                        <tr>
                            <th>Sıra</th>
                            <th>Rozet</th>
                            <th>Başlık</th>
                            <th class="d-none d-md-table-cell">Açıklama</th>
                            <th>Durum</th>
                            <th class="cl-th-actions">İşlem</th>
                        </tr>
                    </thead>
                    <tbody id="sliderTableBody">
                        @forelse($sliders as $slider)
                            <tr data-id="{{ $slider->id }}">
                                <td>
                                    <span class="usr-meta">{{ $slider->sort_order }}</span>
                                </td>
                                <td>
                                    <span class="d-flex align-items-center gap-2">
                                        @if($slider->badge_icon)
                                            <i class="{{ $slider->badge_icon }} text-teal"></i>
                                        @endif
                                        <span class="fw-semibold">{{ $slider->badge_text }}</span>
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ $slider->title }}</span>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <span class="usr-meta">{{ Str::limit($slider->description, 60) }}</span>
                                </td>
                                <td>
                                    @if($slider->is_active)
                                        <span class="usr-status-badge active">Aktif</span>
                                    @else
                                        <span class="usr-status-badge inactive">Pasif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="usr-actions">
                                        <a class="usr-action-btn" title="Düzenle" href="{{ route('admin.home-sliders.edit', $slider) }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button class="usr-action-btn danger" title="Sil" onclick="openDeleteModal({{ $slider->id }}, '{{ addslashes($slider->title) }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-admin.table-empty :colspan="6" icon="bi-sliders" message="Henüz slider öğesi oluşturulmamış." />
                        @endforelse
                    </tbody>
                </table>
            </div>
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
                    <h5 class="cl-modal-heading">Slide Sil</h5>
                    <p class="cl-modal-body-text"><strong id="deleteItemName"></strong> slide'ını silmek istediğinize emin misiniz?</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn-glass" data-bs-dismiss="modal">Vazgeç</button>
                        <form id="deleteForm" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-teal btn-danger-gradient">
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
function openDeleteModal(id, name) {
    document.getElementById('deleteItemName').textContent = name;
    document.getElementById('deleteForm').action = '/admin/home-sliders/' + id;
    var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush
