<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @if(!empty($googleAnalyticsId))
    <!-- Google Analytics (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $googleAnalyticsId }}"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{{ $googleAnalyticsId }}');</script>
    @endif
    @if(!empty($googleSearchConsole))
    <meta name="google-site-verification" content="{{ $googleSearchConsole }}">
    @endif
    @if($siteFavicon)
        <link rel="icon" type="image/webp" href="{{ $siteFavicon }}">
    @else
        <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    @endif
    <link rel="alternate" type="application/rss+xml" title="Boyalı Kelimeler — İçerikler" href="{{ route('feed.literary-works') }}">
    <link rel="alternate" type="application/rss+xml" title="Boyalı Kelimeler — Blog" href="{{ route('feed.blog') }}">
    <title>@yield('title', 'Boyalı Kelimeler')</title>
    <meta name="description" content="@yield('meta_description', 'Sosyal çöküntüye sanatsal direniş. Kelimelerin boyandığı, fırçaların konuştuğu bir sanat hareketi.')">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#000000">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.png') }}">

    <!-- Robots -->
    @hasSection('robots')
        <meta name="robots" content="@yield('robots')">
    @endif

    <!-- Open Graph -->
    <meta property="og:site_name" content="Boyalı Kelimeler">
    <meta property="og:locale" content="tr_TR">
    <meta property="og:title" content="@yield('og_title', 'Boyalı Kelimeler')">
    <meta property="og:description" content="@yield('og_description', 'Sosyal çöküntüye sanatsal direniş.')">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', asset('images/og-cover.jpg'))">
    @stack('og_meta')

    <!-- Twitter Card -->
    <meta name="twitter:card" content="@yield('twitter_card', 'summary_large_image')">
    <meta name="twitter:title" content="@yield('og_title', 'Boyalı Kelimeler')">
    <meta name="twitter:description" content="@yield('og_description', 'Sosyal çöküntüye sanatsal direniş.')">
    <meta name="twitter:image" content="@yield('og_image', asset('images/og-cover.jpg'))">

    <!-- Pagination -->
    @stack('seo_links')

    <!-- JSON-LD -->
    @stack('jsonld')

    <!-- CSRF -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        <meta name="user-id" content="{{ auth()->id() }}">
    @endauth

    <!-- Preload: Critical fonts -->
    <link rel="preload" href="{{ asset('vendor/fonts/inter/inter-latin-400-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('vendor/fonts/playfair-display/playfair-display-latin-700-normal.woff2') }}" as="font" type="font/woff2" crossorigin>

    <!-- Self-hosted Fonts -->
    <link href="{{ asset('vendor/fonts/fonts.css') }}" rel="stylesheet">
    <!-- Bootstrap 5.3.8 (critical) -->
    <link href="{{ asset('vendor/bootstrap/5.3.8/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Custom CSS (critical) -->
    <link href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}" rel="stylesheet">

    <!-- Non-critical CSS: low-priority async load -->
    <link rel="stylesheet" href="{{ asset('vendor/font-awesome/6.7.2/css/all.min.css') }}" media="print" onload="this.media='all'">
    <noscript><link href="{{ asset('vendor/font-awesome/6.7.2/css/all.min.css') }}" rel="stylesheet"></noscript>
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css') }}" media="print" onload="this.media='all'">
    <noscript><link href="{{ asset('vendor/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css') }}" rel="stylesheet"></noscript>
    <link rel="stylesheet" href="{{ asset('vendor/swiper/11/swiper-bundle.min.css') }}" media="print" onload="this.media='all'">
    <noscript><link href="{{ asset('vendor/swiper/11/swiper-bundle.min.css') }}" rel="stylesheet"></noscript>
    <link rel="stylesheet" href="{{ asset('vendor/aos/2.3.4/aos.css') }}" media="print" onload="this.media='all'">
    <noscript><link href="{{ asset('vendor/aos/2.3.4/aos.css') }}" rel="stylesheet"></noscript>

    @stack('styles')
    <style>.bk-loader{position:fixed;inset:0;z-index:99999;background:#0F0F12;display:flex;align-items:center;justify-content:center;transition:opacity .4s}.bk-loader__spinner{width:40px;height:40px;border:3px solid rgba(212,175,55,.2);border-top-color:#D4AF37;border-radius:50%;animation:bk-spin .7s linear infinite}@keyframes bk-spin{to{transform:rotate(360deg)}}.bk-loader--hidden{opacity:0;pointer-events:none}</style>
    <style>/* Styling for the PWA install button specifically on mobile devices */
        @media (max-width: 768px) {
            #installAppBtn {
                position: fixed;
                /* Position at the bottom, accounting for modern smartphone safe areas */
                bottom: calc(20px + env(safe-area-inset-bottom));
                left: 50%;
                /* Center the button precisely */
                transform: translateX(-50%);
                /* Ensure it stays above other content */
                z-index: 9999;

                /* Modern aesthetic styling */
                background-color: #000000;
                color: #ffffff;
                padding: 12px 24px;
                border: none;
                border-radius: 50px; /* Pill shape */
                font-size: 16px;
                font-weight: bold;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2), 0 4px 6px -2px rgba(0, 0, 0, 0.1);
                cursor: pointer;
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>
    <div class="bk-loader" id="bkLoader"><div class="bk-loader__spinner"></div></div>

    <!-- =======================================================
         NAVBAR
    ======================================================= -->
    <nav class="navbar navbar-expand-xl navbar-dark navbar-bk sticky-top" aria-label="Ana navigasyon">
        <div class="container-fluid px-3 px-lg-5">
            <a class="navbar-brand navbar-bk__brand" href="{{ url('/') }}">
                <img src="{{ $siteLogo ?? asset('images/logo.svg') }}"
                     alt="Boyalı Kelimeler Logo"
                     class="navbar-bk__logo"
                     width="200"
                     height="50"
                     loading="lazy">
            </a>

            <button class="navbar-toggler navbar-bk__toggler" type="button"
                    data-bs-toggle="collapse" data-bs-target="#navbarMain"
                    aria-controls="navbarMain" aria-expanded="false" aria-label="Menüyü aç/kapat">
                <i class="fa-solid fa-bars navbar-bk__toggler-icon"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav ms-auto navbar-bk__nav">
                    @isset($navbarMenu)
                        @foreach($navbarMenu as $navItem)
                            @if($navItem->activeChildren->isNotEmpty())
                                <li class="nav-item dropdown">
                                    <a class="nav-link navbar-bk__link dropdown-toggle" href="#" role="button"
                                       data-bs-toggle="dropdown" aria-expanded="false">
                                        @if($navItem->icon)<i class="{{ $navItem->icon }} me-1"></i>@endif{{ $navItem->title }}
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-dark">
                                        @foreach($navItem->activeChildren as $child)
                                            <li>
                                                <a class="dropdown-item" href="{{ $child->resolvedUrl() }}"
                                                   @if($child->target === App\Enums\LinkTarget::Blank) target="_blank" rel="noopener noreferrer" @endif>
                                                    @if($child->icon)<i class="{{ $child->icon }} me-1"></i>@endif{{ $child->title }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link navbar-bk__link" href="{{ $navItem->resolvedUrl() }}"
                                       @if($navItem->target === App\Enums\LinkTarget::Blank) target="_blank" rel="noopener noreferrer" @endif>
                                        @if($navItem->icon)<i class="{{ $navItem->icon }} me-1"></i>@endif{{ $navItem->title }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    @endisset
                </ul>

                <a href="{{ route('search.index') }}" class="navbar-bk__search-btn ms-xl-3" aria-label="Ara" title="Ara">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </a>

                <div class="navbar-bk__auth d-flex align-items-center gap-2 ms-xl-3">
                    @auth
                        <div class="dropdown">
                            <a href="#" class="navbar-bk__auth-link dropdown-toggle" role="button"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-user me-1"></i>{{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end navbar-bk__user-dropdown">
                                @if(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.dashboard') }}" target="_blank">
                                            <i class="fa-solid fa-gauge-high me-2"></i>Admin Panel
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                @endif

                                <li>
                                    <a class="dropdown-item" href="{{ auth()->user()->profile_url }}">
                                        <i class="fa-solid fa-user me-2"></i>Profilim
                                    </a>
                                </li>

                                @if(auth()->user()->isYazar() || auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
                                    <li>
                                        <a class="dropdown-item" href="{{ route('myposts.index') }}">
                                            <i class="fa-solid fa-file-lines me-2"></i>Yazılarım
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('myposts.create') }}">
                                            <i class="fa-solid fa-feather-pointed me-2"></i>Yazı Ekle
                                        </a>
                                    </li>
                                @endif

                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="fa-solid fa-gear me-2"></i>Ayarlar
                                    </a>
                                </li>

                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item navbar-bk__user-dropdown-logout">
                                            <i class="fa-solid fa-right-from-bracket me-2"></i>Çıkış Yap
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
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
                        @if(!empty($socialLinks['instagram']))
                            <a href="{{ $socialLinks['instagram'] }}" class="footer-bk__social-link" target="_blank" rel="noopener noreferrer nofollow" aria-label="Instagram">
                                <i class="fa-brands fa-instagram"></i>
                            </a>
                        @endif
                        @if(!empty($socialLinks['twitter']))
                            <a href="{{ $socialLinks['twitter'] }}" class="footer-bk__social-link" target="_blank" rel="noopener noreferrer nofollow" aria-label="Twitter">
                                <i class="fa-brands fa-x-twitter"></i>
                            </a>
                        @endif
                        @if(!empty($socialLinks['youtube']))
                            <a href="{{ $socialLinks['youtube'] }}" class="footer-bk__social-link" target="_blank" rel="noopener noreferrer nofollow" aria-label="YouTube">
                                <i class="fa-brands fa-youtube"></i>
                            </a>
                        @endif
                        @if(!empty($socialLinks['facebook']))
                            <a href="{{ $socialLinks['facebook'] }}" class="footer-bk__social-link" target="_blank" rel="noopener noreferrer nofollow" aria-label="Facebook">
                                <i class="fa-brands fa-facebook-f"></i>
                            </a>
                        @endif
                        @if(!empty($socialLinks['tiktok']))
                            <a href="{{ $socialLinks['tiktok'] }}" class="footer-bk__social-link" target="_blank" rel="noopener noreferrer nofollow" aria-label="TikTok">
                                <i class="fa-brands fa-tiktok"></i>
                            </a>
                        @endif
                        @if(!empty($socialLinks['linkedin']))
                            <a href="{{ $socialLinks['linkedin'] }}" class="footer-bk__social-link" target="_blank" rel="noopener noreferrer nofollow" aria-label="LinkedIn">
                                <i class="fa-brands fa-linkedin-in"></i>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <h5 class="footer-bk__heading">Keşfet</h5>
                    <nav aria-label="Keşfet linkleri">
                        @isset($footerDiscoverMenu)
                            @foreach($footerDiscoverMenu as $fItem)
                                <a href="{{ $fItem->resolvedUrl() }}" class="footer-bk__link"
                                   @if($fItem->target === App\Enums\LinkTarget::Blank) target="_blank" rel="noopener noreferrer" @endif>{{ $fItem->title }}</a>
                            @endforeach
                        @endisset
                    </nav>
                </div>
                <div class="col-6 col-lg-2">
                    <h5 class="footer-bk__heading">Yarışmalar</h5>
                    <nav aria-label="Yarışma linkleri">
                        @isset($footerCompetitionsMenu)
                            @foreach($footerCompetitionsMenu as $fItem)
                                <a href="{{ $fItem->resolvedUrl() }}" class="footer-bk__link"
                                   @if($fItem->target === App\Enums\LinkTarget::Blank) target="_blank" rel="noopener noreferrer" @endif>{{ $fItem->title }}</a>
                            @endforeach
                        @endisset
                    </nav>
                </div>
                <div class="col-6 col-lg-2">
                    <h5 class="footer-bk__heading">Kurumsal</h5>
                    <nav aria-label="Kurumsal linkler">
                        @isset($footerCorporateMenu)
                            @foreach($footerCorporateMenu as $fItem)
                                <a href="{{ $fItem->resolvedUrl() }}" class="footer-bk__link"
                                   @if($fItem->target === App\Enums\LinkTarget::Blank) target="_blank" rel="noopener noreferrer" @endif>{{ $fItem->title }}</a>
                            @endforeach
                        @endisset
                    </nav>
                </div>
                <div class="col-6 col-lg-2">
                    <h5 class="footer-bk__heading">İletişim</h5>
                    <p class="footer-bk__text">
                        <a href="mailto:info@boyalikelimeler.com" class="footer-bk__link">
                            <i class="fa-solid fa-envelope me-1 text-gold"></i> info@boyalikelimeler.com
                        </a>
                    </p>
                    @if(!empty($socialLinks['whatsapp']))
                        <p class="footer-bk__text">
                            <a href="https://wa.me/+90{{ $socialLinks['whatsapp'] }}?text=Merhaba" target="_blank" rel="noopener noreferrer nofollow" class="footer-bk__link">
                                <i class="fa-brands fa-whatsapp me-1 text-gold"></i> WhatsApp ile ulaşın
                            </a>
                        </p>
                    @endif
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

    <button id="installAppBtn" style="display: none;" class="your-custom-button-class">
        Uygulama İndir
    </button>

    <!-- Global Status Modal -->
    @include('partials.front.status-modal')

    <!-- Bootstrap 5.3.8 JS -->
    <script src="{{ asset('vendor/bootstrap/5.3.8/js/bootstrap.bundle.min.js') }}" defer></script>
    <!-- Swiper.js -->
    <script src="{{ asset('vendor/swiper/11/swiper-bundle.min.js') }}" defer></script>
    <!-- AOS.js -->
    <script src="{{ asset('vendor/aos/2.3.4/aos.js') }}" defer></script>
    <!-- Custom JS -->
    <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}" defer></script>
    @stack('scripts')
    <script>window.addEventListener('load',function(){var l=document.getElementById('bkLoader');if(l){l.classList.add('bk-loader--hidden');l.addEventListener('transitionend',function(){l.remove()})}})</script>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((registration) => {
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);
                    }, (error) => {
                        console.log('ServiceWorker registration failed: ', error);
                    });
            });
        }

        let deferredPrompt;
        const installBtn = document.getElementById('installAppBtn');

        // Detect if device is iOS
        const isIos = () => {
            const userAgent = window.navigator.userAgent.toLowerCase();
            return /iphone|ipad|ipod/.test(userAgent);
        };

        // Detect if browser is Chrome on iOS (CriOS)
        const isIosChrome = () => {
            const userAgent = window.navigator.userAgent.toLowerCase();
            return isIos() && userAgent.includes('crios');
        };

        // Detect if browser is Safari on iOS
        const isIosSafari = () => {
            const userAgent = window.navigator.userAgent.toLowerCase();
            return isIos() && userAgent.includes('safari') && !userAgent.includes('crios') && !userAgent.includes('fxios');
        };

        // Check if app is already installed
        const isAppInstalled = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

        if (!isAppInstalled) {

            // Android & Desktop Chrome logic
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredPrompt = e;

                if (installBtn) {
                    installBtn.style.display = 'block';
                    installBtn.innerText = 'Install App';
                }
            });

            // iOS Handling logic
            if (isIos()) {
                if (installBtn) {
                    installBtn.style.display = 'block';

                    if (isIosChrome()) {
                        installBtn.innerText = 'Open in Safari to Install';
                    } else if (isIosSafari()) {
                        installBtn.innerText = 'Add to Home Screen';
                    } else {
                        installBtn.innerText = 'Install App';
                    }
                }
            }

            // Button click handling
            if (installBtn) {
                installBtn.addEventListener('click', async () => {

                    // Android flow
                    if (deferredPrompt) {
                        deferredPrompt.prompt();
                        const { outcome } = await deferredPrompt.userChoice;
                        deferredPrompt = null;
                        installBtn.style.display = 'none';
                    }
                    // iOS Chrome flow
                    else if (isIosChrome()) {
                        alert("Apple restrictions require this app to be installed via Safari. Please open this site in the Safari browser.");
                    }
                    // iOS Safari flow
                    else if (isIosSafari()) {
                        alert("To install: Tap the 'Share' icon at the bottom of Safari, then tap 'Add to Home Screen'.");
                    }
                });
            }

            // Cleanup after installation
            window.addEventListener('appinstalled', () => {
                if (installBtn) {
                    installBtn.style.display = 'none';
                }
                deferredPrompt = null;
            });
        }
    </script>
</body>
</html>
