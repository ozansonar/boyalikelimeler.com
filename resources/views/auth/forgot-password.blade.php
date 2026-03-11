@extends('layouts.front')

@section('title', 'Şifremi Unuttum — Boyalı Kelimeler')
@section('meta_description', 'Şifrenizi mi unuttunuz? E-posta adresinize şifre sıfırlama linki gönderin.')
@section('canonical', url('/sifremi-unuttum'))
@section('og_title', 'Şifremi Unuttum — Boyalı Kelimeler')
@section('og_description', 'Şifrenizi sıfırlayın ve hesabınıza tekrar erişin.')

@section('content')
<section class="login-section" aria-label="Şifremi unuttum formu">
    <div class="container">
        <div class="row justify-content-center align-items-center g-5">

            <!-- Sol: Bilgilendirme -->
            <div class="col-lg-6 d-none d-lg-block">
                <div class="login-welcome">
                    <h1 class="login-welcome__title">Şifrenizi mi<br>Unuttunuz?</h1>
                    <div class="login-welcome__divider"></div>
                    <p class="login-welcome__text">
                        Endişelenmeyin! E-posta adresinizi girin, size şifre sıfırlama
                        linki gönderelim. Birkaç dakika içinde hesabınıza tekrar erişebilirsiniz.
                    </p>

                    <div class="login-welcome__features">
                        <div class="login-welcome__feature-item">
                            <div class="login-welcome__feature-icon">
                                <i class="fa-solid fa-envelope-circle-check"></i>
                            </div>
                            <div>
                                <h4 class="login-welcome__feature-title">E-posta Kontrolü</h4>
                                <p class="login-welcome__feature-text">Kayıtlı e-posta adresinizi girin</p>
                            </div>
                        </div>
                        <div class="login-welcome__feature-item">
                            <div class="login-welcome__feature-icon">
                                <i class="fa-solid fa-link"></i>
                            </div>
                            <div>
                                <h4 class="login-welcome__feature-title">Sıfırlama Linki</h4>
                                <p class="login-welcome__feature-text">Size özel link e-postanıza gönderilir</p>
                            </div>
                        </div>
                        <div class="login-welcome__feature-item">
                            <div class="login-welcome__feature-icon">
                                <i class="fa-solid fa-key"></i>
                            </div>
                            <div>
                                <h4 class="login-welcome__feature-title">Yeni Şifre</h4>
                                <p class="login-welcome__feature-text">Linke tıklayıp yeni şifrenizi belirleyin</p>
                            </div>
                        </div>
                    </div>

                    <blockquote class="login-welcome__quote">
                        <i class="fa-solid fa-quote-left login-welcome__quote-icon"></i>
                        <p class="login-welcome__quote-text">"Her yeni başlangıç, bir sıfırlama ile başlar."</p>
                        <span class="login-welcome__quote-author">— Boyalı Kelimeler</span>
                    </blockquote>
                </div>
            </div>

            <!-- Sağ: Form -->
            <div class="col-lg-5 col-md-8">
                <div class="login-card">
                    <div class="login-card__header">
                        <div class="login-card__icon">
                            <i class="fa-solid fa-unlock-keyhole"></i>
                        </div>
                        <h2 class="login-card__title">Şifremi Unuttum</h2>
                        <p class="login-card__subtitle">E-posta adresinizi girin, size sıfırlama linki gönderelim</p>
                    </div>

                    @if ($errors->any())
                        <div class="auth-form__alert auth-form__alert--error mb-3" role="alert">
                            @foreach ($errors->all() as $error)
                                <p class="mb-0">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form class="auth-form" method="POST" action="{{ route('password.email') }}" novalidate>
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

                        <x-recaptcha />

                        <button type="submit" class="auth-form__submit">
                            <i class="fa-solid fa-paper-plane me-2"></i>Sıfırlama Linki Gönder
                        </button>
                    </form>

                    <div class="login-card__footer">
                        <p class="login-card__footer-text">
                            Şifrenizi hatırladınız mı?
                            <a href="{{ route('login') }}" class="auth-form__link">Giriş Yap</a>
                        </p>
                        <p class="login-card__footer-text">
                            Hesabınız yok mu?
                            <a href="{{ route('register') }}" class="auth-form__link">Kayıt Ol</a>
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection
