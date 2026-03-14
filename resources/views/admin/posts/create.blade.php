@extends('layouts.admin')

@section('title', 'Yeni İçerik Oluştur — Admin')

@section('content')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link"><i class="bi bi-house me-1"></i>Ana Sayfa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.posts.index') }}" class="breadcrumb-link">İçerikler</a></li>
            <li class="breadcrumb-item active text-teal">Yeni İçerik Ekle</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header d-flex align-items-start align-items-sm-center justify-content-between flex-column flex-sm-row gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.posts.index') }}" class="btn-glass" title="Geri Dön"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h1 class="page-title mb-0">Yeni İçerik Oluştur</h1>
                <p class="page-subtitle mb-0">Tüm alanları doldurarak yeni bir içerik yayınlayın</p>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn-teal" onclick="document.getElementById('postForm').submit()">
                <i class="bi bi-send me-1"></i>Yayınla
            </button>
        </div>
    </div>

    <form id="postForm" method="POST" action="{{ route('admin.posts.store') }}" enctype="multipart/form-data" onsubmit="syncEditor()">
        @csrf
        @include('admin.posts._form')
    </form>

@endsection

@push('scripts')
<script src="{{ asset('assets/admin/js/content-add.js') }}?v={{ filemtime(public_path('assets/admin/js/content-add.js')) }}"></script>
@endpush
