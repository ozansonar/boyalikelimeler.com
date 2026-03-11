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
    <link href="{{ asset('vendor/font-awesome/6.7.2/css/all.min.css') }}" rel="stylesheet">
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
            @yield('content')
        </div>

    </main>
</div>

<!-- Global Status Modal -->
<div class="modal fade" id="globalStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-5">
                <div class="status-modal-icon" id="gsm-icon"></div>
                <h4 class="fw-800-mb" id="gsm-title"></h4>
                <p class="text-muted-label" id="gsm-message"></p>
                <button class="btn-teal" id="gsm-btn" data-bs-dismiss="modal"></button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('vendor/bootstrap/5.3.8/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/aos/2.3.4/aos.js') }}"></script>
<script src="{{ asset('assets/admin/js/app.js') }}?v={{ filemtime(public_path('assets/admin/js/app.js')) }}"></script>
@if(session('success') || session('error') || session('warning') || session('info'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            showStatusModal('success', @json(session('success')));
        @elseif(session('error'))
            showStatusModal('danger', @json(session('error')));
        @elseif(session('warning'))
            showStatusModal('warning', @json(session('warning')));
        @elseif(session('info'))
            showStatusModal('info', @json(session('info')));
        @endif
    });
</script>
@endif
@stack('scripts')
</body>
</html>
