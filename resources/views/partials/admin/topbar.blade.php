<header class="top-navbar" id="topNavbar">
    <div class="d-flex align-items-center gap-3">
        <button type="button" class="btn-icon d-lg-none" aria-label="Menüyü aç/kapat" onclick="document.getElementById('sidebar').classList.toggle('show')">
            <i class="bi bi-list"></i>
        </button>
        <button type="button" class="sidebar-toggle-btn" id="sidebarToggleBtn" aria-label="Menüyü aç/kapat" onclick="toggleSidebar()">
            <i class="bi bi-layout-sidebar-inset" id="sidebarToggleIcon"></i>
        </button>
    </div>
    <div class="navbar-actions">
        <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="nav-action-btn" aria-label="Çıkış" title="Çıkış Yap">
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </form>
    </div>
</header>
