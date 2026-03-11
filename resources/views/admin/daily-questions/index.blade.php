@extends('layouts.admin')

@section('title', 'Günün Sorusu Yönetimi — Admin')

@section('content')

    <x-admin.page-header title="Günün Sorusu" subtitle="Anasayfadaki günün sorusunu yönetin">
        <a href="{{ route('admin.daily-questions.create') }}" class="btn-teal">
            <i class="bi bi-plus-lg me-1"></i> Yeni Soru
        </a>
    </x-admin.page-header>

    <!-- Stats -->
    <div class="row g-4 mb-4">
        <x-admin.stat-card color="blue" icon="bi-question-circle-fill" label="Toplam" :count="$stats['total']" :delay="0" colClass="col-xxl col-xl-4 col-sm-6" />
        <x-admin.stat-card color="green" icon="bi-check-circle-fill" label="Yayında" :count="$stats['published']" :delay="100" colClass="col-xxl col-xl-4 col-sm-6" />
        <x-admin.stat-card color="yellow" icon="bi-pencil-fill" label="Taslak" :count="$stats['draft']" :delay="200" colClass="col-xxl col-xl-4 col-sm-6" />
        <x-admin.stat-card color="red" icon="bi-archive-fill" label="Arşiv" :count="$stats['archived']" :delay="300" colClass="col-xxl col-xl-4 col-sm-6" />
        <x-admin.stat-card color="purple" icon="bi-chat-text-fill" label="Toplam Cevap" :count="$stats['total_answers']" :delay="400" colClass="col-xxl col-xl-4 col-sm-6" />
    </div>

    <!-- Table -->
    <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="200">
        <div class="card-body-custom p-0">
            <div class="table-responsive">
                <table class="table table-hover cl-table mb-0">
                    <thead>
                        <tr>
                            <th>Soru</th>
                            <th class="d-none d-md-table-cell">Yayın Tarihi</th>
                            <th class="d-none d-md-table-cell">Cevap</th>
                            <th>Durum</th>
                            <th class="cl-th-actions">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($questions as $q)
                            <tr data-aos="fade-right" data-aos-delay="{{ $loop->index * 50 }}">
                                <td data-label="Soru">
                                    <div class="cl-content-cell">
                                        <div class="cl-content-info">
                                            <span class="cl-content-title">{{ Str::limit($q->question_text, 60) }}</span>
                                            <span class="cl-content-meta d-md-none">
                                                <i class="bi bi-calendar me-1"></i>{{ $q->published_at->format('d.m.Y') }}
                                                <span class="cl-separator">|</span>
                                                <i class="bi bi-chat-text me-1"></i>{{ $q->answers_count }} cevap
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="Yayın Tarihi" class="d-none d-md-table-cell">
                                    <span class="usr-meta">
                                        <i class="bi bi-calendar me-1"></i>{{ $q->published_at->format('d.m.Y') }}
                                    </span>
                                </td>
                                <td data-label="Cevap" class="d-none d-md-table-cell">
                                    <div class="cl-views">
                                        <i class="bi bi-chat-text me-1"></i> {{ $q->answers_count }}
                                    </div>
                                </td>
                                <td data-label="Durum">
                                    @if($q->status === 'published')
                                        <span class="usr-status-badge active">Yayında</span>
                                    @elseif($q->status === 'draft')
                                        <span class="usr-status-badge pending">Taslak</span>
                                    @else
                                        <span class="usr-status-badge inactive">Arşiv</span>
                                    @endif
                                </td>
                                <td data-label="İşlem">
                                    <div class="usr-actions">
                                        <a class="usr-action-btn poll-action-chart" title="Cevaplar" href="{{ route('admin.daily-questions.answers', $q) }}">
                                            <i class="bi bi-chat-text-fill"></i>
                                        </a>
                                        <a class="usr-action-btn poll-action-edit" title="Düzenle" href="{{ route('admin.daily-questions.edit', $q) }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <button class="usr-action-btn danger" title="Sil" onclick="openDeleteModal({{ $q->id }}, '{{ addslashes(Str::limit($q->question_text, 40)) }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-admin.table-empty :colspan="5" icon="bi-question-circle" message="Henüz günün sorusu oluşturulmamış." />
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
                    <h5 class="cl-modal-heading">Soruyu Sil</h5>
                    <p class="cl-modal-body-text"><strong id="deleteItemName"></strong> sorusunu silmek istediğinize emin misiniz?</p>
                    <p class="cl-modal-warning"><i class="bi bi-exclamation-circle me-1"></i>Bu işlem geri alınamaz.</p>
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
    document.getElementById('deleteForm').action = '/admin/daily-questions/' + id;
    var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush
