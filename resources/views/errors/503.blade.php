@extends('layouts.front')

@section('title', 'Bakım Modu — Boyalı Kelimeler')

@section('content')
<section class="error-page">
    <div class="container">
        <div class="error-page__content">
            <span class="error-page__code">503</span>
            <h1 class="error-page__title">Bakım Çalışması</h1>
            <p class="error-page__text">{{ $maintenanceMessage ?? 'Sitemiz şu anda bakım çalışması nedeniyle geçici olarak kullanılamıyor. Kısa süre içinde tekrar hizmetinizdeyiz.' }}</p>
            <a href="{{ url('/') }}" class="error-page__btn">
                <i class="fa-solid fa-house me-2"></i>Ana Sayfaya Dön
            </a>
        </div>
    </div>
</section>
@endsection
