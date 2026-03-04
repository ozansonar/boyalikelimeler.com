@extends('layouts.admin')

@section('title', 'Yeni Sayfa Oluştur — Admin')

@section('content')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link"><i class="bi bi-house me-1"></i>Ana Sayfa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.pages.index') }}" class="breadcrumb-link">Sayfalar</a></li>
            <li class="breadcrumb-item active text-teal">Yeni Sayfa Ekle</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header d-flex align-items-start align-items-sm-center justify-content-between flex-column flex-sm-row gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.pages.index') }}" class="btn-glass" title="Geri Dön"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h1 class="page-title mb-0">Yeni Sayfa Oluştur</h1>
                <p class="page-subtitle mb-0">Hakkımızda, Gizlilik Politikası gibi statik sayfalar ekleyin</p>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn-teal" onclick="syncEditor(); document.getElementById('pageForm').submit()">
                <i class="bi bi-send me-1"></i>Kaydet
            </button>
        </div>
    </div>

    <form id="pageForm" method="POST" action="{{ route('admin.pages.store') }}" enctype="multipart/form-data" onsubmit="syncEditor()">
        @csrf
        @include('admin.pages._form')
    </form>

@endsection

@push('scripts')
<script src="{{ asset('assets/admin/js/content-add.js') }}"></script>
<script src="{{ asset('assets/admin/js/pages.js') }}"></script>
<script src="{{ asset('assets/admin/js/page-boxes.js') }}"></script>
@endpush
