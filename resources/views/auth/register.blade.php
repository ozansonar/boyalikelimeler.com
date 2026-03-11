@extends('layouts.front')

@section('title', 'Kayıt Ol — Boyalı Kelimeler')
@section('meta_description', 'Boyalı Kelimeler topluluğuna katılın. Ücretsiz kayıt olun, yazın, çizin, paylaşın.')
@section('canonical', url('/kayit-ol'))
@section('og_title', 'Kayıt Ol — Boyalı Kelimeler')
@section('og_description', 'Boyalı Kelimeler topluluğuna katılın.')
@section('robots', 'noindex, nofollow')

@section('content')
<section class="login-section" aria-label="Kayıt formu">
    <div class="container">
        <div class="row justify-content-center align-items-center g-5">

            <!-- Sol: Topluluk Mesajı -->
            <div class="col-lg-6 d-none d-lg-block">
                <div class="login-welcome">
                    <h1 class="login-welcome__title">Topluluğa<br>Katılın</h1>
                    <div class="login-welcome__divider"></div>
                    <p class="login-welcome__text">
                        Sanat ve edebiyatın buluştuğu bu platforma ücretsiz katılın.
                        Yazın, çizin, paylaşın — sesinizi duyurun.
                    </p>

                    <div class="login-welcome__features">
                        <div class="login-welcome__feature-item">
                            <div class="login-welcome__feature-icon">
                                <i class="fa-solid fa-feather-pointed"></i>
                            </div>
                            <div>
                                <h4 class="login-welcome__feature-title">Yazın ve Paylaşın</h4>
                                <p class="login-welcome__feature-text">Şiir, hikaye, deneme — her tür eseri yayınlayın</p>
                            </div>
                        </div>
                        <div class="login-welcome__feature-item">
                            <div class="login-welcome__feature-icon">
                                <i class="fa-solid fa-paintbrush"></i>
                            </div>
                            <div>
                                <h4 class="login-welcome__feature-title">Eserlerinizi Sergileyin</h4>
                                <p class="login-welcome__feature-text">Resim galerinizi oluşturun, sanatınızı gösterin</p>
                            </div>
                        </div>
                        <div class="login-welcome__feature-item">
                            <div class="login-welcome__feature-icon">
                                <i class="fa-solid fa-award"></i>
                            </div>
                            <div>
                                <h4 class="login-welcome__feature-title">Yarışmalara Katılın</h4>
                                <p class="login-welcome__feature-text">Altın Kalem ve Altın Fırça yarışmalarıyla ödüller kazanın</p>
                            </div>
                        </div>
                        <div class="login-welcome__feature-item">
                            <div class="login-welcome__feature-icon">
                                <i class="fa-solid fa-users"></i>
                            </div>
                            <div>
                                <h4 class="login-welcome__feature-title">Toplulukla Tanışın</h4>
                                <p class="login-welcome__feature-text">Yazar ve ressamlarla etkileşime geçin</p>
                            </div>
                        </div>
                    </div>

                    <blockquote class="login-welcome__quote">
                        <i class="fa-solid fa-quote-left login-welcome__quote-icon"></i>
                        <p class="login-welcome__quote-text">"Sanat, ruhun gözle görülür halidir."</p>
                        <span class="login-welcome__quote-author">— Boyalı Kelimeler</span>
                    </blockquote>
                </div>
            </div>

            <!-- Sağ: Kayıt Formu -->
            <div class="col-lg-5 col-md-8">
                <div class="login-card">
                    <div class="login-card__header">
                        <div class="login-card__icon">
                            <i class="fa-solid fa-user-plus"></i>
                        </div>
                        <h2 class="login-card__title">Kayıt Ol</h2>
                        <p class="login-card__subtitle">Aşağıdaki bilgilerle hemen üye olabilirsiniz</p>
                    </div>

                    @if ($errors->any())
                        <div class="auth-form__alert auth-form__alert--error mb-3" role="alert">
                            @foreach ($errors->all() as $error)
                                <p class="mb-0">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form class="auth-form" method="POST" action="{{ route('register.post') }}" novalidate>
                        @csrf

                        <!-- Ad + Soyad -->
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="auth-form__group">
                                    <label class="auth-form__label" for="first_name">
                                        <i class="fa-solid fa-user me-1"></i>Ad
                                    </label>
                                    <input type="text"
                                           class="auth-form__input @error('first_name') auth-form__input--error @enderror"
                                           id="first_name"
                                           name="first_name"
                                           value="{{ old('first_name') }}"
                                           placeholder="Adınız"
                                           required
                                           autocomplete="given-name">
                                    @error('first_name')
                                        <span class="auth-form__error-text">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="auth-form__group">
                                    <label class="auth-form__label" for="last_name">
                                        <i class="fa-solid fa-user me-1"></i>Soyad
                                    </label>
                                    <input type="text"
                                           class="auth-form__input @error('last_name') auth-form__input--error @enderror"
                                           id="last_name"
                                           name="last_name"
                                           value="{{ old('last_name') }}"
                                           placeholder="Soyadınız"
                                           required
                                           autocomplete="family-name">
                                    @error('last_name')
                                        <span class="auth-form__error-text">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- E-posta -->
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
                                   autocomplete="email">
                            @error('email')
                                <span class="auth-form__error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Şifre -->
                        <div class="auth-form__group">
                            <label class="auth-form__label" for="password">
                                <i class="fa-solid fa-lock me-1"></i>Şifre
                            </label>
                            <div class="auth-form__password-wrap">
                                <input type="password"
                                       class="auth-form__input @error('password') auth-form__input--error @enderror"
                                       id="password"
                                       name="password"
                                       placeholder="En az 8 karakter"
                                       required
                                       minlength="8"
                                       autocomplete="new-password">
                                <button type="button" class="auth-form__eye" data-target="password" aria-label="Şifreyi göster/gizle">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <span class="auth-form__error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Şifre Tekrar -->
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

                        <!-- Koşullar -->
                        <div class="auth-form__check">
                            <input type="checkbox"
                                   class="auth-form__checkbox @error('terms') auth-form__input--error @enderror"
                                   id="terms"
                                   name="terms"
                                   value="1"
                                   required
                                   {{ old('terms') ? 'checked' : '' }}>
                            <label class="auth-form__check-label" for="terms">
                                <a href="{{ url('/kullanim-kosullari') }}" class="auth-form__link" target="_blank" rel="noopener noreferrer">Kullanım koşullarını</a> ve
                                <a href="{{ url('/gizlilik-politikasi') }}" class="auth-form__link" target="_blank" rel="noopener noreferrer">gizlilik politikasını</a> kabul ediyorum
                            </label>
                            @error('terms')
                                <span class="auth-form__error-text d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <x-recaptcha />

                        <button type="submit" class="auth-form__submit">
                            <i class="fa-solid fa-user-plus me-2"></i>Kayıt Ol
                        </button>
                    </form>

                    <div class="login-card__footer">
                        <p class="login-card__footer-text">
                            Zaten hesabınız var mı?
                            <a href="{{ route('login') }}" class="auth-form__link">Giriş Yap</a>
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
