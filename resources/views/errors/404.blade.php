@extends('layouts.front')

@section('title', 'Sayfa Bulunamadı — Boyalı Kelimeler')

@section('content')
<section class="error-page">
    <div class="container">
        <div class="error-page__content">
            <span class="error-page__code">404</span>
            <h1 class="error-page__title">Sayfa Bulunamadı</h1>
            <p class="error-page__text">Aradığınız sayfa taşınmış, silinmiş veya hiç var olmamış olabilir.</p>
            <a href="{{ url('/') }}" class="error-page__btn">
                <i class="fa-solid fa-house me-2"></i>Ana Sayfaya Dön
            </a>
        </div>
    </div>
</section>
@endsection
