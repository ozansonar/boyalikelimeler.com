<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Rules\RecaptchaRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function showForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email'                => ['required', 'email'],
            'g-recaptcha-response' => ['sometimes', new RecaptchaRule()],
        ], [
            'email.required' => 'E-posta adresi zorunludur.',
            'email.email'    => 'Geçerli bir e-posta adresi giriniz.',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'Şifre sıfırlama linki e-posta adresinize gönderildi.');
        }

        return back()->with('error', 'Bu e-posta adresiyle kayıtlı bir kullanıcı bulunamadı.');
    }
}
