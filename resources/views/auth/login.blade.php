@extends('layouts.app')

@section('title', 'Giriş Yap')

@section('content')
<div style="max-width: 400px; margin: 4rem auto;">
    <div style="background: white; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="text-align: center; margin-bottom: 1.5rem; font-size: 1.5rem;">Giriş Yap</h2>

        @if ($errors->any())
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 0.75rem 1rem; border-radius: 0.375rem; margin-bottom: 1rem; font-size: 0.875rem;">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div style="margin-bottom: 1rem;">
                <label for="email" style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.875rem;">E-posta</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                    style="width: 100%; padding: 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="password" style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.875rem;">Şifre</label>
                <input type="password" id="password" name="password" required
                    style="width: 100%; padding: 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem;">
                    <input type="checkbox" name="remember">
                    Beni hatırla
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem;">Giriş Yap</button>
        </form>
    </div>
</div>
@endsection
