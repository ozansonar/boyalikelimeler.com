@extends('layouts.front')

@section('title', 'Şifre Sıfırla — Boyalı Kelimeler')
@section('meta_description', 'Yeni şifrenizi belirleyin ve hesabınıza erişin.')
@section('canonical', url()->current())
@section('og_title', 'Şifre Sıfırla — Boyalı Kelimeler')
@section('og_description', 'Yeni şifrenizi belirleyin.')

@section('content')
<section class="login-section" aria-label="Şifre sıfırlama formu">
    <div class="container">
        <div class="row justify-content-center align-items-center g-5">

            <!-- Sol: Bilgilendirme -->
            <div class="col-lg-6 d-none d-lg-block">
                <div class="login-welcome">
                    <h1 class="login-welcome__title">Yeni Şifrenizi<br>Belirleyin</h1>
                    <div class="login-welcome__divider"></div>
                    <p class="login-welcome__text">
                        Güçlü bir şifre seçin ve hesabınıza tekrar erişim sağlayın.
                        Güvenliğiniz için en az 8 karakter kullanmanızı öneriyoruz.
                    </p>

                    <div class="login-welcome__features">
                        <div class="login-welcome__feature-item">
                            <div class="login-welcome__feature-icon">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <div>
                                <h4 class="login-welcome__feature-title">Güçlü Şifre</h4>
                                <p class="login-welcome__feature-text">En az 8 karakter, harf ve rakam karışımı önerilir</p>
                            </div>
                        </div>
                        <div class="login-welcome__feature-item">
                            <div class="login-welcome__feature-icon">
                                <i class="fa-solid fa-lock"></i>
                            </div>
                            <div>
                                <h4 class="login-welcome__feature-title">Güvenli Bağlantı</h4>
                                <p class="login-welcome__feature-text">Şifreniz şifreli olarak saklanır</p>
                            </div>
                        </div>
                    </div>

                    <blockquote class="login-welcome__quote">
                        <i class="fa-solid fa-quote-left login-welcome__quote-icon"></i>
                        <p class="login-welcome__quote-text">"Güvenlik, özgürlüğün temelidir."</p>
                        <span class="login-welcome__quote-author">— Boyalı Kelimeler</span>
                    </blockquote>
                </div>
            </div>

            <!-- Sağ: Form -->
            <div class="col-lg-5 col-md-8">
                <div class="login-card">
                    <div class="login-card__header">
                        <div class="login-card__icon">
                            <i class="fa-solid fa-key"></i>
                        </div>
                        <h2 class="login-card__title">Şifre Sıfırla</h2>
                        <p class="login-card__subtitle">Yeni şifrenizi belirleyin</p>
                    </div>

                    @if ($errors->any())
                        <div class="auth-form__alert auth-form__alert--error mb-3" role="alert">
                            @foreach ($errors->all() as $error)
                                <p class="mb-0">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form class="auth-form" method="POST" action="{{ route('password.update') }}" novalidate>
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="auth-form__group">
                            <label class="auth-form__label" for="email">
                                <i class="fa-solid fa-envelope me-1"></i>E-posta
                            </label>
                            <input type="email"
                                   class="auth-form__input @error('email') auth-form__input--error @enderror"
                                   id="email"
                                   name="email"
                                   value="{{ old('email', $email) }}"
                                   placeholder="ornek@email.com"
                                   required
                                   autocomplete="email"
                                   readonly>
                            @error('email')
                                <span class="auth-form__error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="auth-form__group">
                            <label class="auth-form__label" for="password">
                                <i class="fa-solid fa-lock me-1"></i>Yeni Şifre
                            </label>
                            <div class="auth-form__password-wrap">
                                <input type="password"
                                       class="auth-form__input @error('password') auth-form__input--error @enderror"
                                       id="password"
                                       name="password"
                                       placeholder="En az 8 karakter"
                                       required
                                       minlength="8"
                                       autocomplete="new-password"
                                       autofocus>
                                <button type="button" class="auth-form__eye" data-target="password" aria-label="Şifreyi göster/gizle">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <span class="auth-form__error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="auth-form__group">
                            <label class="auth-form__label" for="password_confirmation">
                                <i class="fa-solid fa-lock me-1"></i>Şifre Tekrar
                            </label>
                            <div class="auth-form__password-wrap">
                                <input type="password"
                                       class="auth-form__input"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       placeholder="Şifrenizi tekrar girin"
                                       required
                                       minlength="8"
                                       autocomplete="new-password">
                                <button type="button" class="auth-form__eye" data-target="password_confirmation" aria-label="Şifreyi göster/gizle">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="auth-form__submit">
                            <i class="fa-solid fa-check me-2"></i>Şifremi Sıfırla
                        </button>
                    </form>

                    <div class="login-card__footer">
                        <p class="login-card__footer-text">
                            <a href="{{ route('login') }}" class="auth-form__link">
                                <i class="fa-solid fa-arrow-left me-1"></i>Giriş sayfasına dön
                            </a>
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
    document.querySelectorAll('.auth-form__eye').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var targetId = this.getAttribute('data-target');
            var input = document.getElementById(targetId);
            var icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });
</script>
@endpush
