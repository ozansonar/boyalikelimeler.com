@extends('layouts.front')

@section('title', 'Oturum Süresi Doldu — Boyalı Kelimeler')

@section('content')
<section class="error-page">
    <div class="container">
        <div class="error-page__content">
            <span class="error-page__code">419</span>
            <h1 class="error-page__title">Oturum Süresi Doldu</h1>
            <p class="error-page__text">Güvenlik nedeniyle oturumunuz zaman aşımına uğradı. Lütfen sayfayı yenileyip tekrar deneyin.</p>
            <a href="{{ url()->current() }}" class="error-page__btn">
                <i class="fa-solid fa-rotate-right me-2"></i>Sayfayı Yenile
            </a>
        </div>
    </div>
</section>
@endsection
