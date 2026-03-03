<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">BK</div>
        <div class="sidebar-brand">
            <h5>Boyalı Kelimeler</h5>
            <span>Admin Panel</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-title">Ana Menü</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>

        <div class="nav-section-title">İçerik Yönetimi</div>
        <a href="{{ route('admin.posts.index') }}" class="nav-link {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-text-fill"></i> İçerikler
        </a>
        <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <i class="bi bi-folder-fill"></i> Kategoriler
        </a>
        <a href="{{ route('admin.pages.index') }}" class="nav-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-richtext-fill"></i> Sayfalar
        </a>

        <div class="nav-section-title">Edebiyat</div>
        <a href="{{ route('admin.literary-works.index') }}" class="nav-link {{ request()->routeIs('admin.literary-works.*') ? 'active' : '' }}">
            <i class="bi bi-journal-text"></i> Edebiyat Eserleri
            @if(($pendingWorks = app(\App\Services\LiteraryWorkService::class)->getPendingCount()) > 0)
                <span class="badge bg-danger rounded-pill ms-auto">{{ $pendingWorks }}</span>
            @endif
        </a>
        <a href="{{ route('admin.literary-categories.index') }}" class="nav-link {{ request()->routeIs('admin.literary-categories.*') ? 'active' : '' }}">
            <i class="bi bi-bookmark-fill"></i> Edebiyat Kategorileri
        </a>

        <div class="nav-section-title">Yönetim</div>
        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i> Kullanıcılar
        </a>

        <a href="{{ route('admin.comments.index') }}" class="nav-link {{ request()->routeIs('admin.comments.*') ? 'active' : '' }}">
            <i class="bi bi-chat-dots-fill"></i> Yorumlar
            @if(($pendingComments = app(\App\Services\CommentService::class)->getPendingCount()) > 0)
                <span class="badge bg-danger rounded-pill ms-auto">{{ $pendingComments }}</span>
            @endif
        </a>
        <a href="{{ route('admin.contacts.index') }}" class="nav-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
            <i class="bi bi-chat-left-text-fill"></i> Mesajlar
            @if(($unreadMessages = \App\Models\ContactMessage::where('is_read', false)->count()) > 0)
                <span class="badge bg-danger rounded-pill ms-auto">{{ $unreadMessages }}</span>
            @endif
        </a>
        <a href="{{ route('admin.menus.index') }}" class="nav-link {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}">
            <i class="bi bi-list-nested"></i> Menüler
        </a>

        <div class="nav-section-title">Sistem</div>
        <a href="{{ route('admin.mail-logs.index') }}" class="nav-link {{ request()->routeIs('admin.mail-logs.*') ? 'active' : '' }}">
            <i class="bi bi-envelope-fill"></i> Mail Logları
        </a>
        <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <i class="bi bi-gear-fill"></i> Ayarlar
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-user-avatar">{{ mb_substr(auth()->user()->name, 0, 2) }}</div>
            <div class="sidebar-user-info">
                <h6>{{ auth()->user()->name }}</h6>
                <span>{{ auth()->user()->role->name ?? '-' }}</span>
            </div>
        </div>
    </div>
</aside>
