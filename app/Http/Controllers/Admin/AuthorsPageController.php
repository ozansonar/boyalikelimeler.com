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
            'title'                    => 'nullable|string|max:200',
            'description'              => 'nullable|string|max:500',
            'featured_author_ids'      => 'nullable|array',
            'featured_author_ids.*'    => 'integer|exists:users,id',
            'featured_author_labels'   => 'nullable|array',
            'featured_author_labels.*' => 'nullable|string|max:150',
            'golden_pen_title'         => 'nullable|string|max:200',
            'golden_pen_description'   => 'nullable|string|max:500',
            'authors_list_title'       => 'nullable|string|max:200',
            'meta_title'               => 'nullable|string|max:70',
            'meta_description'         => 'nullable|string|max:170',
        ]);

        $ids = array_values(array_filter($data['featured_author_ids'] ?? []));
        $rawLabels = $data['featured_author_labels'] ?? [];

        $labels = [];
        foreach ($ids as $index => $id) {
            $label = trim($rawLabels[$index] ?? '');
            if ($label !== '') {
                $labels[(string) $id] = $label;
            }
        }

        $data['featured_author_ids'] = json_encode($ids, JSON_THROW_ON_ERROR);
        $data['featured_author_labels'] = json_encode($labels, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);

        $this->settingService->updateGroup('authors_page', $data);

        return redirect()->route('admin.authors-page.index')
            ->with('success', 'Yazarlar sayfası ayarları başarıyla güncellendi.');
    }
}
