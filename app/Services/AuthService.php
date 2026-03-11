<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\RoleSlug;
use App\Enums\UserType;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Mail\NewUserRegisteredMail;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

    public function register(RegisterRequest $request): User
    {
        return DB::transaction(function () use ($request): User {
            $role = Role::where('slug', RoleSlug::Kullanici->value)->firstOrFail();

            $user = User::create([
                'name'     => $request->validated('first_name') . ' ' . $request->validated('last_name'),
                'email'    => $request->validated('email'),
                'password' => $request->validated('password'),
                'type'     => UserType::Kullanici,
                'role_id'  => $role->id,
            ]);

            $user->sendEmailVerificationNotification();

            $this->notifyAdmins($user);

            return $user;
        });
    }

    private function notifyAdmins(User $newUser): void
    {
        $admins = User::whereIn('type', [UserType::SuperAdmin, UserType::Admin])
            ->whereNotNull('email_verified_at')
            ->get();

        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email, $admin->name)
                    ->send(new NewUserRegisteredMail($newUser));
            } catch (\Throwable $e) {
                Log::error("Mail gönderilemedi [notifyAdmins] — Yeni kullanıcı #{$newUser->id}: {$e->getMessage()}");
            }
        }
    }

    public function logout(\Illuminate\Http\Request $request): void
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
