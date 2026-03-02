<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function showLoginForm(): View|RedirectResponse
    {
        if (auth()->check()) {
            /** @var \App\Models\User $user */
            $user = auth()->user();

            return $user->isAdmin() || $user->isSuperAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('home');
        }

        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        if (! $this->authService->login($request)) {
            return back()->withErrors([
                'email' => 'Girdiğiniz bilgiler hatalı.',
            ])->onlyInput('email');
        }

        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (! $user->hasVerifiedEmail()) {
            $this->authService->logout($request);

            return redirect()->route('login')
                ->with('error', 'E-posta adresiniz henüz doğrulanmamış. Lütfen e-postanızı kontrol edin ve doğrulama linkine tıklayın.');
        }

        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('home'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->authService->logout($request);

        return redirect()->route('login')
            ->with('success', 'Başarıyla çıkış yapıldı.');
    }
}
