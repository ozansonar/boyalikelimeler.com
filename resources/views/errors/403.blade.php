@extends('layouts.front')

@section('title', 'Erişim Engellendi — Boyalı Kelimeler')

@section('content')
<section class="error-page">
    <div class="container">
        <div class="error-page__content">
            <span class="error-page__code">403</span>
            <h1 class="error-page__title">Erişim Engellendi</h1>
            <p class="error-page__text">{{ $exception->getMessage() ?: 'Bu sayfaya erişim yetkiniz bulunmuyor.' }}</p>
            <a href="{{ url('/') }}" class="error-page__btn">
                <i class="fa-solid fa-house me-2"></i>Ana Sayfaya Dön
            </a>
        </div>
    </div>
</section>
@endsection
