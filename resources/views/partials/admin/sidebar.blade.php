<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">BK</div>
        <div class="sidebar-brand">
            <h5>Boyalı Kelimeler</h5>
            <span>Admin Panel</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        @if($sidebarUser->hasPermission('dashboard.view'))
            <div class="nav-section-title">Ana Menü</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
        @endif

        @if($sidebarUser->hasAnyPermission('home-sliders.view', 'advertisements.view', 'polls.view', 'daily-questions.view'))
            <div class="nav-section-title">Ana Sayfa</div>
            @if($sidebarUser->hasPermission('home-sliders.view'))
                <a href="{{ route('admin.home-sliders.index') }}" class="nav-link {{ request()->routeIs('admin.home-sliders.*') ? 'active' : '' }}">
                    <i class="bi bi-display-fill"></i> Slider Yönetimi
                </a>
            @endif
            @if($sidebarUser->hasPermission('advertisements.view'))
                <a href="{{ route('admin.advertisements.index') }}" class="nav-link {{ request()->routeIs('admin.advertisements.*') ? 'active' : '' }}">
                    <i class="bi bi-megaphone-fill"></i> Reklam Yönetimi
                </a>
            @endif
            @if($sidebarUser->hasPermission('polls.view'))
                <a href="{{ route('admin.polls.index') }}" class="nav-link {{ request()->routeIs('admin.polls.*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-fill"></i> Anket Yönetimi
                </a>
            @endif
            @if($sidebarUser->hasPermission('daily-questions.view'))
                <a href="{{ route('admin.daily-questions.index') }}" class="nav-link {{ request()->routeIs('admin.daily-questions.*') ? 'active' : '' }}">
                    <i class="bi bi-question-circle-fill"></i> Günün Sorusu
                </a>
            @endif
        @endif

        @if($sidebarUser->hasAnyPermission('posts.view', 'categories.view', 'pages.view', 'authors-page.manage', 'painters-page.manage'))
            <div class="nav-section-title">İçerik Yönetimi</div>
            @if($sidebarUser->hasPermission('posts.view'))
                <a href="{{ route('admin.posts.index') }}" class="nav-link {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text-fill"></i> İçerikler
                </a>
            @endif
            @if($sidebarUser->hasPermission('categories.view'))
                <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="bi bi-folder-fill"></i> Kategoriler
                </a>
            @endif
            @if($sidebarUser->hasPermission('pages.view'))
                <a href="{{ route('admin.pages.index') }}" class="nav-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-richtext-fill"></i> Sayfalar
                </a>
            @endif
            @if($sidebarUser->hasPermission('authors-page.manage'))
                <a href="{{ route('admin.authors-page.index') }}" class="nav-link {{ request()->routeIs('admin.authors-page.*') ? 'active' : '' }}">
                    <i class="bi bi-pen-fill"></i> Yazarlar
                </a>
            @endif
            @if($sidebarUser->hasPermission('painters-page.manage'))
                <a href="{{ route('admin.painters-page.index') }}" class="nav-link {{ request()->routeIs('admin.painters-page.*') ? 'active' : '' }}">
                    <i class="bi bi-palette-fill"></i> Ressamlar
                </a>
            @endif
        @endif

        @if($sidebarUser->hasAnyPermission('literary-works.view', 'literary-categories.view'))
            <div class="nav-section-title">Edebiyat</div>
            @if($sidebarUser->hasPermission('literary-works.view'))
                <a href="{{ route('admin.literary-works.index') }}" class="nav-link {{ request()->routeIs('admin.literary-works.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-text"></i> Edebiyat Eserleri
                    @if($pendingWorksCount > 0)
                        <span class="badge bg-danger rounded-pill ms-auto">{{ $pendingWorksCount }}</span>
                    @endif
                </a>
            @endif
            @if($sidebarUser->hasPermission('literary-categories.view'))
                <a href="{{ route('admin.literary-categories.index') }}" class="nav-link {{ request()->routeIs('admin.literary-categories.*') ? 'active' : '' }}">
                    <i class="bi bi-bookmark-fill"></i> Edebiyat Kategorileri
                </a>
            @endif
        @endif

        @if($sidebarUser->hasAnyPermission('users.view', 'roles.view', 'comments.view', 'contacts.view', 'menus.view'))
            <div class="nav-section-title">Yönetim</div>
            @if($sidebarUser->hasPermission('users.view'))
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i> Kullanıcılar
                </a>
            @endif

            @if($sidebarUser->hasPermission('roles.view'))
                <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                    <i class="bi bi-shield-fill"></i> Roller & İzinler
                </a>
            @endif

            @if($sidebarUser->hasPermission('comments.view'))
                <a href="{{ route('admin.comments.index') }}" class="nav-link {{ request()->routeIs('admin.comments.*') ? 'active' : '' }}">
                    <i class="bi bi-chat-dots-fill"></i> Yorumlar
                    @if($pendingCommentsCount > 0)
                        <span class="badge bg-danger rounded-pill ms-auto">{{ $pendingCommentsCount }}</span>
                    @endif
                </a>
            @endif
            @if($sidebarUser->hasPermission('contacts.view'))
                <a href="{{ route('admin.contacts.index') }}" class="nav-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                    <i class="bi bi-chat-left-text-fill"></i> Mesajlar
                    @if($unreadMessagesCount > 0)
                        <span class="badge bg-danger rounded-pill ms-auto">{{ $unreadMessagesCount }}</span>
                    @endif
                </a>
            @endif
            @if($sidebarUser->hasPermission('menus.view'))
                <a href="{{ route('admin.menus.index') }}" class="nav-link {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}">
                    <i class="bi bi-list-nested"></i> Menüler
                </a>
            @endif
        @endif

        @if($sidebarUser->hasAnyPermission('mail-logs.view', 'mail-templates.view', 'settings.view'))
            <div class="nav-section-title">Sistem</div>
            @if($sidebarUser->hasPermission('mail-logs.view'))
                <a href="{{ route('admin.mail-logs.index') }}" class="nav-link {{ request()->routeIs('admin.mail-logs.*') ? 'active' : '' }}">
                    <i class="bi bi-envelope-fill"></i> Mail Logları
                </a>
            @endif
            @if($sidebarUser->hasPermission('mail-templates.view'))
                <a href="{{ route('admin.mail-templates.index') }}" class="nav-link {{ request()->routeIs('admin.mail-templates.*') ? 'active' : '' }}">
                    <i class="bi bi-envelope-paper-fill"></i> Mail Şablonları
                </a>
            @endif
            @if($sidebarUser->hasPermission('settings.view'))
                <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="bi bi-gear-fill"></i> Ayarlar
                </a>
            @endif
        @endif

        <div class="nav-section-title">Kısayollar</div>
        <a href="{{ url('/') }}" target="_blank" class="nav-link">
            <i class="bi bi-box-arrow-up-right"></i> Siteye Dön
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="{{ route('admin.profile.index') }}" class="sidebar-user text-decoration-none">
            <div class="sidebar-user-avatar">
                @if($sidebarUser->avatar)
                    <img src="{{ upload_url($sidebarUser->avatar, 'thumb') }}" alt="{{ $sidebarUser->name }}" class="ap-sidebar-avatar-img" loading="lazy">
                @else
                    {{ mb_substr($sidebarUser->name, 0, 2) }}
                @endif
            </div>
            <div class="sidebar-user-info">
                <h6>{{ $sidebarUser->name }}</h6>
                <span>{{ $sidebarUser->role->name ?? '-' }}</span>
            </div>
        </a>
    </div>
</aside>
