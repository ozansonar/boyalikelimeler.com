<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify(Request $request, int $id, string $hash): RedirectResponse
    {
        $user = User::findOrFail($id);

        if (! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return redirect()->route('login')
                ->with('error', 'Doğrulama linki geçersiz.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')
                ->with('success', 'E-posta adresiniz zaten doğrulanmış. Giriş yapabilirsiniz.');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->route('login')
            ->with('success', 'E-posta adresiniz başarıyla doğrulandı! Artık giriş yapabilirsiniz.');
    }

    public function resend(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if (! $user) {
            return back()->with('error', 'Bu e-posta adresiyle kayıtlı bir kullanıcı bulunamadı.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')
                ->with('success', 'E-posta adresiniz zaten doğrulanmış.');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('success', 'Doğrulama linki e-posta adresinize tekrar gönderildi.');
    }
}
