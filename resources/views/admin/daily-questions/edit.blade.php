@extends('layouts.admin')

@section('title', 'Günün Sorusu Düzenle — Admin')

@section('content')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb breadcrumb-reset fs-13">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link"><i class="bi bi-house me-1"></i>Ana Sayfa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.daily-questions.index') }}" class="breadcrumb-link">Günün Sorusu</a></li>
            <li class="breadcrumb-item active text-teal">Düzenle</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4" data-aos="fade-down">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.daily-questions.index') }}" class="btn-glass" title="Geri Dön"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h1 class="page-title">Soruyu Düzenle</h1>
                <p class="page-subtitle">{{ Str::limit($question->question_text, 60) }}</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.daily-questions.update', $question) }}">
        @csrf
        @method('PUT')
        @include('admin.daily-questions._form')
    </form>

@endsection
