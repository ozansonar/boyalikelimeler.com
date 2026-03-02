@extends('layouts.admin')

@section('title', 'Yeni Menü Oluştur — Admin')

@section('content')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link"><i class="bi bi-house me-1"></i>Ana Sayfa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.menus.index') }}" class="breadcrumb-link">Menüler</a></li>
            <li class="breadcrumb-item active text-teal">Yeni Menü</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.menus.index') }}" class="btn-glass" title="Geri Dön"><i class="bi bi-arrow-left"></i></a>
        <div>
            <h1 class="page-title mb-0">Yeni Menü Oluştur</h1>
            <p class="page-subtitle mb-0">Navbar, footer veya özel bir menü grubu oluşturun</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('admin.menus.store') }}">
                @csrf
                @include('admin.menus._menu_form')
            </form>
        </div>
    </div>

@endsection
