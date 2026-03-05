<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\PasswordChangeRequest;
use App\Http\Requests\Front\ProfileUpdateRequest;
use App\Models\User;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ProfileController extends Controller
{
    public function __construct(
        private readonly ProfileService $profileService,
    ) {}

    public function show(User $user): View
    {
        $data = $this->profileService->getProfileData($user);

        return view('front.profile.show', [
            'user'          => $user,
            'posts'         => $data['posts'],
            'works'         => $data['works'],
            'stats'         => $data['stats'],
            'favoriteWorks' => $data['favoriteWorks'],
            'favoritePosts' => $data['favoritePosts'],
        ]);
    }

    public function edit(): View
    {
        $user = auth()->user();

        return view('front.profile.edit', compact('user'));
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $this->profileService->updateProfile($user, $request->validated());

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Profil bilgileriniz güncellendi.');
    }

    public function updatePassword(PasswordChangeRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $updated = $this->profileService->updatePassword(
            $user,
            $request->validated('old_password'),
            $request->validated('password'),
        );

        if (! $updated) {
            return redirect()
                ->route('profile.edit')
                ->withErrors(['old_password' => 'Mevcut şifreniz yanlış.']);
        }

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Şifreniz başarıyla değiştirildi.');
    }

    public function updateAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
        ], [
            'avatar.max' => 'Profil resmi en fazla 1 MB olabilir.',
        ]);

        $user = auth()->user();
        $path = $this->profileService->uploadAvatar($user, $request->file('avatar'));

        return response()->json([
            'success' => true,
            'url'     => upload_url($path, 'thumb'),
        ]);
    }

    public function updateCover(Request $request): JsonResponse
    {
        $request->validate([
            'cover' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
        ], [
            'cover.max' => 'Kapak resmi en fazla 1 MB olabilir.',
        ]);

        $user = auth()->user();
        $path = $this->profileService->uploadCover($user, $request->file('cover'));

        return response()->json([
            'success' => true,
            'url'     => upload_url($path),
        ]);
    }
}
