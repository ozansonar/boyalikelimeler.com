<!DOCTYPE html>
<html lang="tr" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin — Boyalı Kelimeler')</title>
    @if(!empty($siteFavicon))
        <link rel="icon" type="image/webp" href="{{ $siteFavicon }}">
    @else
        <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    @endif
    <link rel="preload" href="{{ asset('vendor/fonts/inter/inter-latin-400-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link href="{{ asset('vendor/fonts/fonts.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap/5.3.8/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/aos/2.3.4/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin/css/styles.css') }}?v={{ filemtime(public_path('assets/admin/css/styles.css')) }}" rel="stylesheet">
    @stack('styles')
</head>
<body>

<div class="admin-wrapper">

    <!-- Sidebar -->
    @include('partials.admin.sidebar')

    <!-- Main Content -->
    <main class="main-content">

        <!-- Topbar -->
        @include('partials.admin.topbar')

        <!-- Page Content -->
        <div class="page-content">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
                </div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
                </div>
            @endif

            @yield('content')
        </div>

    </main>
</div>

<script src="{{ asset('vendor/bootstrap/5.3.8/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/aos/2.3.4/aos.js') }}"></script>
<script src="{{ asset('assets/admin/js/app.js') }}?v={{ filemtime(public_path('assets/admin/js/app.js')) }}"></script>
@stack('scripts')
</body>
</html>
