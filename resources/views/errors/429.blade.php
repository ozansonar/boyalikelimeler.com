@extends('layouts.front')

@section('title', 'Çok Fazla İstek — Boyalı Kelimeler')

@section('content')
<section class="error-page">
    <div class="container">
        <div class="error-page__content">
            <span class="error-page__code">429</span>
            <h1 class="error-page__title">Çok Fazla İstek</h1>
            <p class="error-page__text">Kısa sürede çok fazla istek gönderdiniz. Lütfen biraz bekleyip tekrar deneyin.</p>
            <a href="{{ url('/') }}" class="error-page__btn">
                <i class="fa-solid fa-house me-2"></i>Ana Sayfaya Dön
            </a>
        </div>
    </div>
</section>
@endsection
