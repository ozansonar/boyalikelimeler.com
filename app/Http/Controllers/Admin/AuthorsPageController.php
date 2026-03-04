<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\RoleSlug;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthorsPageController extends Controller
{
    public function __construct(
        private readonly SettingService $settingService,
    ) {}

    public function index(): View
    {
        $settings = $this->settingService->getGroup('authors_page');

        $writers = User::query()
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->where('roles.slug', RoleSlug::Yazar->value)
            ->whereNotNull('users.email_verified_at')
            ->select('users.id', 'users.name', 'users.username')
            ->orderBy('users.name')
            ->get();

        return view('admin.authors-page.index', [
            'settings' => $settings,
            'writers'  => $writers,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'              => 'nullable|string|max:200',
            'description'        => 'nullable|string|max:500',
            'body'               => 'nullable|string|max:50000',
            'featured_author_id' => 'nullable|integer|exists:users,id',
            'meta_title'         => 'nullable|string|max:70',
            'meta_description'   => 'nullable|string|max:170',
        ]);

        $this->settingService->updateGroup('authors_page', $data);

        return redirect()->route('admin.authors-page.index')
            ->with('success', 'Yazarlar sayfası ayarları başarıyla güncellendi.');
    }
}
