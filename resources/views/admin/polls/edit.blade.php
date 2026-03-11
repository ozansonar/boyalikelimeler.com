@extends('layouts.admin')

@section('title', 'Anket Düzenle — Admin')

@section('content')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb breadcrumb-reset fs-13">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link"><i class="bi bi-house me-1"></i>Ana Sayfa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.polls.index') }}" class="breadcrumb-link">Anket Yönetimi</a></li>
            <li class="breadcrumb-item active text-teal">Düzenle</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4" data-aos="fade-down">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.polls.index') }}" class="btn-glass" title="Geri Dön"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h1 class="page-title">Anket Düzenle</h1>
                <p class="page-subtitle">{{ Str::limit($poll->question, 60) }}</p>
            </div>
        </div>
    </div>

    @if($poll->votes()->count() > 0)
        <div class="alert alert-warning d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle fs-5"></i>
            <div>
                Bu ankete <strong>{{ $poll->votes()->count() }}</strong> oy verilmiş.
                Şıkları değiştirirseniz mevcut oylar sıfırlanacaktır.
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.polls.update', $poll) }}">
        @csrf
        @method('PUT')
        @include('admin.polls._form')
    </form>

@endsection
