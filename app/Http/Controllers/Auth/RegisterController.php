<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function showRegisterForm(): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route('home');
        }

        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        try {
            $this->authService->register($request);
        } catch (UniqueConstraintViolationException) {
            return redirect()->route('register')
                ->with('error', 'Bu e-posta adresi zaten kayıtlı. Lütfen farklı bir e-posta adresi deneyin veya giriş yapın.')
                ->withInput($request->except('password', 'password_confirmation'));
        }

        return redirect()->route('register')
            ->with('success', 'Kayıt işleminiz başarıyla tamamlandı! E-posta adresinize gönderilen doğrulama linkine tıklayarak hesabınızı aktif hale getirebilirsiniz.');
    }
}
