<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Auth Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

// Email Verification
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware('signed')
    ->name('verification.verify');
Route::post('/email/resend', [VerificationController::class, 'resend'])
    ->middleware('throttle:6,1')
    ->name('verification.resend');

// Password Reset
Route::get('/sifremi-unuttum', [ForgotPasswordController::class, 'showForm'])->name('password.request');
Route::post('/sifremi-unuttum', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
Route::get('/sifre-sifirla/{token}', [ResetPasswordController::class, 'showForm'])->name('password.reset');
Route::post('/sifre-sifirla', [ResetPasswordController::class, 'reset'])->name('password.update');

// Admin Routes
Route::prefix('admin')->middleware('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
});
