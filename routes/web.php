<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Geçici debug route — sorun çözülünce silinecek
Route::get('/debug/session', function () {
    // Manuel PHP cookie testi (Laravel'den bağımsız)
    setcookie('raw_test', 'works_' . time(), time() + 3600, '/');

    $previousValue = session('debug_test');
    session(['debug_test' => 'written_at_' . time()]);

    // Sessions tablosundaki kayıt sayısı
    $sessionCount = 0;
    try {
        $sessionCount = \Illuminate\Support\Facades\DB::table('sessions')->count();
    } catch (\Throwable $e) {
        $sessionCount = 'ERROR: ' . $e->getMessage();
    }

    $response = response()->json([
        'session_driver'       => config('session.driver'),
        'session_id'           => session()->getId(),
        'csrf_token'           => csrf_token(),
        'session_domain'       => config('session.domain'),
        'session_secure'       => config('session.secure'),
        'session_same_site'    => config('session.same_site'),
        'session_cookie_name'  => config('session.cookie'),
        'session_path'         => config('session.path'),
        'session_http_only'    => config('session.http_only'),
        'session_partitioned'  => config('session.partitioned'),
        'previous_visit_value' => $previousValue,
        'current_visit_value'  => session('debug_test'),
        'db_session_count'     => $sessionCount,
        'app_url'              => config('app.url'),
        'is_secure'            => request()->isSecure(),
        'request_host'         => request()->getHost(),
        'request_scheme'       => request()->getScheme(),
        'request_url'          => request()->fullUrl(),
        'all_cookies_received' => $_COOKIE,
        'raw_test_cookie'      => $_COOKIE['raw_test'] ?? 'NOT RECEIVED',
        'server_https'         => $_SERVER['HTTPS'] ?? 'NOT SET',
        'x_forwarded_proto'    => request()->header('X-Forwarded-Proto', 'NOT SET'),
        'php_version'          => PHP_VERSION,
    ]);

    // Manuel cookie ekle (Laravel cookie mekanizması ile)
    $response->cookie('laravel_test', 'works_' . time(), 60);

    return $response;
});

// Auth Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

// Admin Routes
Route::prefix('admin')->middleware('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
});
