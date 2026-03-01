<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <title>@yield('title', 'Boyalı Kelimeler')</title>
    <meta name="description" content="@yield('meta_description', 'Sosyal çöküntüye sanatsal direniş. Kelimelerin boyandığı, fırçaların konuştuğu bir sanat hareketi.')">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    <!-- Open Graph -->
    <meta property="og:title" content="@yield('og_title', 'Boyalı Kelimeler')">
    <meta property="og:description" content="@yield('og_description', 'Sosyal çöküntüye sanatsal direniş.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', asset('images/og-cover.jpg'))">

    <!-- CSRF -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3.8 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Font Awesome 6.7.2 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>

    <!-- Skip Link -->
    <a class="skip-link visually-hidden-focusable" href="#main">İçeriğe atla</a>

    <!-- =======================================================
         NAVBAR
    ======================================================= -->
    <nav class="navbar navbar-expand-xl navbar-dark navbar-bk sticky-top" aria-label="Ana navigasyon">
        <div class="container-fluid px-3 px-lg-5">
            <a class="navbar-brand navbar-bk__brand" href="{{ url('/') }}">
                <img src="{{ asset('images/logo.svg') }}"
                     alt="Boyalı Kelimeler Logo"
                     class="navbar-bk__logo"
                     width="200"
                     height="50">
            </a>

            <button class="navbar-toggler navbar-bk__toggler" type="button"
                    data-bs-toggle="collapse" data-bs-target="#navbarMain"
                    aria-controls="navbarMain" aria-expanded="false" aria-label="Menüyü aç/kapat">
                <i class="fa-solid fa-bars navbar-bk__toggler-icon"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav ms-auto navbar-bk__nav">
                    <li class="nav-item">
                        <a class="nav-link navbar-bk__link" href="{{ url('/') }}">
                            <i class="fa-solid fa-house me-1"></i>Ana Sayfa
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link navbar-bk__link" href="#">
                            <i class="fa-solid fa-book-open me-1"></i>Edebiyat
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link navbar-bk__link" href="#">
                            <i class="fa-solid fa-palette me-1"></i>Sanat
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link navbar-bk__link" href="#">
                            <i class="fa-solid fa-comments me-1"></i>Söz Meydanı
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link navbar-bk__link" href="#">
                            <i class="fa-solid fa-newspaper me-1"></i>Dergimiz
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link navbar-bk__link" href="#">
                            <i class="fa-solid fa-paper-plane me-1"></i>İletişim
                        </a>
                    </li>
                </ul>

                <div class="navbar-bk__auth d-flex align-items-center gap-2 ms-xl-3">
                    @auth
                        <span class="navbar-bk__auth-link">
                            <i class="fa-solid fa-user me-1"></i>{{ auth()->user()->name }}
                        </span>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="navbar-bk__auth-btn">
                                <i class="fa-solid fa-right-from-bracket me-1"></i>Çıkış
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                           class="navbar-bk__auth-link {{ request()->routeIs('login') ? 'navbar-bk__auth-link--active' : '' }}">
                            <i class="fa-solid fa-right-to-bracket me-1"></i>Giriş Yap
                        </a>
                        <a href="{{ route('register') }}"
                           class="navbar-bk__auth-btn {{ request()->routeIs('register') ? 'navbar-bk__auth-btn--active' : '' }}">
                            <i class="fa-solid fa-user-plus me-1"></i>Kayıt Ol
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- =======================================================
         MAIN
    ======================================================= -->
    <main id="main">
        @yield('content')
    </main>

    <!-- =======================================================
         FOOTER
    ======================================================= -->
    <footer class="footer-bk" aria-label="Site alt bilgi">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h4 class="footer-bk__brand">
                        <i class="fa-solid fa-feather-pointed me-2"></i>Boyalı Kelimeler
                    </h4>
                    <p class="footer-bk__text">
                        Sosyal çöküntüye sanatsal direniş. Kelimelerin boyandığı,
                        fırçaların konuştuğu bir sanat hareketi. 2026.
                    </p>
                    <div class="footer-bk__social mt-3">
                        <a href="#" class="footer-bk__social-link" aria-label="Instagram">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                        <a href="#" class="footer-bk__social-link" aria-label="Twitter">
                            <i class="fa-brands fa-x-twitter"></i>
                        </a>
                        <a href="#" class="footer-bk__social-link" aria-label="YouTube">
                            <i class="fa-brands fa-youtube"></i>
                        </a>
                        <a href="#" class="footer-bk__social-link" aria-label="Facebook">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <h5 class="footer-bk__heading">Keşfet</h5>
                    <nav aria-label="Keşfet linkleri">
                        <a href="#" class="footer-bk__link">Yazılar</a>
                        <a href="#" class="footer-bk__link">Resimler</a>
                        <a href="#" class="footer-bk__link">Sanat Okulu</a>
                        <a href="#" class="footer-bk__link">Söz Meydanı</a>
                    </nav>
                </div>
                <div class="col-6 col-lg-2">
                    <h5 class="footer-bk__heading">Yarışmalar</h5>
                    <nav aria-label="Yarışma linkleri">
                        <a href="#" class="footer-bk__link">Altın Kalem</a>
                        <a href="#" class="footer-bk__link">Altın Fırça</a>
                        <a href="#" class="footer-bk__link">Dergimiz</a>
                        <a href="#" class="footer-bk__link">Astroloji</a>
                    </nav>
                </div>
                <div class="col-6 col-lg-2">
                    <h5 class="footer-bk__heading">Kurumsal</h5>
                    <nav aria-label="Kurumsal linkler">
                        <a href="#" class="footer-bk__link">Hakkımızda</a>
                        <a href="#" class="footer-bk__link">Yönetim Ekibi</a>
                        <a href="#" class="footer-bk__link">İletişim</a>
                        <a href="#" class="footer-bk__link">Gizlilik</a>
                    </nav>
                </div>
                <div class="col-6 col-lg-2">
                    <h5 class="footer-bk__heading">İletişim</h5>
                    <p class="footer-bk__text">
                        <i class="fa-solid fa-envelope me-1 text-gold"></i> info@boyalikelimeler.com
                    </p>
                    <p class="footer-bk__text">
                        <i class="fa-brands fa-whatsapp me-1 text-gold"></i> WhatsApp ile ulaşın
                    </p>
                </div>
            </div>

            <div class="footer-bk__bottom text-center">
                <p class="footer-bk__copy mb-1">
                    &copy; {{ date('Y') }} Boyalı Kelimeler — Tüm hakları saklıdır.
                    <span class="d-none d-md-inline">| Sosyal Çöküntüye Sanatsal Direniş</span>
                </p>
                <p class="footer-bk__credit mb-0">
                    <i class="fa-solid fa-code me-1"></i> Yazılım &amp; Tasarım
                    <a href="https://ozansonar.net/" target="_blank" rel="noopener noreferrer" class="footer-bk__credit-link">Ozan SONAR</a>
                </p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5.3.8 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
