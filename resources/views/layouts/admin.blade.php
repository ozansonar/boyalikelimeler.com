<!DOCTYPE html>
<html lang="tr" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin — Boyalı Kelimeler')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="{{ asset('assets/admin/js/app.js') }}?v={{ filemtime(public_path('assets/admin/js/app.js')) }}"></script>
@stack('scripts')
</body>
</html>
