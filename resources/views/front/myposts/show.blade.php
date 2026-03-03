@extends('layouts.front')

@section('title', $work->title . ' — Eserlerim — Boyalı Kelimeler')
@section('meta_description', Str::limit(strip_tags($work->body), 160))
@section('canonical', route('myposts.show', $work))

@section('content')

    <!-- Breadcrumb -->
    <nav class="cdetail-breadcrumb" aria-label="Breadcrumb">
        <div class="container">
            <ol class="cdetail-breadcrumb__list">
                <li class="cdetail-breadcrumb__item">
                    <a href="{{ url('/') }}" class="cdetail-breadcrumb__link">
                        <i class="fa-solid fa-house"></i>
                    </a>
                </li>
                <li class="cdetail-breadcrumb__sep">
                    <i class="fa-solid fa-chevron-right"></i>
                </li>
                <li class="cdetail-breadcrumb__item">
                    <a href="{{ route('myposts.index') }}" class="cdetail-breadcrumb__link">Eserlerim</a>
                </li>
                <li class="cdetail-breadcrumb__sep">
                    <i class="fa-solid fa-chevron-right"></i>
                </li>
                <li class="cdetail-breadcrumb__item cdetail-breadcrumb__item--active" aria-current="page">
                    {{ Str::limit($work->title, 50) }}
                </li>
            </ol>
        </div>
    </nav>

    <!-- Article Section -->
    <article class="cdetail-section">
        <div class="container">
            <div class="row g-4">

                <!-- =============================================
                     SOL KOLON — ANA İÇERİK
                ============================================== -->
                <div class="col-lg-8">

                    <!-- Article Header -->
                    <header class="cdetail-header">
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-3">
                            <span class="clist-card__category clist-card__category--{{ $work->category->slug }} cdetail-header__category">
                                <i class="fa-solid fa-tag me-1"></i>{{ $work->category->name }}
                            </span>
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
                        </div>

                        <h1 class="cdetail-header__title">
                            {{ $work->title }}
                        </h1>

                        <div class="cdetail-header__meta">
                            <time class="cdetail-header__date" datetime="{{ $work->created_at->toDateString() }}">
                                <i class="fa-regular fa-calendar me-1"></i>{{ $work->created_at->translatedFormat('d F Y') }}
                            </time>
                            @if($work->status === \App\Enums\LiteraryWorkStatus::Approved)
                                <span class="cdetail-header__views">
                                    <i class="fa-solid fa-eye me-1"></i>{{ number_format($work->view_count) }} okunma
                                </span>
                            @endif
                        </div>
                    </header>

                    <!-- Revision Notes -->
                    @if($work->status === \App\Enums\LiteraryWorkStatus::RevisionRequested)
                        @php $latestRevision = $work->revisions->first(); @endphp
                        @if($latestRevision)
                            <div class="myposts-revision-note mb-4">
                                <i class="fa-solid fa-triangle-exclamation me-2 text-warning"></i>
                                <div>
                                    <strong>Editör Notu:</strong>
                                    <p class="mb-0 mt-1">{{ $latestRevision->reason }}</p>
                                    <small class="text-muted">
                                        {{ $latestRevision->created_at->translatedFormat('d M Y, H:i') }}
                                        @if($latestRevision->admin)
                                            — {{ $latestRevision->admin->name }}
                                        @endif
                                    </small>
                                </div>
                            </div>
                        @endif
                    @endif

                    <!-- Cover Image -->
                    @if($work->cover_image)
                        <div class="cdetail-cover">
                            <img src="{{ upload_url($work->cover_image) }}"
                                 alt="{{ $work->title }}"
                                 class="cdetail-cover__img img-fluid"
                                 loading="lazy">
                        </div>
                    @endif

                    <!-- Article Content -->
                    <div class="cdetail-content">
                        {!! $work->body !!}
                    </div>

                    <!-- Action Buttons -->
                    <div class="cdetail-actions">
                        <div class="cdetail-actions__left">
                            @if($work->status === \App\Enums\LiteraryWorkStatus::Approved)
                                <a href="{{ route('literary-works.show', $work->slug) }}" class="cdetail-actions__btn" target="_blank">
                                    <i class="fa-solid fa-up-right-from-square me-1"></i>
                                    <span>Yayında Gör</span>
                                </a>
                            @endif
                            @if($work->status !== \App\Enums\LiteraryWorkStatus::Unpublished)
                                @php
                                    $editLabel = match($work->status) {
                                        \App\Enums\LiteraryWorkStatus::Approved => 'Güncelle',
                                        \App\Enums\LiteraryWorkStatus::RevisionRequested => 'Revize Et',
                                        default => 'Düzenle',
                                    };
                                @endphp
                                <a href="{{ route('myposts.edit', $work) }}" class="cdetail-actions__btn">
                                    <i class="fa-solid fa-pen-to-square me-1"></i>
                                    <span>{{ $editLabel }}</span>
                                </a>
                            @endif
                            @if($work->status === \App\Enums\LiteraryWorkStatus::Approved)
                                <button type="button" class="cdetail-actions__btn cdetail-actions__btn--warning" onclick="openUnpublishModal({{ $work->id }}, '{{ addslashes($work->title) }}')">
                                    <i class="fa-solid fa-eye-slash me-1"></i>
                                    <span>Yayından Kaldır</span>
                                </button>
                            @endif
                            @if($work->status === \App\Enums\LiteraryWorkStatus::Unpublished)
                                <form action="{{ route('myposts.republish', $work) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="cdetail-actions__btn cdetail-actions__btn--success">
                                        <i class="fa-solid fa-rotate-right me-1"></i>
                                        <span>Tekrar Yayına Gönder</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                        <div class="cdetail-actions__right">
                            <a href="{{ route('myposts.index') }}" class="cdetail-actions__btn">
                                <i class="fa-solid fa-arrow-left me-1"></i>
                                <span>Eserlerime Dön</span>
                            </a>
                        </div>
                    </div>

                </div>

                <!-- =============================================
                     SAĞ KOLON — SIDEBAR
                ============================================== -->
                <aside class="col-lg-4">
                    <div class="cdetail-sidebar">

                        <!-- Work Info -->
                        <div class="cdetail-sidebar__card">
                            <h4 class="cdetail-sidebar__title">
                                <i class="fa-solid fa-chart-simple me-2"></i>Eser Bilgileri
                            </h4>
                            <div class="cdetail-sidebar__stats">
                                <div class="cdetail-sidebar__stat">
                                    <i class="fa-solid fa-tag"></i>
                                    <div class="cdetail-sidebar__stat-info">
                                        <span class="cdetail-sidebar__stat-number">{{ $work->category->name }}</span>
                                        <span class="cdetail-sidebar__stat-label">Kategori</span>
                                    </div>
                                </div>
                                <div class="cdetail-sidebar__stat">
                                    <i class="fa-solid fa-calendar"></i>
                                    <div class="cdetail-sidebar__stat-info">
                                        <span class="cdetail-sidebar__stat-number">{{ $work->created_at->translatedFormat('d M Y') }}</span>
                                        <span class="cdetail-sidebar__stat-label">Gönderim Tarihi</span>
                                    </div>
                                </div>
                                @if($work->status === \App\Enums\LiteraryWorkStatus::Approved && $work->published_at)
                                    <div class="cdetail-sidebar__stat">
                                        <i class="fa-solid fa-calendar-check"></i>
                                        <div class="cdetail-sidebar__stat-info">
                                            <span class="cdetail-sidebar__stat-number">{{ $work->published_at->translatedFormat('d M Y') }}</span>
                                            <span class="cdetail-sidebar__stat-label">Yayın Tarihi</span>
                                        </div>
                                    </div>
                                    <div class="cdetail-sidebar__stat">
                                        <i class="fa-solid fa-eye"></i>
                                        <div class="cdetail-sidebar__stat-info">
                                            <span class="cdetail-sidebar__stat-number">{{ number_format($work->view_count) }}</span>
                                            <span class="cdetail-sidebar__stat-label">Okunma</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Revision History -->
                        @if($work->revisions->isNotEmpty())
                            <div class="cdetail-sidebar__card">
                                <h4 class="cdetail-sidebar__title">
                                    <i class="fa-solid fa-clock-rotate-left me-2"></i>Revizyon Geçmişi
                                </h4>
                                <div class="cdetail-sidebar__related">
                                    @foreach($work->revisions as $revision)
                                        <div class="cdetail-sidebar__related-item">
                                            <div class="cdetail-sidebar__related-thumb">
                                                <i class="fa-solid fa-{{ $revision->is_resolved ? 'check' : 'clock' }}"></i>
                                            </div>
                                            <div class="cdetail-sidebar__related-info">
                                                <h5 class="cdetail-sidebar__related-title">{{ Str::limit($revision->reason, 60) }}</h5>
                                                <span class="cdetail-sidebar__related-meta">
                                                    @if($revision->admin)
                                                        {{ $revision->admin->name }}
                                                        <span class="mx-1">·</span>
                                                    @endif
                                                    {{ $revision->created_at->translatedFormat('d M Y') }}
                                                    @if($revision->is_resolved)
                                                        <span class="mx-1">·</span>
                                                        <span class="text-success"><i class="fa-solid fa-check me-1"></i>Çözüldü</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Excerpt -->
                        @if($work->excerpt)
                            <div class="cdetail-sidebar__card">
                                <h4 class="cdetail-sidebar__title">
                                    <i class="fa-solid fa-quote-left me-2"></i>Özet
                                </h4>
                                <p class="cdetail-sidebar__author-role">{{ $work->excerpt }}</p>
                            </div>
                        @endif

                    </div>
                </aside>

            </div>
        </div>
    </article>

    {{-- Unpublish Confirmation Modal (shared partial) --}}
    @if($work->status === \App\Enums\LiteraryWorkStatus::Approved)
        @include('front.myposts._unpublish-modal')
    @endif

@endsection

@push('scripts')
    <script src="{{ asset('js/myposts.js') }}"></script>
@endpush
