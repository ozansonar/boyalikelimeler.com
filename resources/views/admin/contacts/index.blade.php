@extends('layouts.admin')

@section('title', 'Mesajlar — Admin')

@section('content')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3" data-aos="fade-down" data-aos-duration="400">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link"><i class="bi bi-house me-1"></i>Ana Sayfa</a>
            </li>
            <li class="breadcrumb-item active text-teal">Mesajlar</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4" data-aos="fade-down">
        <div>
            <h1 class="page-title">Mesajlar</h1>
            <p class="page-subtitle">İletişim formundan gelen mesajları yönetin</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn-glass" id="markAllReadBtn">
                <i class="bi bi-envelope-open me-1"></i> Tümünü Okundu Yap
            </button>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xxl-3 col-xl-6 col-sm-6" data-aos="fade-up" data-aos-delay="0">
            <div class="usr-stat-card">
                <div class="usr-stat-icon usr-stat-icon-blue">
                    <i class="bi bi-chat-left-text-fill"></i>
                </div>
                <div class="usr-stat-info">
                    <span class="usr-stat-label">Toplam Mesaj</span>
                    <h3 class="usr-stat-value" data-counter="{{ $stats['total'] }}">{{ number_format($stats['total']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-xl-6 col-sm-6" data-aos="fade-up" data-aos-delay="50">
            <div class="usr-stat-card">
                <div class="usr-stat-icon usr-stat-icon-orange">
                    <i class="bi bi-envelope-fill"></i>
                </div>
                <div class="usr-stat-info">
                    <span class="usr-stat-label">Okunmamış</span>
                    <h3 class="usr-stat-value" data-counter="{{ $stats['unread'] }}">{{ number_format($stats['unread']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-xl-6 col-sm-6" data-aos="fade-up" data-aos-delay="100">
            <div class="usr-stat-card">
                <div class="usr-stat-icon usr-stat-icon-green">
                    <i class="bi bi-reply-fill"></i>
                </div>
                <div class="usr-stat-info">
                    <span class="usr-stat-label">Yanıtlanan</span>
                    <h3 class="usr-stat-value" data-counter="{{ $stats['replied'] }}">{{ number_format($stats['replied']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-xl-6 col-sm-6" data-aos="fade-up" data-aos-delay="150">
            <div class="usr-stat-card">
                <div class="usr-stat-icon usr-stat-icon-purple">
                    <i class="bi bi-star-fill"></i>
                </div>
                <div class="usr-stat-info">
                    <span class="usr-stat-label">Yıldızlı</span>
                    <h3 class="usr-stat-value" data-counter="{{ $stats['starred'] }}">{{ number_format($stats['starred']) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Mail Layout -->
    <div class="msg-layout" data-aos="fade-up" data-aos-delay="100">

        <!-- LEFT: Folder Sidebar -->
        <aside class="msg-folders" id="msgFolders">
            <div class="msg-folders-inner">
                <a href="{{ route('admin.contacts.index') }}" class="msg-folder-btn {{ !request('folder') ? 'active' : '' }}">
                    <i class="bi bi-inbox"></i><span>Gelen Kutusu</span>
                    @if($stats['unread'] > 0)
                        <span class="msg-folder-count">{{ $stats['unread'] }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.contacts.index', ['folder' => 'unread']) }}" class="msg-folder-btn {{ request('folder') === 'unread' ? 'active' : '' }}">
                    <i class="bi bi-envelope"></i><span>Okunmamış</span>
                </a>
                <a href="{{ route('admin.contacts.index', ['folder' => 'starred']) }}" class="msg-folder-btn {{ request('folder') === 'starred' ? 'active' : '' }}">
                    <i class="bi bi-star"></i><span>Yıldızlı</span>
                    @if($stats['starred'] > 0)
                        <span class="msg-folder-count">{{ $stats['starred'] }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.contacts.index', ['folder' => 'replied']) }}" class="msg-folder-btn {{ request('folder') === 'replied' ? 'active' : '' }}">
                    <i class="bi bi-reply"></i><span>Yanıtlanan</span>
                </a>
                <a href="{{ route('admin.contacts.index', ['folder' => 'archived']) }}" class="msg-folder-btn {{ request('folder') === 'archived' ? 'active' : '' }}">
                    <i class="bi bi-archive"></i><span>Arşiv</span>
                </a>
                <a href="{{ route('admin.contacts.index', ['folder' => 'trash']) }}" class="msg-folder-btn {{ request('folder') === 'trash' ? 'active' : '' }}">
                    <i class="bi bi-trash3"></i><span>Çöp Kutusu</span>
                </a>

                <div class="msg-labels-section">
                    <div class="msg-labels-title">Konular</div>
                    <a href="{{ route('admin.contacts.index', ['subject' => 'genel']) }}" class="msg-label-btn {{ request('subject') === 'genel' ? 'active' : '' }}">
                        <span class="msg-label-dot msg-label-dot--blue"></span> Genel Bilgi
                    </a>
                    <a href="{{ route('admin.contacts.index', ['subject' => 'isbirligi']) }}" class="msg-label-btn {{ request('subject') === 'isbirligi' ? 'active' : '' }}">
                        <span class="msg-label-dot msg-label-dot--green"></span> İş Birliği
                    </a>
                    <a href="{{ route('admin.contacts.index', ['subject' => 'teknik']) }}" class="msg-label-btn {{ request('subject') === 'teknik' ? 'active' : '' }}">
                        <span class="msg-label-dot msg-label-dot--yellow"></span> Teknik Destek
                    </a>
                    <a href="{{ route('admin.contacts.index', ['subject' => 'oneri']) }}" class="msg-label-btn {{ request('subject') === 'oneri' ? 'active' : '' }}">
                        <span class="msg-label-dot msg-label-dot--red"></span> Öneri / Şikayet
                    </a>
                </div>
            </div>
        </aside>

        <!-- CENTER: Message List -->
        <div class="msg-list-panel" id="msgListPanel">

            <div class="msg-list-header">
                <div class="d-flex align-items-center gap-2">
                    <button class="msg-mobile-back d-lg-none" onclick="document.getElementById('msgFolders').classList.toggle('show')"><i class="bi bi-list"></i></button>
                    <form class="msg-search" method="GET" action="{{ route('admin.contacts.index') }}">
                        @if(request('folder'))
                            <input type="hidden" name="folder" value="{{ request('folder') }}">
                        @endif
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" placeholder="Mesajlarda ara..." value="{{ request('search') }}">
                    </form>
                </div>
            </div>

            <!-- Message Items -->
            <div class="msg-list" id="msgList">
                @forelse($messages as $msg)
                    <div class="msg-item {{ !$msg->is_read ? 'unread' : '' }}"
                         data-id="{{ $msg->id }}"
                         onclick="openMessage({{ $msg->id }})">
                        <button class="msg-star {{ $msg->is_starred ? 'active' : '' }}"
                                onclick="event.stopPropagation(); toggleStar(this, {{ $msg->id }})"
                                title="Yıldızla">
                            <i class="bi {{ $msg->is_starred ? 'bi-star-fill' : 'bi-star' }}"></i>
                        </button>
                        <div class="msg-avatar msg-avatar--{{ $msg->subjectColor() }}">{{ mb_strtoupper(mb_substr($msg->name, 0, 2)) }}</div>
                        <div class="msg-item-content">
                            <div class="msg-item-top">
                                <span class="msg-sender">{{ $msg->name }}</span>
                                <span class="msg-label-tag msg-label-tag--{{ $msg->subjectColor() }}">{{ $msg->subjectLabel() }}</span>
                                <span class="msg-time">{{ $msg->created_at->diffForHumans(short: true) }}</span>
                            </div>
                            <div class="msg-subject">
                                {{ $msg->subjectLabel() }}
                                @if($msg->isReplied())
                                    <i class="bi bi-reply-fill text-neon-green ms-1" title="Yanıtlandı"></i>
                                @endif
                            </div>
                            <div class="msg-preview">{{ Str::limit($msg->message, 100) }}</div>
                        </div>
                        <div class="msg-item-actions">
                            <button onclick="event.stopPropagation(); archiveMsg({{ $msg->id }})" title="Arşivle"><i class="bi bi-archive"></i></button>
                            <button onclick="event.stopPropagation(); deleteMsg({{ $msg->id }})" title="Sil"><i class="bi bi-trash3"></i></button>
                        </div>
                    </div>
                @empty
                    <div class="msg-empty">
                        <i class="bi bi-inbox"></i>
                        <h5>Mesaj bulunamadı</h5>
                        <p>Bu klasörde henüz mesaj yok</p>
                    </div>
                @endforelse
            </div>

            @if($messages->hasPages())
                <div class="cl-pagination-wrapper">
                    <div class="cl-pagination-info">
                        <span>Toplam <strong>{{ number_format($messages->total()) }}</strong> mesajdan <strong>{{ $messages->firstItem() }}-{{ $messages->lastItem() }}</strong> arası gösteriliyor</span>
                    </div>
                    <nav class="cl-pagination">
                        @if($messages->onFirstPage())
                            <button class="cl-page-btn" disabled><i class="bi bi-chevron-left"></i></button>
                        @else
                            <a href="{{ $messages->previousPageUrl() }}" class="cl-page-btn"><i class="bi bi-chevron-left"></i></a>
                        @endif

                        @foreach($messages->getUrlRange(max(1, $messages->currentPage() - 2), min($messages->lastPage(), $messages->currentPage() + 2)) as $page => $url)
                            <a href="{{ $url }}" class="cl-page-btn {{ $page === $messages->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                        @endforeach

                        @if($messages->hasMorePages())
                            <a href="{{ $messages->nextPageUrl() }}" class="cl-page-btn"><i class="bi bi-chevron-right"></i></a>
                        @else
                            <button class="cl-page-btn" disabled><i class="bi bi-chevron-right"></i></button>
                        @endif
                    </nav>
                </div>
            @endif
        </div>

        <!-- RIGHT: Detail Panel -->
        <div class="msg-detail-panel" id="msgDetailPanel">
            <div class="msg-detail-empty" id="msgDetailEmpty">
                <i class="bi bi-envelope-open"></i>
                <h5>Mesaj Seçin</h5>
                <p>Okumak için listeden bir mesaj seçin</p>
            </div>

            <div class="msg-detail-content d-none" id="msgDetailContent">
                <div class="msg-detail-header">
                    <button class="msg-detail-back d-xl-none" onclick="closeDetail()"><i class="bi bi-arrow-left"></i></button>
                    <div class="msg-detail-actions">
                        <button onclick="archiveDetail()" title="Arşivle"><i class="bi bi-archive"></i></button>
                        <button onclick="deleteDetail()" title="Sil"><i class="bi bi-trash3"></i></button>
                    </div>
                </div>

                <div class="msg-detail-body">
                    <h4 class="msg-detail-subject" id="detailSubject"></h4>
                    <div class="msg-detail-meta">
                        <div class="msg-detail-sender-avatar" id="detailAvatar"></div>
                        <div class="msg-detail-sender-info">
                            <div>
                                <strong id="detailSender"></strong>
                                <span class="text-clr-secondary" id="detailEmail"></span>
                            </div>
                            <div class="text-clr-secondary" id="detailDate"></div>
                        </div>
                    </div>
                    <div class="msg-detail-text" id="detailText"></div>

                    <!-- Previous Reply -->
                    <div class="d-none" id="detailReplySection">
                        <hr class="my-3" style="border-color: rgba(155,158,163,0.2);">
                        <div class="mb-2">
                            <i class="bi bi-reply-fill text-neon-green me-1"></i>
                            <strong class="text-neon-green">Yanıt</strong>
                            <span class="text-clr-secondary ms-2" id="detailReplyMeta"></span>
                        </div>
                        <div class="msg-detail-text" id="detailReplyBody"></div>
                    </div>

                    <!-- Quick Reply -->
                    <div class="msg-quick-reply">
                        <div class="msg-reply-header"><i class="bi bi-reply me-1"></i> Yanıtla</div>
                        <textarea class="msg-reply-input" id="quickReplyText" placeholder="Yanıtınızı yazın..." rows="3"></textarea>
                        <div class="msg-reply-footer">
                            <div></div>
                            <button class="btn-teal btn-sm" id="sendReplyBtn"><i class="bi bi-send me-1"></i> Gönder</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection

@push('scripts')
    <script src="{{ asset('assets/admin/js/messages.js') }}?v={{ filemtime(public_path('assets/admin/js/messages.js')) }}"></script>
@endpush
