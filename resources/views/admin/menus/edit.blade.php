@extends('layouts.admin')

@section('title', 'Menü Düzenle — Admin')

@section('content')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link"><i class="bi bi-house me-1"></i>Ana Sayfa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.menus.index') }}" class="breadcrumb-link">Menüler</a></li>
            <li class="breadcrumb-item active text-teal">{{ $menu->name }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.menus.index') }}" class="btn-glass" title="Geri Dön"><i class="bi bi-arrow-left"></i></a>
        <div>
            <h1 class="page-title mb-0">Menü Düzenle</h1>
            <p class="page-subtitle mb-0">{{ $menu->name }}</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('admin.menus.update', $menu) }}">
                @csrf
                @method('PUT')
                @include('admin.menus._menu_form')
            </form>
        </div>
    </div>

@endsection
