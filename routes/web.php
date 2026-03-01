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
    $previousValue = session('debug_test');
    session(['debug_test' => 'written_at_' . time()]);

    return response()->json([
        'session_driver'       => config('session.driver'),
        'session_id'           => session()->getId(),
        'csrf_token'           => csrf_token(),
        'session_domain'       => config('session.domain'),
        'session_secure'       => config('session.secure'),
        'session_same_site'    => config('session.same_site'),
        'session_cookie_name'  => config('session.cookie'),
        'previous_visit_value' => $previousValue,
        'current_visit_value'  => session('debug_test'),
        'app_url'              => config('app.url'),
        'is_secure'            => request()->isSecure(),
        'request_host'         => request()->getHost(),
        'request_scheme'       => request()->getScheme(),
        'all_cookies'          => array_keys($_COOKIE),
        'server_https'         => $_SERVER['HTTPS'] ?? 'NOT SET',
        'x_forwarded_proto'    => request()->header('X-Forwarded-Proto', 'NOT SET'),
    ]);
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
