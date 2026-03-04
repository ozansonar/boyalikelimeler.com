@extends('layouts.front')

@section('title', 'Sunucu Hatası — Boyalı Kelimeler')

@section('content')
<section class="error-page">
    <div class="container">
        <div class="error-page__content">
            <span class="error-page__code">500</span>
            <h1 class="error-page__title">Sunucu Hatası</h1>
            <p class="error-page__text">Beklenmeyen bir hata oluştu. Teknik ekibimiz bilgilendirildi, lütfen daha sonra tekrar deneyin.</p>
            <a href="{{ url('/') }}" class="error-page__btn">
                <i class="fa-solid fa-house me-2"></i>Ana Sayfaya Dön
            </a>
        </div>
    </div>
</section>
@endsection
