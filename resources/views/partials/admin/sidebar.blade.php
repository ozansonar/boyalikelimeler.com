<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">BK</div>
        <div class="sidebar-brand">
            <h5>Boyalı Kelimeler</h5>
            <span>Admin Panel</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        @if(auth()->user()->hasPermission('dashboard.view'))
            <div class="nav-section-title">Ana Menü</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
        @endif

        @if(auth()->user()->hasPermission('home-sliders.view'))
            <div class="nav-section-title">Ana Sayfa</div>
            <a href="{{ route('admin.home-sliders.index') }}" class="nav-link {{ request()->routeIs('admin.home-sliders.*') ? 'active' : '' }}">
                <i class="bi bi-display-fill"></i> Slider Yönetimi
            </a>
        @endif

        @if(auth()->user()->hasAnyPermission('posts.view', 'categories.view', 'pages.view', 'authors-page.manage'))
            <div class="nav-section-title">İçerik Yönetimi</div>
            @if(auth()->user()->hasPermission('posts.view'))
                <a href="{{ route('admin.posts.index') }}" class="nav-link {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text-fill"></i> İçerikler
                </a>
            @endif
            @if(auth()->user()->hasPermission('categories.view'))
                <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="bi bi-folder-fill"></i> Kategoriler
                </a>
            @endif
            @if(auth()->user()->hasPermission('pages.view'))
                <a href="{{ route('admin.pages.index') }}" class="nav-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-richtext-fill"></i> Sayfalar
                </a>
            @endif
            @if(auth()->user()->hasPermission('authors-page.manage'))
                <a href="{{ route('admin.authors-page.index') }}" class="nav-link {{ request()->routeIs('admin.authors-page.*') ? 'active' : '' }}">
                    <i class="bi bi-pen-fill"></i> Yazarlar
                </a>
            @endif
        @endif

        @if(auth()->user()->hasAnyPermission('literary-works.view', 'literary-categories.view'))
            <div class="nav-section-title">Edebiyat</div>
            @if(auth()->user()->hasPermission('literary-works.view'))
                <a href="{{ route('admin.literary-works.index') }}" class="nav-link {{ request()->routeIs('admin.literary-works.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-text"></i> Edebiyat Eserleri
                    @if(($pendingWorks = app(\App\Services\LiteraryWorkService::class)->getPendingCount()) > 0)
                        <span class="badge bg-danger rounded-pill ms-auto">{{ $pendingWorks }}</span>
                    @endif
                </a>
            @endif
            @if(auth()->user()->hasPermission('literary-categories.view'))
                <a href="{{ route('admin.literary-categories.index') }}" class="nav-link {{ request()->routeIs('admin.literary-categories.*') ? 'active' : '' }}">
                    <i class="bi bi-bookmark-fill"></i> Edebiyat Kategorileri
                </a>
            @endif
        @endif

        @if(auth()->user()->hasAnyPermission('users.view', 'roles.view', 'comments.view', 'contacts.view', 'menus.view'))
            <div class="nav-section-title">Yönetim</div>
            @if(auth()->user()->hasPermission('users.view'))
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i> Kullanıcılar
                </a>
            @endif

            @if(auth()->user()->hasPermission('roles.view'))
                <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                    <i class="bi bi-shield-fill"></i> Roller & İzinler
                </a>
            @endif

            @if(auth()->user()->hasPermission('comments.view'))
                <a href="{{ route('admin.comments.index') }}" class="nav-link {{ request()->routeIs('admin.comments.*') ? 'active' : '' }}">
                    <i class="bi bi-chat-dots-fill"></i> Yorumlar
                    @if(($pendingComments = app(\App\Services\CommentService::class)->getPendingCount()) > 0)
                        <span class="badge bg-danger rounded-pill ms-auto">{{ $pendingComments }}</span>
                    @endif
                </a>
            @endif
            @if(auth()->user()->hasPermission('contacts.view'))
                <a href="{{ route('admin.contacts.index') }}" class="nav-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                    <i class="bi bi-chat-left-text-fill"></i> Mesajlar
                    @if(($unreadMessages = \App\Models\ContactMessage::where('is_read', false)->count()) > 0)
                        <span class="badge bg-danger rounded-pill ms-auto">{{ $unreadMessages }}</span>
                    @endif
                </a>
            @endif
            @if(auth()->user()->hasPermission('menus.view'))
                <a href="{{ route('admin.menus.index') }}" class="nav-link {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}">
                    <i class="bi bi-list-nested"></i> Menüler
                </a>
            @endif
        @endif

        @if(auth()->user()->hasAnyPermission('mail-logs.view', 'settings.view'))
            <div class="nav-section-title">Sistem</div>
            @if(auth()->user()->hasPermission('mail-logs.view'))
                <a href="{{ route('admin.mail-logs.index') }}" class="nav-link {{ request()->routeIs('admin.mail-logs.*') ? 'active' : '' }}">
                    <i class="bi bi-envelope-fill"></i> Mail Logları
                </a>
            @endif
            @if(auth()->user()->hasPermission('settings.view'))
                <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="bi bi-gear-fill"></i> Ayarlar
                </a>
            @endif
        @endif
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
