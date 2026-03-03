@extends('layouts.front')

@section('title', 'Yazılarım — Boyalı Kelimeler')
@section('meta_description', 'Boyalı Kelimeler\'de gönderdiğiniz yazılarınızı görüntüleyin ve yönetin.')
@section('canonical', route('myposts.index'))

@section('content')

    <section class="wpost-section" aria-label="Yazılarım">
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
                        <i class="fa-solid fa-file-lines me-2"></i>Yazılarım
                    </h1>
                </div>
                <div class="wpost-header__actions">
                    <a href="{{ route('myposts.create') }}" class="wpost-btn wpost-btn--primary">
                        <i class="fa-solid fa-feather-pointed me-1"></i>Yeni Yazı Gönder
                    </a>
                </div>
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
                </div>
            @endif

            {{-- Stats Summary --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="myposts-stat">
                        <div class="myposts-stat__icon">
                            <i class="fa-solid fa-file-lines"></i>
                        </div>
                        <div class="myposts-stat__info">
                            <span class="myposts-stat__number">{{ $stats['total'] }}</span>
                            <span class="myposts-stat__label">Toplam Yazı</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="myposts-stat">
                        <div class="myposts-stat__icon myposts-stat__icon--success">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <div class="myposts-stat__info">
                            <span class="myposts-stat__number">{{ $stats['published'] }}</span>
                            <span class="myposts-stat__label">Yayında</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="myposts-stat">
                        <div class="myposts-stat__icon myposts-stat__icon--warning">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div class="myposts-stat__info">
                            <span class="myposts-stat__number">{{ $stats['draft'] }}</span>
                            <span class="myposts-stat__label">Taslak</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="myposts-stat">
                        <div class="myposts-stat__icon myposts-stat__icon--danger">
                            <i class="fa-solid fa-archive"></i>
                        </div>
                        <div class="myposts-stat__info">
                            <span class="myposts-stat__number">{{ $stats['archived'] }}</span>
                            <span class="myposts-stat__label">Arşiv</span>
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
                               placeholder="Yazılarınızda arayın...">
                    </div>
                    <div class="myposts-toolbar__filter">
                        <select class="wpost-form__input myposts-toolbar__select" id="statusFilter" name="status" onchange="this.form.submit()">
                            <option value="">Tüm Durumlar</option>
                            <option value="published" @selected(request('status') === 'published')>Yayında</option>
                            <option value="draft" @selected(request('status') === 'draft')>Taslak</option>
                            <option value="scheduled" @selected(request('status') === 'scheduled')>Zamanlanmış</option>
                            <option value="archived" @selected(request('status') === 'archived')>Arşiv</option>
                        </select>
                    </div>
                </form>

                {{-- Table --}}
                <div class="myposts-table-wrap">
                    <table class="myposts-table" id="postsTable">
                        <thead>
                            <tr>
                                <th class="myposts-table__th myposts-table__th--title">Yazı Başlığı</th>
                                <th class="myposts-table__th myposts-table__th--category">Kategori</th>
                                <th class="myposts-table__th myposts-table__th--date">Tarih</th>
                                <th class="myposts-table__th myposts-table__th--status">Durum</th>
                                <th class="myposts-table__th myposts-table__th--views">Görüntülenme</th>
                                <th class="myposts-table__th myposts-table__th--actions">İşlem</th>
                            </tr>
                        </thead>
                        <tbody id="postsTableBody">
                            @forelse($posts as $post)
                                <tr class="myposts-table__row" data-status="{{ $post->status->value }}">
                                    <td class="myposts-table__td">
                                        @if($post->status === \App\Enums\PostStatus::Published)
                                            <a href="{{ route('blog.show', $post->slug) }}" class="myposts-table__title-link">{{ $post->title }}</a>
                                        @else
                                            <span class="myposts-table__title-link">{{ $post->title }}</span>
                                        @endif
                                    </td>
                                    <td class="myposts-table__td">
                                        <span class="myposts-badge myposts-badge--category">{{ $post->category?->name ?? '—' }}</span>
                                    </td>
                                    <td class="myposts-table__td myposts-table__td--muted">{{ $post->created_at->translatedFormat('d M Y') }}</td>
                                    <td class="myposts-table__td">
                                        @switch($post->status)
                                            @case(\App\Enums\PostStatus::Published)
                                                <span class="myposts-badge myposts-badge--published">
                                                    <i class="fa-solid fa-circle me-1"></i>Yayında
                                                </span>
                                                @break
                                            @case(\App\Enums\PostStatus::Draft)
                                                <span class="myposts-badge myposts-badge--pending">
                                                    <i class="fa-solid fa-circle me-1"></i>Taslak
                                                </span>
                                                @break
                                            @case(\App\Enums\PostStatus::Scheduled)
                                                <span class="myposts-badge myposts-badge--pending">
                                                    <i class="fa-solid fa-circle me-1"></i>Zamanlanmış
                                                </span>
                                                @break
                                            @case(\App\Enums\PostStatus::Archived)
                                                <span class="myposts-badge myposts-badge--rejected">
                                                    <i class="fa-solid fa-circle me-1"></i>Arşiv
                                                </span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="myposts-table__td myposts-table__td--muted">
                                        @if($post->status === \App\Enums\PostStatus::Published)
                                            <i class="fa-solid fa-eye me-1"></i>{{ number_format($post->view_count) }}
                                        @else
                                            <i class="fa-solid fa-eye me-1"></i>—
                                        @endif
                                    </td>
                                    <td class="myposts-table__td">
                                        <div class="myposts-table__actions">
                                            @if($post->status === \App\Enums\PostStatus::Published)
                                                <a href="{{ route('blog.show', $post->slug) }}" class="myposts-action-btn myposts-action-btn--view" title="Görüntüle">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('myposts.edit', $post) }}" class="myposts-action-btn myposts-action-btn--edit" title="Düzenle">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <button type="button"
                                                    class="myposts-action-btn myposts-action-btn--delete"
                                                    title="Sil"
                                                    onclick="openDeleteModal({{ $post->id }}, '{{ addslashes($post->title) }}')">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="myposts-table__td text-center py-5">
                                        <i class="fa-solid fa-feather-pointed fa-2x mb-3 d-block" aria-hidden="true"></i>
                                        Henüz yazınız bulunmuyor. İlk yazınızı gönderin!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Table Footer: Info + Pagination --}}
                @if($posts->hasPages())
                    <div class="myposts-table-footer">
                        <p class="myposts-table-footer__info">
                            Toplam <strong>{{ $posts->total() }}</strong> yazıdan
                            <strong>{{ $posts->firstItem() }}-{{ $posts->lastItem() }}</strong> arası gösteriliyor
                        </p>
                        <div class="myposts-pagination">
                            @if($posts->onFirstPage())
                                <button class="myposts-pagination__btn" disabled aria-label="Önceki sayfa">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </button>
                            @else
                                <a href="{{ $posts->previousPageUrl() }}" class="myposts-pagination__btn" aria-label="Önceki sayfa">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </a>
                            @endif

                            <div class="myposts-pagination__pages">
                                @foreach($posts->getUrlRange(1, $posts->lastPage()) as $page => $url)
                                    @if($page == $posts->currentPage())
                                        <span class="myposts-pagination__page myposts-pagination__page--active">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}" class="myposts-pagination__page">{{ $page }}</a>
                                    @endif
                                @endforeach
                            </div>

                            @if($posts->hasMorePages())
                                <a href="{{ $posts->nextPageUrl() }}" class="myposts-pagination__btn" aria-label="Sonraki sayfa">
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
            <div class="modal-content" style="background: var(--color-black-card); border: 1px solid rgba(255,255,255,.08);">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="fa-solid fa-triangle-exclamation me-2 text-danger"></i>Yazıyı Sil
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
                </div>
                <div class="modal-body">
                    <p><strong id="deleteItemName"></strong> yazısını silmek istediğinize emin misiniz?</p>
                    <p class="text-muted small">Bu işlem geri alınamaz.</p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">İptal</button>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="fa-solid fa-trash-can me-1"></i>Sil
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('js/myposts.js') }}"></script>
@endpush
