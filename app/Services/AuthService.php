<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;

final class AuthService
{
    public function login(LoginRequest $request): bool
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return true;
        }

        return false;
    }

    public function logout(\Illuminate\Http\Request $request): void
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
