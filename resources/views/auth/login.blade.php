@extends('layouts.front')

@section('title', 'Giriş Yap — Boyalı Kelimeler')
@section('meta_description', 'Boyalı Kelimeler hesabınıza giriş yapın. Yazmaya, çizmeye, paylaşmaya devam edin.')
@section('canonical', url('/giris'))
@section('og_title', 'Giriş Yap — Boyalı Kelimeler')
@section('og_description', 'Boyalı Kelimeler hesabınıza giriş yapın.')
@section('robots', 'noindex, nofollow')

@section('content')
<section class="login-section" aria-label="Giriş formu">
    <div class="container">
        <div class="row justify-content-center align-items-center g-5">

            <!-- Sol: Hoş Geldiniz Metni -->
            <div class="col-lg-6 d-none d-lg-block">
                <div class="login-welcome">
                    <h1 class="login-welcome__title">Tekrar Hoş Geldiniz</h1>
                    <div class="login-welcome__divider"></div>
                    <p class="login-welcome__text">
                        Kelimelerin boyandığı, fırçaların konuştuğu dünyamıza geri dönün.
                        Yazılarınız, eserleriniz ve topluluğunuz sizi bekliyor.
                    </p>

                    <div class="login-welcome__stats">
                        <div class="login-welcome__stat">
                            <span class="login-welcome__stat-number">{{ number_format($activeAuthorCount, 0, ',', '.') }}+</span>
                            <span class="login-welcome__stat-label">Aktif Sanatçı</span>
                        </div>
                        <div class="login-welcome__stat">
                            <span class="login-welcome__stat-number">{{ number_format($totalWorkCount, 0, ',', '.') }}+</span>
                            <span class="login-welcome__stat-label">Eser</span>
                        </div>
                        <div class="login-welcome__stat">
                            <span class="login-welcome__stat-number">{{ number_format($painterCount, 0, ',', '.') }}+</span>
                            <span class="login-welcome__stat-label">Ressam</span>
                        </div>
                    </div>

                    <blockquote class="login-welcome__quote">
                        <i class="fa-solid fa-quote-left login-welcome__quote-icon"></i>
                        <p class="login-welcome__quote-text">"Yazmak, ruhun nefes almasıdır."</p>
                        <span class="login-welcome__quote-author">— Boyalı Kelimeler</span>
                    </blockquote>
                </div>
            </div>

            <!-- Sağ: Giriş Formu -->
            <div class="col-lg-5 col-md-8">
                <div class="login-card">
                    <div class="login-card__header">
                        <div class="login-card__icon">
                            <i class="fa-solid fa-right-to-bracket"></i>
                        </div>
                        <h2 class="login-card__title">Giriş Yap</h2>
                        <p class="login-card__subtitle">Hesabınıza erişin</p>
                    </div>

                    @if ($errors->any())
                        <div class="auth-form__alert auth-form__alert--error mb-3" role="alert">
                            @foreach ($errors->all() as $error)
                                <p class="mb-0">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form class="auth-form" method="POST" action="{{ route('login') }}" novalidate>
                        @csrf

                        <div class="auth-form__group">
                            <label class="auth-form__label" for="email">
                                <i class="fa-solid fa-envelope me-1"></i>E-posta
                            </label>
                            <input type="email"
                                   class="auth-form__input @error('email') auth-form__input--error @enderror"
                                   id="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   placeholder="ornek@email.com"
                                   required
                                   autocomplete="email"
                                   autofocus>
                            @error('email')
                                <span class="auth-form__error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="auth-form__group">
                            <label class="auth-form__label" for="password">
                                <i class="fa-solid fa-lock me-1"></i>Şifre
                            </label>
                            <div class="auth-form__password-wrap">
                                <input type="password"
                                       class="auth-form__input @error('password') auth-form__input--error @enderror"
                                       id="password"
                                       name="password"
                                       placeholder="Şifrenizi girin"
                                       required
                                       autocomplete="current-password">
                                <button type="button" class="auth-form__eye" aria-label="Şifreyi göster/gizle">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <span class="auth-form__error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="login-card__options">
                            <div class="auth-form__check mb-0">
                                <input type="checkbox"
                                       class="auth-form__checkbox"
                                       id="remember"
                                       name="remember"
                                       {{ old('remember') ? 'checked' : '' }}>
                                <label class="auth-form__check-label" for="remember">Beni hatırla</label>
                            </div>
                            <a href="{{ route('password.request') }}" class="auth-form__link login-card__forgot">Şifremi Unuttum</a>
                        </div>

                        <x-recaptcha />

                        <button type="submit" class="auth-form__submit">
                            <i class="fa-solid fa-right-to-bracket me-2"></i>Giriş Yap
                        </button>
                    </form>

                    <div class="login-card__footer">
                        <p class="login-card__footer-text">
                            Hesabınız yok mu?
                            <a href="{{ url('/register') }}" class="auth-form__link">Kayıt Ol</a>
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.querySelector('.auth-form__eye').addEventListener('click', function () {
        var input = document.getElementById('password');
        var icon = this.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });
</script>
@endpush
