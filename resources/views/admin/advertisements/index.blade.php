@extends('layouts.admin')

@section('title', 'Reklam Yönetimi — Admin')

@section('content')

    <x-admin.page-header title="Reklam Yönetimi" subtitle="Ana sayfadaki reklam alanlarını yönetin">
        <a href="{{ route('admin.advertisements.create') }}" class="btn-teal">
            <i class="bi bi-plus-lg me-1"></i> Yeni Reklam
        </a>
    </x-admin.page-header>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <x-admin.stat-card color="blue" icon="bi-megaphone" label="Toplam" :count="$stats['total']" :delay="0" />
        <x-admin.stat-card color="green" icon="bi-check-circle" label="Aktif" :count="$stats['active']" :delay="50" />
        <x-admin.stat-card color="red" icon="bi-x-circle" label="Pasif" :count="$stats['inactive']" :delay="100" />
        <x-admin.stat-card color="purple" icon="bi-hand-index-thumb" label="Tıklama" :count="$stats['clicks']" :delay="150" />
    </div>

    <!-- Table -->
    <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="100">
        <div class="card-body-custom p-0">
            <div class="table-responsive">
                <table class="table table-hover cl-table mb-0">
                    <thead>
                        <tr>
                            <th>Görsel</th>
                            <th>Başlık</th>
                            <th>Pozisyon</th>
                            <th class="d-none d-md-table-cell">Tarih Aralığı</th>
                            <th class="d-none d-md-table-cell">Tıklama</th>
                            <th>Durum</th>
                            <th class="cl-th-actions">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($advertisements as $ad)
                            <tr>
                                <td>
                                    @if($ad->image)
                                        <img src="{{ upload_url($ad->image, 'thumb') }}" alt="{{ $ad->title }}" class="rounded" style="width:48px;height:48px;object-fit:cover" loading="lazy">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center rounded bg-dark" style="width:48px;height:48px">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ $ad->title }}</span>
                                    @if($ad->link)
                                        <br><small class="text-muted">{{ Str::limit($ad->link, 40) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $ad->position->label() }}</span>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    @if($ad->start_date || $ad->end_date)
                                        <small class="usr-meta">
                                            {{ $ad->start_date?->format('d.m.Y') ?? '—' }}
                                            <i class="bi bi-arrow-right mx-1"></i>
                                            {{ $ad->end_date?->format('d.m.Y') ?? '—' }}
                                        </small>
                                    @else
                                        <span class="usr-meta">Süresiz</span>
                                    @endif
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <span class="usr-meta">{{ number_format($ad->click_count) }}</span>
                                </td>
                                <td>
                                    @if($ad->is_active)
                                        <span class="usr-status-badge active">Aktif</span>
                                    @else
                                        <span class="usr-status-badge inactive">Pasif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="usr-actions">
                                        <a class="usr-action-btn" title="Düzenle" href="{{ route('admin.advertisements.edit', $ad) }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button class="usr-action-btn danger" title="Sil" onclick="openDeleteModal({{ $ad->id }}, '{{ addslashes($ad->title) }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-admin.table-empty :colspan="7" icon="bi-megaphone" message="Henüz reklam oluşturulmamış." />
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
                    <h5 class="cl-modal-heading">Reklam Sil</h5>
                    <p class="cl-modal-body-text"><strong id="deleteItemName"></strong> reklamını silmek istediğinize emin misiniz?</p>
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
    document.getElementById('deleteForm').action = '/admin/advertisements/' + id;
    var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush
