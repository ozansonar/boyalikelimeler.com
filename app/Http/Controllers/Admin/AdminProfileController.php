<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminPasswordUpdateRequest;
use App\Http\Requests\Admin\AdminProfileUpdateRequest;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminProfileController extends Controller
{
    public function __construct(
        private readonly ProfileService $profileService,
    ) {}

    public function index(): View
    {
        $user = Auth::user();

        return view('admin.profile.index', [
            'user' => $user,
        ]);
    }

    public function updateProfile(AdminProfileUpdateRequest $request): RedirectResponse
    {
        $user = Auth::user();

        $data = $request->validated();
        $data['notify_admin_mails'] = $request->boolean('notify_admin_mails');

        $this->profileService->updateProfile($user, $data);

        return redirect()->route('admin.profile.index')
            ->with('success', 'Profil bilgileri başarıyla güncellendi.');
    }

    public function updatePassword(AdminPasswordUpdateRequest $request): RedirectResponse
    {
        $user = Auth::user();

        $this->profileService->updatePassword(
            $user,
            $request->validated('current_password'),
            $request->validated('password'),
        );

        return redirect()->route('admin.profile.index')
            ->with('success', 'Şifreniz başarıyla güncellendi.');
    }

    public function updateAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,jpg,png,webp|max:2048',
        ], [
            'avatar.required' => 'Bir görsel seçin.',
            'avatar.image'    => 'Dosya bir görsel olmalıdır.',
            'avatar.mimes'    => 'Sadece JPEG, PNG ve WebP formatları desteklenir.',
            'avatar.max'      => 'Görsel en fazla 2 MB olabilir.',
        ]);

        $user = Auth::user();

        $path = $this->profileService->uploadAvatar($user, $request->file('avatar'));

        return response()->json([
            'success'    => true,
            'message'    => 'Avatar başarıyla güncellendi.',
            'avatar_url' => upload_url($path, 'thumb'),
        ]);
    }

    public function removeAvatar(): JsonResponse
    {
        $user = Auth::user();

        $this->profileService->removeAvatar($user);

        return response()->json([
            'success' => true,
            'message' => 'Avatar kaldırıldı.',
        ]);
    }
}
