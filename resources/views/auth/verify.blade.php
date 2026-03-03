@extends('layouts.front')

@section('title', 'E-posta Doğrulama — Boyalı Kelimeler')
@section('meta_description', 'E-posta adresinizi doğrulayın.')

@section('content')
<section class="login-section" aria-label="E-posta doğrulama">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="login-card">
                    <div class="login-card__header text-center">
                        <div class="login-card__icon mb-3">
                            <i class="fa-solid fa-envelope-circle-check fa-3x"></i>
                        </div>
                        <h1 class="login-card__title">E-posta Doğrulama</h1>
                        <p class="login-card__subtitle">
                            Devam etmeden önce e-posta adresinizi doğrulamanız gerekmektedir.
                            Kayıt olurken belirttiğiniz e-posta adresine bir doğrulama linki gönderildi.
                        </p>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
                        </div>
                    @endif

                    <p class="text-center mt-3 mb-3">
                        E-posta almadıysanız, aşağıdaki butona tıklayarak tekrar gönderebilirsiniz.
                    </p>

                    <form method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <input type="hidden" name="email" value="{{ auth()->user()->email }}">
                        <div class="d-grid">
                            <button type="submit" class="login-card__btn">
                                <i class="fa-solid fa-paper-plane me-2"></i>Doğrulama Linkini Tekrar Gönder
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
