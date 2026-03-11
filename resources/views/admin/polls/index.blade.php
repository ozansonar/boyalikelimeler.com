@extends('layouts.admin')

@section('title', 'Anket Yönetimi — Admin')

@section('content')

    <x-admin.page-header title="Anket Yönetimi" subtitle="Anasayfadaki anketleri yönetin">
        <a href="{{ route('admin.polls.create') }}" class="btn-teal">
            <i class="bi bi-plus-lg me-1"></i> Yeni Anket
        </a>
    </x-admin.page-header>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <x-admin.stat-card color="blue" icon="bi-bar-chart" label="Toplam" :count="$stats['total']" :delay="0" />
        <x-admin.stat-card color="green" icon="bi-check-circle" label="Aktif" :count="$stats['active']" :delay="50" />
        <x-admin.stat-card color="red" icon="bi-x-circle" label="Pasif" :count="$stats['inactive']" :delay="100" />
        <x-admin.stat-card color="purple" icon="bi-hand-index-thumb" label="Toplam Oy" :count="$stats['total_votes']" :delay="150" />
    </div>

    <!-- Table -->
    <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="100">
        <div class="card-body-custom p-0">
            <div class="table-responsive">
                <table class="table table-hover cl-table mb-0">
                    <thead>
                        <tr>
                            <th>Soru</th>
                            <th>Şık</th>
                            <th>Oy</th>
                            <th class="d-none d-md-table-cell">Tarih Aralığı</th>
                            <th>Durum</th>
                            <th class="cl-th-actions">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($polls as $poll)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ Str::limit($poll->question, 50) }}</span>
                                </td>
                                <td>
                                    <span class="usr-meta">{{ $poll->options_count }} şık</span>
                                </td>
                                <td>
                                    <span class="usr-meta">{{ number_format($poll->votes_count) }}</span>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    @if($poll->starts_at || $poll->ends_at)
                                        <small class="usr-meta">
                                            {{ $poll->starts_at?->format('d.m.Y') ?? '—' }}
                                            <i class="bi bi-arrow-right mx-1"></i>
                                            {{ $poll->ends_at?->format('d.m.Y') ?? '—' }}
                                        </small>
                                    @else
                                        <span class="usr-meta">Süresiz</span>
                                    @endif
                                </td>
                                <td>
                                    @if($poll->is_active)
                                        <span class="usr-status-badge active">Aktif</span>
                                    @else
                                        <span class="usr-status-badge inactive">Pasif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="usr-actions">
                                        <a class="usr-action-btn" title="Sonuçlar" href="{{ route('admin.polls.results', $poll) }}">
                                            <i class="bi bi-bar-chart"></i>
                                        </a>
                                        <a class="usr-action-btn" title="Düzenle" href="{{ route('admin.polls.edit', $poll) }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.polls.toggle-active', $poll) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="usr-action-btn" title="{{ $poll->is_active ? 'Pasif Yap' : 'Aktif Yap' }}">
                                                <i class="bi {{ $poll->is_active ? 'bi-toggle-on text-success' : 'bi-toggle-off' }}"></i>
                                            </button>
                                        </form>
                                        <button class="usr-action-btn danger" title="Sil" onclick="openDeleteModal({{ $poll->id }}, '{{ addslashes(Str::limit($poll->question, 40)) }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-admin.table-empty :colspan="6" icon="bi-bar-chart" message="Henüz anket oluşturulmamış." />
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
                    <h5 class="cl-modal-heading">Anket Sil</h5>
                    <p class="cl-modal-body-text"><strong id="deleteItemName"></strong> anketini silmek istediğinize emin misiniz?</p>
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
    document.getElementById('deleteForm').action = '/admin/polls/' + id;
    var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush
