@extends('layouts.admin')

@section('title', 'Yeni Günün Sorusu — Admin')

@section('content')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb breadcrumb-reset fs-13">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link"><i class="bi bi-house me-1"></i>Ana Sayfa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.daily-questions.index') }}" class="breadcrumb-link">Günün Sorusu</a></li>
            <li class="breadcrumb-item active text-teal">Yeni Soru</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4" data-aos="fade-down">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.daily-questions.index') }}" class="btn-glass" title="Geri Dön"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h1 class="page-title">Yeni Soru Oluştur</h1>
                <p class="page-subtitle">Anasayfada gösterilecek günün sorusunu ekleyin</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.daily-questions.store') }}">
        @csrf
        @include('admin.daily-questions._form')
    </form>

@endsection
