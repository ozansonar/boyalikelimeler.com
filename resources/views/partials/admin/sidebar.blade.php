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

        <div class="nav-section-title">Yönetim</div>
        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i> Kullanıcılar
        </a>

        <div class="nav-section-title">Sistem</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-link">
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
