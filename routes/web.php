<?php

use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AuthorStatisticsController;
use App\Http\Controllers\Admin\AuthorsPageController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdvertisementController;
use App\Http\Controllers\Admin\HomeSliderController;
use App\Http\Controllers\Admin\LiteraryCategoryController;
use App\Http\Controllers\Admin\LiteraryWorkController;
use App\Http\Controllers\Admin\MailLogController;
use App\Http\Controllers\Admin\MailTemplateController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\PaintersPageController;
use App\Http\Controllers\Admin\DailyQuestionController as AdminDailyQuestionController;
use App\Http\Controllers\Admin\PollController as AdminPollController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\RoleController;
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
use App\Http\Controllers\Front\PainterController;
use App\Http\Controllers\Front\CommentController;
use App\Http\Controllers\Front\CategoryController as FrontCategoryController;
use App\Http\Controllers\Front\ContactController;
use App\Http\Controllers\Front\FavoriteController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\SearchController;
use App\Http\Controllers\Front\LiteraryWorkController as FrontLiteraryWorkController;
use App\Http\Controllers\Front\MyPostController;
use App\Http\Controllers\Front\PageController;
use App\Http\Controllers\Front\DailyQuestionController as FrontDailyQuestionController;
use App\Http\Controllers\Front\PollController as FrontPollController;
use App\Http\Controllers\Front\ProfileController;
use App\Http\Controllers\Front\QnaController;
use App\Http\Controllers\Admin\QnaCategoryController;
use App\Http\Controllers\Admin\QnaQuestionController as AdminQnaQuestionController;
use App\Http\Controllers\Admin\QnaAnswerController as AdminQnaAnswerController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/advertisement/{advertisement}/click', [HomeController::class, 'trackAdClick'])->name('advertisement.click');
Route::post('/advertisement/{advertisement}/view', [HomeController::class, 'trackAdView'])->name('advertisement.view');

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

    // Favorite Toggle (AJAX)
    Route::post('/favori/toggle', [FavoriteController::class, 'toggle'])->name('favorite.toggle');

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
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard')->middleware('permission:dashboard.view');

    // Admin Profile
    Route::get('profile', [AdminProfileController::class, 'index'])->name('profile.index');
    Route::put('profile', [AdminProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('profile/avatar', [AdminProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::delete('profile/avatar', [AdminProfileController::class, 'removeAvatar'])->name('profile.avatar.remove');

    // User Management
    Route::get('users/create', [UserController::class, 'create'])->name('users.create')->middleware('permission:users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store')->middleware('permission:users.create');
    Route::middleware('permission:users.view')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    });
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('permission:users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('permission:users.edit');
    Route::patch('users/{user}', [UserController::class, 'update'])->name('users.update.patch')->middleware('permission:users.edit');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:users.delete');

    // Category Management
    Route::middleware('permission:categories.view')->group(function () {
        Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    });
    Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create')->middleware('permission:categories.create');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store')->middleware('permission:categories.create');
    Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit')->middleware('permission:categories.edit');
    Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update')->middleware('permission:categories.edit');
    Route::patch('categories/{category}', [CategoryController::class, 'update'])->name('categories.update.patch')->middleware('permission:categories.edit');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy')->middleware('permission:categories.delete');

    // Post Management
    Route::middleware('permission:posts.view')->group(function () {
        Route::get('posts', [PostController::class, 'index'])->name('posts.index');
    });
    Route::get('posts/create', [PostController::class, 'create'])->name('posts.create')->middleware('permission:posts.create');
    Route::post('posts', [PostController::class, 'store'])->name('posts.store')->middleware('permission:posts.create');
    Route::get('posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit')->middleware('permission:posts.edit');
    Route::put('posts/{post}', [PostController::class, 'update'])->name('posts.update')->middleware('permission:posts.edit');
    Route::patch('posts/{post}', [PostController::class, 'update'])->name('posts.update.patch')->middleware('permission:posts.edit');
    Route::delete('posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy')->middleware('permission:posts.delete');

    // Page Management
    Route::middleware('permission:pages.view')->group(function () {
        Route::get('pages', [AdminPageController::class, 'index'])->name('pages.index');
    });
    Route::get('pages/create', [AdminPageController::class, 'create'])->name('pages.create')->middleware('permission:pages.create');
    Route::post('pages', [AdminPageController::class, 'store'])->name('pages.store')->middleware('permission:pages.create');
    Route::get('pages/{page}/edit', [AdminPageController::class, 'edit'])->name('pages.edit')->middleware('permission:pages.edit');
    Route::put('pages/{page}', [AdminPageController::class, 'update'])->name('pages.update')->middleware('permission:pages.edit');
    Route::patch('pages/{page}', [AdminPageController::class, 'update'])->name('pages.update.patch')->middleware('permission:pages.edit');
    Route::delete('pages/{page}', [AdminPageController::class, 'destroy'])->name('pages.destroy')->middleware('permission:pages.delete');

    // Menu Management
    Route::middleware('permission:menus.view')->group(function () {
        Route::get('menus', [MenuController::class, 'index'])->name('menus.index');
        Route::get('menus/{menu}/items', [MenuController::class, 'items'])->name('menus.items');
    });
    Route::get('menus/create', [MenuController::class, 'create'])->name('menus.create')->middleware('permission:menus.create');
    Route::post('menus', [MenuController::class, 'store'])->name('menus.store')->middleware('permission:menus.create');
    Route::middleware('permission:menus.edit')->group(function () {
        Route::get('menus/{menu}/edit', [MenuController::class, 'edit'])->name('menus.edit');
        Route::put('menus/{menu}', [MenuController::class, 'update'])->name('menus.update');
        Route::patch('menus/{menu}', [MenuController::class, 'update'])->name('menus.update.patch');
        Route::post('menus/{menu}/items', [MenuController::class, 'storeItem'])->name('menus.items.store');
        Route::put('menus/{menu}/items/{item}', [MenuController::class, 'updateItem'])->name('menus.items.update');
        Route::delete('menus/{menu}/items/{item}', [MenuController::class, 'destroyItem'])->name('menus.items.destroy');
        Route::post('menus/{menu}/items/reorder', [MenuController::class, 'reorderItems'])->name('menus.items.reorder');
    });
    Route::delete('menus/{menu}', [MenuController::class, 'destroy'])->name('menus.destroy')->middleware('permission:menus.delete');

    // Authors Page Settings
    Route::middleware('permission:authors-page.manage')->group(function () {
        Route::get('authors-page', [AuthorsPageController::class, 'index'])->name('authors-page.index');
        Route::put('authors-page', [AuthorsPageController::class, 'update'])->name('authors-page.update');
    });

    // Painters Page Settings
    Route::middleware('permission:painters-page.manage')->group(function () {
        Route::get('painters-page', [PaintersPageController::class, 'index'])->name('painters-page.index');
        Route::put('painters-page', [PaintersPageController::class, 'update'])->name('painters-page.update');
    });

    // Settings
    Route::middleware('permission:settings.view')->group(function () {
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    });
    Route::middleware('permission:settings.edit')->group(function () {
        Route::put('settings/homepage', [SettingController::class, 'updateHomepage'])->name('settings.update.homepage');
        Route::put('settings/weekly-movies', [SettingController::class, 'updateWeeklyMovies'])->name('settings.update.weekly-movies');
        Route::put('settings/general', [SettingController::class, 'updateGeneral'])->name('settings.update.general');
        Route::put('settings/contact', [SettingController::class, 'updateContact'])->name('settings.update.contact');
        Route::put('settings/social', [SettingController::class, 'updateSocial'])->name('settings.update.social');
        Route::put('settings/seo', [SettingController::class, 'updateSeo'])->name('settings.update.seo');
        Route::put('settings/smtp', [SettingController::class, 'updateSmtp'])->name('settings.update.smtp');
        Route::put('settings/recaptcha', [SettingController::class, 'updateRecaptcha'])->name('settings.update.recaptcha');
        Route::put('settings/maintenance', [SettingController::class, 'updateMaintenance'])->name('settings.update.maintenance');
        Route::get('settings/remove-logo', [SettingController::class, 'removeLogo'])->name('settings.remove-logo');
        Route::get('settings/remove-favicon', [SettingController::class, 'removeFavicon'])->name('settings.remove-favicon');
        Route::get('settings/remove-mail-logo', [SettingController::class, 'removeMailLogo'])->name('settings.remove-mail-logo');
        Route::get('settings/clear-cache', [SettingController::class, 'clearCache'])->name('settings.clear-cache');
        Route::post('settings/send-test-mail', [SettingController::class, 'sendTestMail'])->name('settings.send-test-mail');
        Route::put('settings/mail-theme', [SettingController::class, 'updateMailTheme'])->name('settings.update.mail-theme');
        Route::post('settings/mail-theme/preview', [SettingController::class, 'previewMailTheme'])->name('settings.mail-theme.preview');
        Route::get('settings/mail-theme/reset', [SettingController::class, 'resetMailTheme'])->name('settings.mail-theme.reset');
    });

    // Comment Management
    Route::middleware('permission:comments.view')->group(function () {
        Route::get('comments', [AdminCommentController::class, 'index'])->name('comments.index');
        Route::get('comments/{comment}', [AdminCommentController::class, 'show'])->name('comments.show');
    });
    Route::middleware('permission:comments.edit')->group(function () {
        Route::get('comments/{comment}/edit', [AdminCommentController::class, 'edit'])->name('comments.edit');
        Route::put('comments/{comment}', [AdminCommentController::class, 'update'])->name('comments.update');
    });
    Route::middleware('permission:comments.moderate')->group(function () {
        Route::patch('comments/{comment}/approve', [AdminCommentController::class, 'approve'])->name('comments.approve');
        Route::patch('comments/{comment}/reject', [AdminCommentController::class, 'reject'])->name('comments.reject');
    });
    Route::delete('comments/{comment}', [AdminCommentController::class, 'destroy'])->name('comments.destroy')->middleware('permission:comments.delete');

    // Contact Messages
    Route::middleware('permission:contacts.view')->group(function () {
        Route::get('contacts', [AdminContactController::class, 'index'])->name('contacts.index');
        Route::patch('contacts/mark-all-read', [AdminContactController::class, 'markAllRead'])->name('contacts.mark-all-read');
        Route::get('contacts/{id}', [AdminContactController::class, 'show'])->name('contacts.show')->where('id', '[0-9]+');
        Route::patch('contacts/{id}/star', [AdminContactController::class, 'toggleStar'])->name('contacts.star')->where('id', '[0-9]+');
        Route::patch('contacts/{id}/archive', [AdminContactController::class, 'archive'])->name('contacts.archive')->where('id', '[0-9]+');
    });
    Route::post('contacts/{id}/reply', [AdminContactController::class, 'reply'])->name('contacts.reply')->where('id', '[0-9]+')->middleware('permission:contacts.reply');
    Route::delete('contacts/{id}', [AdminContactController::class, 'destroy'])->name('contacts.destroy')->where('id', '[0-9]+')->middleware('permission:contacts.delete');

    // Mail Logs
    Route::middleware('permission:mail-logs.view')->group(function () {
        Route::get('mail-logs', [MailLogController::class, 'index'])->name('mail-logs.index');
        Route::get('mail-logs/{mailLog}', [MailLogController::class, 'show'])->name('mail-logs.show');
    });
    Route::post('mail-logs/{mailLog}/resend', [MailLogController::class, 'resend'])->name('mail-logs.resend')->middleware('permission:mail-logs.view');
    Route::delete('mail-logs/{mailLog}', [MailLogController::class, 'destroy'])->name('mail-logs.destroy')->middleware('permission:mail-logs.delete');

    // Mail Templates (Mail Şablonları)
    Route::middleware('permission:mail-templates.view')->group(function () {
        Route::get('mail-templates', [MailTemplateController::class, 'index'])->name('mail-templates.index');
        Route::get('mail-templates/{mailTemplate}/edit', [MailTemplateController::class, 'edit'])->name('mail-templates.edit');
    });
    Route::middleware('permission:mail-templates.edit')->group(function () {
        Route::get('mail-templates/reset-all', [MailTemplateController::class, 'resetAll'])->name('mail-templates.reset-all');
        Route::put('mail-templates/{mailTemplate}', [MailTemplateController::class, 'update'])->name('mail-templates.update');
        Route::get('mail-templates/{mailTemplate}/reset', [MailTemplateController::class, 'reset'])->name('mail-templates.reset');
    });

    // Home Slider Management (Ana Sayfa Slider)
    Route::middleware('permission:home-sliders.view')->group(function () {
        Route::get('home-sliders', [HomeSliderController::class, 'index'])->name('home-sliders.index');
    });
    Route::get('home-sliders/create', [HomeSliderController::class, 'create'])->name('home-sliders.create')->middleware('permission:home-sliders.create');
    Route::post('home-sliders', [HomeSliderController::class, 'store'])->name('home-sliders.store')->middleware('permission:home-sliders.create');
    Route::middleware('permission:home-sliders.edit')->group(function () {
        Route::get('home-sliders/{home_slider}/edit', [HomeSliderController::class, 'edit'])->name('home-sliders.edit');
        Route::put('home-sliders/{home_slider}', [HomeSliderController::class, 'update'])->name('home-sliders.update');
        Route::patch('home-sliders/{home_slider}', [HomeSliderController::class, 'update'])->name('home-sliders.update.patch');
        Route::post('home-sliders/update-order', [HomeSliderController::class, 'updateOrder'])->name('home-sliders.update-order');
    });
    Route::delete('home-sliders/{home_slider}', [HomeSliderController::class, 'destroy'])->name('home-sliders.destroy')->middleware('permission:home-sliders.delete');

    // Advertisement Management
    Route::middleware('permission:advertisements.view')->group(function () {
        Route::get('advertisements', [AdvertisementController::class, 'index'])->name('advertisements.index');
    });
    Route::get('advertisements/create', [AdvertisementController::class, 'create'])->name('advertisements.create')->middleware('permission:advertisements.create');
    Route::post('advertisements', [AdvertisementController::class, 'store'])->name('advertisements.store')->middleware('permission:advertisements.create');
    Route::get('advertisements/{advertisement}/edit', [AdvertisementController::class, 'edit'])->name('advertisements.edit')->middleware('permission:advertisements.edit');
    Route::put('advertisements/{advertisement}', [AdvertisementController::class, 'update'])->name('advertisements.update')->middleware('permission:advertisements.edit');
    Route::patch('advertisements/{advertisement}', [AdvertisementController::class, 'update'])->name('advertisements.update.patch')->middleware('permission:advertisements.edit');
    Route::delete('advertisements/{advertisement}', [AdvertisementController::class, 'destroy'])->name('advertisements.destroy')->middleware('permission:advertisements.delete');

    // Poll Management (Anket Yönetimi)
    Route::middleware('permission:polls.view')->group(function () {
        Route::get('polls', [AdminPollController::class, 'index'])->name('polls.index');
        Route::get('polls/{id}/results', [AdminPollController::class, 'results'])->name('polls.results')->where('id', '[0-9]+');
    });
    Route::get('polls/create', [AdminPollController::class, 'create'])->name('polls.create')->middleware('permission:polls.create');
    Route::post('polls', [AdminPollController::class, 'store'])->name('polls.store')->middleware('permission:polls.create');
    Route::get('polls/{id}/edit', [AdminPollController::class, 'edit'])->name('polls.edit')->where('id', '[0-9]+')->middleware('permission:polls.edit');
    Route::put('polls/{id}', [AdminPollController::class, 'update'])->name('polls.update')->where('id', '[0-9]+')->middleware('permission:polls.edit');
    Route::patch('polls/{id}/toggle-active', [AdminPollController::class, 'toggleActive'])->name('polls.toggle-active')->where('id', '[0-9]+')->middleware('permission:polls.edit');
    Route::delete('polls/{id}', [AdminPollController::class, 'destroy'])->name('polls.destroy')->where('id', '[0-9]+')->middleware('permission:polls.delete');

    // Daily Question Management (Günün Sorusu)
    Route::middleware('permission:daily-questions.view')->group(function () {
        Route::get('daily-questions', [AdminDailyQuestionController::class, 'index'])->name('daily-questions.index');
        Route::get('daily-questions/{id}/answers', [AdminDailyQuestionController::class, 'answers'])->name('daily-questions.answers')->where('id', '[0-9]+');
    });
    Route::get('daily-questions/create', [AdminDailyQuestionController::class, 'create'])->name('daily-questions.create')->middleware('permission:daily-questions.create');
    Route::post('daily-questions', [AdminDailyQuestionController::class, 'store'])->name('daily-questions.store')->middleware('permission:daily-questions.create');
    Route::get('daily-questions/{id}/edit', [AdminDailyQuestionController::class, 'edit'])->name('daily-questions.edit')->where('id', '[0-9]+')->middleware('permission:daily-questions.edit');
    Route::put('daily-questions/{id}', [AdminDailyQuestionController::class, 'update'])->name('daily-questions.update')->where('id', '[0-9]+')->middleware('permission:daily-questions.edit');
    Route::delete('daily-questions/{id}', [AdminDailyQuestionController::class, 'destroy'])->name('daily-questions.destroy')->where('id', '[0-9]+')->middleware('permission:daily-questions.delete');
    Route::delete('daily-questions/{questionId}/answers/{answerId}', [AdminDailyQuestionController::class, 'destroyAnswer'])->name('daily-questions.answers.destroy')->where(['questionId' => '[0-9]+', 'answerId' => '[0-9]+'])->middleware('permission:daily-questions.delete');

    // Söz Meydanı — QnA Category Management
    Route::middleware('permission:qna-categories.view')->group(function () {
        Route::get('soz-meydani/kategoriler', [QnaCategoryController::class, 'index'])->name('qna.categories.index');
    });
    Route::get('soz-meydani/kategoriler/olustur', [QnaCategoryController::class, 'create'])->name('qna.categories.create')->middleware('permission:qna-categories.create');
    Route::post('soz-meydani/kategoriler', [QnaCategoryController::class, 'store'])->name('qna.categories.store')->middleware('permission:qna-categories.create');
    Route::get('soz-meydani/kategoriler/{id}/duzenle', [QnaCategoryController::class, 'edit'])->name('qna.categories.edit')->where('id', '[0-9]+')->middleware('permission:qna-categories.edit');
    Route::put('soz-meydani/kategoriler/{id}', [QnaCategoryController::class, 'update'])->name('qna.categories.update')->where('id', '[0-9]+')->middleware('permission:qna-categories.edit');
    Route::delete('soz-meydani/kategoriler/{id}', [QnaCategoryController::class, 'destroy'])->name('qna.categories.destroy')->where('id', '[0-9]+')->middleware('permission:qna-categories.delete');

    // Söz Meydanı — QnA Question Management
    Route::middleware('permission:qna.view')->group(function () {
        Route::get('soz-meydani/sorular', [AdminQnaQuestionController::class, 'index'])->name('qna.questions.index');
        Route::get('soz-meydani/sorular/{id}', [AdminQnaQuestionController::class, 'show'])->name('qna.questions.show')->where('id', '[0-9]+');
    });
    Route::middleware('permission:qna.approve')->group(function () {
        Route::patch('soz-meydani/sorular/{id}/onayla', [AdminQnaQuestionController::class, 'approve'])->name('qna.questions.approve')->where('id', '[0-9]+');
        Route::patch('soz-meydani/sorular/{id}/reddet', [AdminQnaQuestionController::class, 'reject'])->name('qna.questions.reject')->where('id', '[0-9]+');
    });
    Route::delete('soz-meydani/sorular/{id}', [AdminQnaQuestionController::class, 'destroy'])->name('qna.questions.destroy')->where('id', '[0-9]+')->middleware('permission:qna.delete');

    // Söz Meydanı — QnA Answer Management
    Route::middleware('permission:qna.view')->group(function () {
        Route::get('soz-meydani/cevaplar', [AdminQnaAnswerController::class, 'index'])->name('qna.answers.index');
    });
    Route::middleware('permission:qna.approve')->group(function () {
        Route::patch('soz-meydani/cevaplar/{id}/onayla', [AdminQnaAnswerController::class, 'approve'])->name('qna.answers.approve')->where('id', '[0-9]+');
        Route::patch('soz-meydani/cevaplar/{id}/reddet', [AdminQnaAnswerController::class, 'reject'])->name('qna.answers.reject')->where('id', '[0-9]+');
    });
    Route::delete('soz-meydani/cevaplar/{id}', [AdminQnaAnswerController::class, 'destroy'])->name('qna.answers.destroy')->where('id', '[0-9]+')->middleware('permission:qna.delete');

    // Role & Permission Management
    Route::middleware('permission:roles.view')->group(function () {
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
    });
    Route::post('roles', [RoleController::class, 'store'])->name('roles.store')->middleware('permission:roles.create');
    Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update')->middleware('permission:roles.edit');
    Route::put('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update')->middleware('permission:roles.edit');
    Route::post('roles/assign', [RoleController::class, 'assignRole'])->name('roles.assign')->middleware('permission:roles.assign');
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('permission:roles.delete');

    // Author Statistics (Yazar İstatistikleri)
    Route::middleware('permission:author-statistics.view')->group(function () {
        Route::get('author-statistics', [AuthorStatisticsController::class, 'index'])->name('author-statistics.index');
        Route::get('author-statistics/{user}', [AuthorStatisticsController::class, 'show'])->name('author-statistics.show');
    });

    // Literary Category Management (Edebiyat Kategorileri)
    Route::middleware('permission:literary-categories.view')->group(function () {
        Route::get('literary-categories', [LiteraryCategoryController::class, 'index'])->name('literary-categories.index');
    });
    Route::get('literary-categories/create', [LiteraryCategoryController::class, 'create'])->name('literary-categories.create')->middleware('permission:literary-categories.create');
    Route::post('literary-categories', [LiteraryCategoryController::class, 'store'])->name('literary-categories.store')->middleware('permission:literary-categories.create');
    Route::get('literary-categories/{literary_category}/edit', [LiteraryCategoryController::class, 'edit'])->name('literary-categories.edit')->middleware('permission:literary-categories.edit');
    Route::put('literary-categories/{literary_category}', [LiteraryCategoryController::class, 'update'])->name('literary-categories.update')->middleware('permission:literary-categories.edit');
    Route::patch('literary-categories/{literary_category}', [LiteraryCategoryController::class, 'update'])->name('literary-categories.update.patch')->middleware('permission:literary-categories.edit');
    Route::delete('literary-categories/{literary_category}', [LiteraryCategoryController::class, 'destroy'])->name('literary-categories.destroy')->middleware('permission:literary-categories.delete');

    // Literary Work Management (Edebiyat Eserleri)
    Route::middleware('permission:literary-works.view')->group(function () {
        Route::get('literary-works', [LiteraryWorkController::class, 'index'])->name('literary-works.index');
        Route::get('literary-works/{id}', [LiteraryWorkController::class, 'show'])->name('literary-works.show')->where('id', '[0-9]+');
    });
    Route::middleware('permission:literary-works.edit')->group(function () {
        Route::get('literary-works/{literaryWork}/edit', [LiteraryWorkController::class, 'edit'])->name('literary-works.edit');
        Route::put('literary-works/{literaryWork}', [LiteraryWorkController::class, 'update'])->name('literary-works.update');
    });
    Route::middleware('permission:literary-works.moderate')->group(function () {
        Route::patch('literary-works/{literaryWork}/approve', [LiteraryWorkController::class, 'approve'])->name('literary-works.approve');
        Route::patch('literary-works/{literaryWork}/reject', [LiteraryWorkController::class, 'reject'])->name('literary-works.reject');
        Route::patch('literary-works/{literaryWork}/unpublish', [LiteraryWorkController::class, 'unpublish'])->name('literary-works.unpublish');
        Route::post('literary-works/{literaryWork}/revision', [LiteraryWorkController::class, 'requestRevision'])->name('literary-works.revision');
    });
    Route::delete('literary-works/{literaryWork}', [LiteraryWorkController::class, 'destroy'])->name('literary-works.destroy')->middleware('permission:literary-works.delete');
});

// Poll (Frontend — Anket)
Route::post('/anket/vote', [FrontPollController::class, 'vote'])->name('poll.vote')->middleware('throttle:10,1');
Route::get('/anket/{pollId}/results', [FrontPollController::class, 'results'])->name('poll.results')->where('pollId', '[0-9]+');

// Daily Question (Frontend — Günün Sorusu)
Route::post('/gunun-sorusu/cevapla', [FrontDailyQuestionController::class, 'answer'])->name('daily-question.answer')->middleware('throttle:10,1');

// Comments (Frontend)
Route::post('/yorum', [CommentController::class, 'store'])->name('comment.store')->middleware('throttle:5,1');

// Contact (Frontend)
Route::get('/iletisim', [ContactController::class, 'show'])->name('contact.show');
Route::post('/iletisim', [ContactController::class, 'store'])->name('contact.store')->middleware('throttle:5,1');

// Literary Works (Frontend — İçerikler)
Route::get('/icerikler', [FrontLiteraryWorkController::class, 'index'])->name('literary-works.index');
Route::get('/yazili-eserler', [FrontLiteraryWorkController::class, 'writtenWorks'])->name('literary-works.written');
Route::get('/gorsel-eserler', [FrontLiteraryWorkController::class, 'visualWorks'])->name('literary-works.visual');
Route::get('/icerik/{slug}', [FrontLiteraryWorkController::class, 'show'])->name('literary-works.show');

// Authors (Frontend — Yazarlar)
Route::get('/yazarlar', [AuthorController::class, 'index'])->name('authors.index');
Route::get('/yazarlar/altin-kalem/{yearMonth}', [AuthorController::class, 'goldenPenMonth'])
    ->name('authors.golden-pen-month')
    ->where('yearMonth', '\d{4}-\d{2}');

Route::get('/ressamlar', [PainterController::class, 'index'])->name('painters.index');
Route::get('/ressamlar/altin-firca/{yearMonth}', [PainterController::class, 'goldenBrushMonth'])
    ->name('painters.golden-brush-month')
    ->where('yearMonth', '\d{4}-\d{2}');
Route::redirect('/ressamlarimiz', '/ressamlar', 301);

// Search (Frontend — Arama)
Route::get('/ara', [SearchController::class, 'index'])->name('search.index');

// Category (Frontend — Kategori Sayfası)
Route::get('/kategori/{slug}', [FrontCategoryController::class, 'show'])->name('category.show')->where('slug', '[a-z0-9\-]+');

// Söz Meydanı (Frontend — Soru/Cevap)
Route::get('/soz-meydani', [QnaController::class, 'index'])->name('qna.index');
Route::get('/soz-meydani/{categorySlug}', [QnaController::class, 'category'])->name('qna.category')->where('categorySlug', '[a-z0-9\-]+');
Route::get('/soz-meydani/{categorySlug}/{questionSlug}', [QnaController::class, 'show'])->name('qna.show')->where(['categorySlug' => '[a-z0-9\-]+', 'questionSlug' => '[a-z0-9\-]+']);
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/soz-meydani/soru-sor', [QnaController::class, 'storeQuestion'])->name('qna.store-question')->middleware('throttle:3,1');
    Route::post('/soz-meydani/cevap-yaz/{question}', [QnaController::class, 'storeAnswer'])->name('qna.store-answer')->middleware('throttle:10,1');
    Route::post('/soz-meydani/begen', [QnaController::class, 'toggleLike'])->name('qna.toggle-like')->middleware('throttle:30,1');
});

// Sitemap & RSS Feeds
Route::get('/sitemap.xml', [App\Http\Controllers\Front\SitemapController::class, 'index'])->name('sitemap');
Route::get('/feed/icerikler', [App\Http\Controllers\Front\RssFeedController::class, 'literaryWorks'])->name('feed.literary-works');
Route::get('/feed/blog', [App\Http\Controllers\Front\RssFeedController::class, 'blog'])->name('feed.blog');

// Static Pages (catch-all — MUST be LAST route)
Route::get('/{slug}', [PageController::class, 'show'])->name('page.show')->where('slug', '[a-z0-9\-]+');
