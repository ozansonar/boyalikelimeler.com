<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Front\BlogController;
use App\Http\Controllers\Front\PageController;
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

// Blog (Frontend)
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// Admin Routes
Route::prefix('admin')->middleware('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::resource('users', UserController::class)->except(['show']);

    // Category Management
    Route::resource('categories', CategoryController::class)->except(['show']);

    // Post Management
    Route::resource('posts', PostController::class)->except(['show']);

    // Page Management
    Route::resource('pages', AdminPageController::class)->except(['show']);

    // Menu Management
    Route::resource('menus', MenuController::class)->except(['show']);
    Route::get('menus/{menu}/items', [MenuController::class, 'items'])->name('menus.items');
    Route::post('menus/{menu}/items', [MenuController::class, 'storeItem'])->name('menus.items.store');
    Route::put('menus/{menu}/items/{item}', [MenuController::class, 'updateItem'])->name('menus.items.update');
    Route::delete('menus/{menu}/items/{item}', [MenuController::class, 'destroyItem'])->name('menus.items.destroy');
    Route::post('menus/{menu}/items/reorder', [MenuController::class, 'reorderItems'])->name('menus.items.reorder');

    // Settings
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('settings/general', [SettingController::class, 'updateGeneral'])->name('settings.update.general');
    Route::put('settings/contact', [SettingController::class, 'updateContact'])->name('settings.update.contact');
    Route::put('settings/social', [SettingController::class, 'updateSocial'])->name('settings.update.social');
    Route::put('settings/seo', [SettingController::class, 'updateSeo'])->name('settings.update.seo');
    Route::put('settings/smtp', [SettingController::class, 'updateSmtp'])->name('settings.update.smtp');
    Route::put('settings/maintenance', [SettingController::class, 'updateMaintenance'])->name('settings.update.maintenance');
    Route::get('settings/remove-logo', [SettingController::class, 'removeLogo'])->name('settings.remove-logo');
    Route::get('settings/remove-favicon', [SettingController::class, 'removeFavicon'])->name('settings.remove-favicon');
    Route::get('settings/clear-cache', [SettingController::class, 'clearCache'])->name('settings.clear-cache');
});

// Static Pages (catch-all — MUST be LAST route)
Route::get('/{slug}', [PageController::class, 'show'])->name('page.show')->where('slug', '[a-z0-9\-]+');
