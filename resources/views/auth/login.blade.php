@extends('layouts.guest')

@section('title', 'Giriş Yap — Boyalı Kelimeler')

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">
        <h1 class="auth-card__title">Boyalı Kelimeler</h1>
        <p class="auth-card__subtitle">Admin paneline giriş yapın</p>

        @if ($errors->any())
            <div class="alert alert-danger py-2 mb-3">
                @foreach ($errors->all() as $error)
                    <div class="small">{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label fw-medium">E-posta</label>
                <input type="email"
                       id="email"
                       name="email"
                       value="{{ old('email') }}"
                       class="form-control @error('email') is-invalid @enderror"
                       required
                       autofocus
                       autocomplete="email">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-medium">Şifre</label>
                <input type="password"
                       id="password"
                       name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       required
                       autocomplete="current-password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <div class="form-check">
                    <input type="checkbox" id="remember" name="remember" class="form-check-input">
                    <label for="remember" class="form-check-label small">Beni hatırla</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-box-arrow-in-right me-1"></i>Giriş Yap
            </button>
        </form>
    </div>
</div>
@endsection
