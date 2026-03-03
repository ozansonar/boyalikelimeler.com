@extends('layouts.front')

@section('title', 'Eserlerim — Boyalı Kelimeler')
@section('meta_description', 'Boyalı Kelimeler\'de gönderdiğiniz edebiyat eserlerinizi görüntüleyin ve yönetin.')
@section('canonical', route('myposts.index'))

@section('content')

    <section class="wpost-section" aria-label="Eserlerim">
        <div class="container">

            {{-- Page Header --}}
            <div class="wpost-header">
                <div class="wpost-header__left">
                    @if(auth()->user()->username)
                        <a href="{{ route('profile.show', auth()->user()->username) }}" class="wpost-header__back">
                            <i class="fa-solid fa-arrow-left me-2"></i>Profile Dön
                        </a>
                    @endif
                    <h1 class="wpost-header__title">
                        <i class="fa-solid fa-feather-pointed me-2"></i>Eserlerim
                    </h1>
                </div>
                <div class="wpost-header__actions">
                    <a href="{{ route('myposts.create') }}" class="wpost-btn wpost-btn--primary">
                        <i class="fa-solid fa-feather-pointed me-1"></i>Yeni Eser Gönder
                    </a>
                </div>
            </div>

            {{-- Stats Summary --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="myposts-stat">
                        <div class="myposts-stat__icon">
                            <i class="fa-solid fa-file-lines"></i>
                        </div>
                        <div class="myposts-stat__info">
                            <span class="myposts-stat__number">{{ $stats['total'] }}</span>
                            <span class="myposts-stat__label">Toplam Eser</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="myposts-stat">
                        <div class="myposts-stat__icon myposts-stat__icon--warning">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div class="myposts-stat__info">
                            <span class="myposts-stat__number">{{ $stats['pending'] }}</span>
                            <span class="myposts-stat__label">Beklemede</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="myposts-stat">
                        <div class="myposts-stat__icon myposts-stat__icon--success">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <div class="myposts-stat__info">
                            <span class="myposts-stat__number">{{ $stats['approved'] }}</span>
                            <span class="myposts-stat__label">Onaylandı</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="myposts-stat">
                        <div class="myposts-stat__icon myposts-stat__icon--danger">
                            <i class="fa-solid fa-rotate"></i>
                        </div>
                        <div class="myposts-stat__info">
                            <span class="myposts-stat__number">{{ $stats['revision_requested'] }}</span>
                            <span class="myposts-stat__label">Revize Bekliyor</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Table Card --}}
            <div class="wpost-card">

                {{-- Table Toolbar --}}
                <form action="{{ route('myposts.index') }}" method="GET" class="myposts-toolbar">
                    <div class="myposts-toolbar__search">
                        <i class="fa-solid fa-magnifying-glass myposts-toolbar__search-icon"></i>
                        <input type="text"
                               class="wpost-form__input myposts-toolbar__search-input"
                               id="postSearch"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Eserlerinizde arayın...">
                    </div>
                    <div class="myposts-toolbar__filter">
                        <select class="wpost-form__input myposts-toolbar__select" id="statusFilter" name="status" onchange="this.form.submit()">
                            <option value="">Tüm Durumlar</option>
                            <option value="pending" @selected(request('status') === 'pending')>Beklemede</option>
                            <option value="approved" @selected(request('status') === 'approved')>Onaylandı</option>
                            <option value="rejected" @selected(request('status') === 'rejected')>Reddedildi</option>
                            <option value="revision_requested" @selected(request('status') === 'revision_requested')>Revize Bekliyor</option>
                            <option value="unpublished" @selected(request('status') === 'unpublished')>Yayından Kaldırıldı</option>
                        </select>
                    </div>
                </form>

                {{-- Table --}}
                <div class="myposts-table-wrap">
                    <table class="myposts-table" id="postsTable">
                        <thead>
                            <tr>
                                <th class="myposts-table__th myposts-table__th--title">Eser Başlığı</th>
                                <th class="myposts-table__th myposts-table__th--category">Kategori</th>
                                <th class="myposts-table__th myposts-table__th--date">Tarih</th>
                                <th class="myposts-table__th myposts-table__th--status">Durum</th>
                                <th class="myposts-table__th myposts-table__th--views">Görüntülenme</th>
                                <th class="myposts-table__th myposts-table__th--actions">İşlem</th>
                            </tr>
                        </thead>
                        <tbody id="postsTableBody">
                            @forelse($works as $work)
                                <tr class="myposts-table__row" data-status="{{ $work->status->value }}">
                                    <td class="myposts-table__td">
                                        <a href="{{ route('myposts.show', $work) }}" class="myposts-table__title-link">{{ $work->title }}</a>
                                    </td>
                                    <td class="myposts-table__td">
                                        <span class="myposts-badge myposts-badge--category">{{ $work->category?->name ?? '—' }}</span>
                                    </td>
                                    <td class="myposts-table__td myposts-table__td--muted">{{ $work->created_at->translatedFormat('d M Y') }}</td>
                                    <td class="myposts-table__td">
                                        @switch($work->status)
                                            @case(\App\Enums\LiteraryWorkStatus::Pending)
                                                <span class="myposts-badge myposts-badge--pending">
                                                    <i class="fa-solid fa-circle me-1"></i>Beklemede
                                                </span>
                                                @break
                                            @case(\App\Enums\LiteraryWorkStatus::Approved)
                                                <span class="myposts-badge myposts-badge--published">
                                                    <i class="fa-solid fa-circle me-1"></i>Onaylandı
                                                </span>
                                                @break
                                            @case(\App\Enums\LiteraryWorkStatus::Rejected)
                                                <span class="myposts-badge myposts-badge--rejected">
                                                    <i class="fa-solid fa-circle me-1"></i>Reddedildi
                                                </span>
                                                @break
                                            @case(\App\Enums\LiteraryWorkStatus::RevisionRequested)
                                                <span class="myposts-badge myposts-badge--pending">
                                                    <i class="fa-solid fa-circle me-1"></i>Revize Bekliyor
                                                </span>
                                                @break
                                            @case(\App\Enums\LiteraryWorkStatus::Unpublished)
                                                <span class="myposts-badge myposts-badge--unpublished">
                                                    <i class="fa-solid fa-circle me-1"></i>Yayından Kaldırıldı
                                                </span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="myposts-table__td myposts-table__td--muted">
                                        <i class="fa-solid fa-eye me-1"></i>{{ $work->status === \App\Enums\LiteraryWorkStatus::Approved ? number_format($work->view_count) : '—' }}
                                    </td>
                                    <td class="myposts-table__td">
                                        <div class="myposts-table__actions">
                                            <a href="{{ route('myposts.show', $work) }}" class="myposts-action-btn myposts-action-btn--view" title="Görüntüle">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            @if($work->status !== \App\Enums\LiteraryWorkStatus::Unpublished)
                                                @php
                                                    $editTitle = match($work->status) {
                                                        \App\Enums\LiteraryWorkStatus::Approved => 'Güncelle',
                                                        \App\Enums\LiteraryWorkStatus::RevisionRequested => 'Revize Et',
                                                        default => 'Düzenle',
                                                    };
                                                @endphp
                                                <a href="{{ route('myposts.edit', $work) }}" class="myposts-action-btn myposts-action-btn--edit" title="{{ $editTitle }}">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
                                            @endif
                                            @if($work->status === \App\Enums\LiteraryWorkStatus::Approved)
                                                <button type="button"
                                                        class="myposts-action-btn myposts-action-btn--unpublish"
                                                        title="Yayından Kaldır"
                                                        onclick="openUnpublishModal({{ $work->id }}, '{{ addslashes($work->title) }}')">
                                                    <i class="fa-solid fa-eye-slash"></i>
                                                </button>
                                            @endif
                                            @if($work->status === \App\Enums\LiteraryWorkStatus::Unpublished)
                                                <form action="{{ route('myposts.republish', $work) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="myposts-action-btn myposts-action-btn--republish"
                                                            title="Tekrar Yayına Gönder">
                                                        <i class="fa-solid fa-rotate-right"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <button type="button"
                                                    class="myposts-action-btn myposts-action-btn--delete"
                                                    title="Sil"
                                                    onclick="openDeleteModal({{ $work->id }}, '{{ addslashes($work->title) }}')">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                {{-- Revision note --}}
                                @if($work->status === \App\Enums\LiteraryWorkStatus::RevisionRequested)
                                    @php $latestRevision = $work->revisions->first(); @endphp
                                    @if($latestRevision)
                                        <tr class="myposts-table__row">
                                            <td colspan="6" class="myposts-table__td">
                                                <div class="myposts-revision-note">
                                                    <i class="fa-solid fa-triangle-exclamation me-2 text-warning"></i>
                                                    <strong>Editör notu:</strong> {{ $latestRevision->reason }}
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endif
                            @empty
                                <tr>
                                    <td colspan="6" class="myposts-table__td text-center py-5">
                                        <i class="fa-solid fa-feather-pointed fa-2x mb-3 d-block" aria-hidden="true"></i>
                                        Henüz eseriniz bulunmuyor. İlk eserinizi gönderin!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Table Footer: Info + Pagination --}}
                @if($works->hasPages())
                    <div class="myposts-table-footer">
                        <p class="myposts-table-footer__info">
                            Toplam <strong>{{ $works->total() }}</strong> eserden
                            <strong>{{ $works->firstItem() }}-{{ $works->lastItem() }}</strong> arası gösteriliyor
                        </p>
                        <div class="myposts-pagination">
                            @if($works->onFirstPage())
                                <button class="myposts-pagination__btn" disabled aria-label="Önceki sayfa">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </button>
                            @else
                                <a href="{{ $works->previousPageUrl() }}" class="myposts-pagination__btn" aria-label="Önceki sayfa">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </a>
                            @endif

                            <div class="myposts-pagination__pages">
                                @foreach($works->getUrlRange(1, $works->lastPage()) as $page => $url)
                                    @if($page == $works->currentPage())
                                        <span class="myposts-pagination__page myposts-pagination__page--active">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}" class="myposts-pagination__page">{{ $page }}</a>
                                    @endif
                                @endforeach
                            </div>

                            @if($works->hasMorePages())
                                <a href="{{ $works->nextPageUrl() }}" class="myposts-pagination__btn" aria-label="Sonraki sayfa">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </a>
                            @else
                                <button class="myposts-pagination__btn" disabled aria-label="Sonraki sayfa">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                @endif

            </div>

        </div>
    </section>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content delete-modal">
                <div class="delete-modal__icon-wrap">
                    <div class="delete-modal__icon-ring">
                        <i class="fa-solid fa-trash-can"></i>
                    </div>
                </div>
                <h5 class="delete-modal__title" id="deleteModalLabel">Eseri Silmek İstiyor musunuz?</h5>
                <p class="delete-modal__desc">
                    <strong id="deleteItemName"></strong> başlıklı eseriniz kalıcı olarak silinecektir.
                </p>
                <p class="delete-modal__warning">
                    <i class="fa-solid fa-circle-exclamation me-1"></i>Bu işlem geri alınamaz.
                </p>
                <div class="delete-modal__actions">
                    <button type="button" class="delete-modal__btn delete-modal__btn--cancel" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark me-1"></i>Vazgeç
                    </button>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="delete-modal__btn delete-modal__btn--confirm">
                            <i class="fa-solid fa-trash-can me-1"></i>Evet, Sil
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Unpublish Confirmation Modal (shared partial) --}}
    @include('front.myposts._unpublish-modal')

@endsection

@push('scripts')
    <script src="{{ asset('js/myposts.js') }}"></script>
@endpush
