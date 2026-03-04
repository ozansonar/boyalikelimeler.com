<?php

use App\Http\Controllers\Admin\AuthorsPageController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HomeSliderController;
use App\Http\Controllers\Admin\LiteraryCategoryController;
use App\Http\Controllers\Admin\LiteraryWorkController;
use App\Http\Controllers\Admin\MailLogController;
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
use App\Http\Controllers\EditorImageController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Front\AuthorController;
use App\Http\Controllers\Front\BlogController;
use App\Http\Controllers\Front\CommentController;
use App\Http\Controllers\Front\ContactController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\LiteraryWorkController as FrontLiteraryWorkController;
use App\Http\Controllers\Front\MyPostController;
use App\Http\Controllers\Front\PageController;
use App\Http\Controllers\Front\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

// Email Verification
Route::get('/email/dogrula', fn () => view('auth.verify'))
    ->middleware('auth')
    ->name('verification.notice');
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

// Profile (public)
Route::get('/yazar/{user:username}', [ProfileController::class, 'show'])->name('profile.show');

// User Panel (auth required)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profil/duzenle', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil/duzenle', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profil/sifre', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profil/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::post('/profil/kapak', [ProfileController::class, 'updateCover'])->name('profile.cover');

    // Editor Image Upload (TinyMCE)
    Route::get('/editor/images', [EditorImageController::class, 'index'])->name('editor.images.index');
    Route::post('/editor/images', [EditorImageController::class, 'store'])->name('editor.images.store');
    Route::delete('/editor/images/{editorImage}', [EditorImageController::class, 'destroy'])->name('editor.images.destroy');

    // My Literary Works (Eserlerim)
    Route::get('/yazilarim', [MyPostController::class, 'index'])->name('myposts.index');
    Route::get('/yazilarim/{work}', [MyPostController::class, 'show'])->name('myposts.show');
    Route::get('/yazi-gonder', [MyPostController::class, 'create'])->name('myposts.create');
    Route::post('/yazi-gonder', [MyPostController::class, 'store'])->name('myposts.store');
    Route::get('/yazi-duzenle/{work}', [MyPostController::class, 'edit'])->name('myposts.edit');
    Route::put('/yazi-duzenle/{work}', [MyPostController::class, 'update'])->name('myposts.update');
    Route::patch('/yazilarim/{work}/yayindan-kaldir', [MyPostController::class, 'unpublish'])->name('myposts.unpublish');
    Route::patch('/yazilarim/{work}/tekrar-yayinla', [MyPostController::class, 'republish'])->name('myposts.republish');
    Route::delete('/yazilarim/{work}', [MyPostController::class, 'destroy'])->name('myposts.destroy');
});

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

    // Authors Page Settings
    Route::get('authors-page', [AuthorsPageController::class, 'index'])->name('authors-page.index');
    Route::put('authors-page', [AuthorsPageController::class, 'update'])->name('authors-page.update');

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
    Route::get('settings/remove-mail-logo', [SettingController::class, 'removeMailLogo'])->name('settings.remove-mail-logo');
    Route::get('settings/clear-cache', [SettingController::class, 'clearCache'])->name('settings.clear-cache');
    Route::post('settings/send-test-mail', [SettingController::class, 'sendTestMail'])->name('settings.send-test-mail');

    // Comment Management
    Route::get('comments', [AdminCommentController::class, 'index'])->name('comments.index');
    Route::get('comments/{comment}', [AdminCommentController::class, 'show'])->name('comments.show');
    Route::get('comments/{comment}/edit', [AdminCommentController::class, 'edit'])->name('comments.edit');
    Route::put('comments/{comment}', [AdminCommentController::class, 'update'])->name('comments.update');
    Route::patch('comments/{comment}/approve', [AdminCommentController::class, 'approve'])->name('comments.approve');
    Route::patch('comments/{comment}/reject', [AdminCommentController::class, 'reject'])->name('comments.reject');
    Route::delete('comments/{comment}', [AdminCommentController::class, 'destroy'])->name('comments.destroy');

    // Contact Messages
    Route::get('contacts', [AdminContactController::class, 'index'])->name('contacts.index');
    Route::patch('contacts/mark-all-read', [AdminContactController::class, 'markAllRead'])->name('contacts.mark-all-read');
    Route::get('contacts/{id}', [AdminContactController::class, 'show'])->name('contacts.show')->where('id', '[0-9]+');
    Route::post('contacts/{id}/reply', [AdminContactController::class, 'reply'])->name('contacts.reply')->where('id', '[0-9]+');
    Route::patch('contacts/{id}/star', [AdminContactController::class, 'toggleStar'])->name('contacts.star')->where('id', '[0-9]+');
    Route::patch('contacts/{id}/archive', [AdminContactController::class, 'archive'])->name('contacts.archive')->where('id', '[0-9]+');
    Route::delete('contacts/{id}', [AdminContactController::class, 'destroy'])->name('contacts.destroy')->where('id', '[0-9]+');

    // Mail Logs
    Route::get('mail-logs', [MailLogController::class, 'index'])->name('mail-logs.index');
    Route::get('mail-logs/{mailLog}', [MailLogController::class, 'show'])->name('mail-logs.show');
    Route::delete('mail-logs/{mailLog}', [MailLogController::class, 'destroy'])->name('mail-logs.destroy');

    // Home Slider Management (Ana Sayfa Slider)
    Route::resource('home-sliders', HomeSliderController::class)->except(['show']);
    Route::post('home-sliders/update-order', [HomeSliderController::class, 'updateOrder'])->name('home-sliders.update-order');

    // Literary Category Management (Edebiyat Kategorileri)
    Route::resource('literary-categories', LiteraryCategoryController::class)->except(['show']);

    // Literary Work Management (Edebiyat Eserleri)
    Route::get('literary-works', [LiteraryWorkController::class, 'index'])->name('literary-works.index');
    Route::get('literary-works/{id}', [LiteraryWorkController::class, 'show'])->name('literary-works.show')->where('id', '[0-9]+');
    Route::get('literary-works/{literaryWork}/edit', [LiteraryWorkController::class, 'edit'])->name('literary-works.edit');
    Route::put('literary-works/{literaryWork}', [LiteraryWorkController::class, 'update'])->name('literary-works.update');
    Route::delete('literary-works/{literaryWork}', [LiteraryWorkController::class, 'destroy'])->name('literary-works.destroy');
    Route::patch('literary-works/{literaryWork}/approve', [LiteraryWorkController::class, 'approve'])->name('literary-works.approve');
    Route::patch('literary-works/{literaryWork}/reject', [LiteraryWorkController::class, 'reject'])->name('literary-works.reject');
    Route::patch('literary-works/{literaryWork}/unpublish', [LiteraryWorkController::class, 'unpublish'])->name('literary-works.unpublish');
    Route::post('literary-works/{literaryWork}/revision', [LiteraryWorkController::class, 'requestRevision'])->name('literary-works.revision');
});

// Comments (Frontend)
Route::post('/yorum', [CommentController::class, 'store'])->name('comment.store')->middleware('throttle:5,1');

// Contact (Frontend)
Route::get('/iletisim', [ContactController::class, 'show'])->name('contact.show');
Route::post('/iletisim', [ContactController::class, 'store'])->name('contact.store')->middleware('throttle:5,1');

// Literary Works (Frontend — İçerikler)
Route::get('/icerikler', [FrontLiteraryWorkController::class, 'index'])->name('literary-works.index');
Route::get('/icerik/{slug}', [FrontLiteraryWorkController::class, 'show'])->name('literary-works.show');

// Authors (Frontend — Yazarlar)
Route::get('/yazarlar', [AuthorController::class, 'index'])->name('authors.index');
Route::get('/yazarlar/altin-kalem/{yearMonth}', [AuthorController::class, 'goldenPenMonth'])
    ->name('authors.golden-pen-month')
    ->where('yearMonth', '\d{4}-\d{2}');

// Static Pages (catch-all — MUST be LAST route)
Route::get('/{slug}', [PageController::class, 'show'])->name('page.show')->where('slug', '[a-z0-9\-]+');
