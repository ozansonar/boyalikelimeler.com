<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Boyalı Kelimeler')</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f3f4f6; color: #1f2937; min-height: 100vh; }
        .navbar { background: #1f2937; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: #f9fafb; text-decoration: none; font-weight: 600; font-size: 1.1rem; }
        .navbar .nav-right { display: flex; align-items: center; gap: 1rem; }
        .navbar .nav-right span { color: #9ca3af; font-size: 0.9rem; }
        .btn { padding: 0.5rem 1rem; border: none; border-radius: 0.375rem; cursor: pointer; font-size: 0.875rem; font-weight: 500; text-decoration: none; display: inline-block; }
        .btn-danger { background: #dc2626; color: white; }
        .btn-danger:hover { background: #b91c1c; }
        .btn-primary { background: #2563eb; color: white; }
        .btn-primary:hover { background: #1d4ed8; }
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        @yield('extra-styles')
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="/">Boyalı Kelimeler</a>
        <div class="nav-right">
            @auth
                <span>{{ auth()->user()->name }} ({{ auth()->user()->role->name ?? '-' }})</span>
                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger">Çıkış</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary">Giriş</a>
            @endauth
        </div>
    </nav>

    <div class="container">
        @yield('content')
    </div>
</body>
</html>
