<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\LiteraryWorkType;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaintersPageController extends Controller
{
    public function __construct(
        private readonly SettingService $settingService,
    ) {}

    public function index(): View
    {
        $settings = $this->settingService->getGroup('painters_page');

        $painters = User::query()
            ->whereNotNull('users.email_verified_at')
            ->whereExists(function ($q): void {
                $q->select(DB::raw(1))
                  ->from('literary_works')
                  ->whereColumn('literary_works.user_id', 'users.id')
                  ->whereNull('literary_works.deleted_at')
                  ->where('literary_works.status', 'approved')
                  ->where('literary_works.work_type', LiteraryWorkType::Visual->value);
            })
            ->select('users.id', 'users.name', 'users.username')
            ->orderBy('users.name')
            ->get();

        return view('admin.painters-page.index', [
            'settings' => $settings,
            'painters' => $painters,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'                     => 'nullable|string|max:200',
            'description'               => 'nullable|string|max:500',
            'featured_painter_ids'      => 'nullable|array',
            'featured_painter_ids.*'    => 'integer|exists:users,id',
            'featured_painter_labels'   => 'nullable|array',
            'featured_painter_labels.*' => 'nullable|string|max:150',
            'painters_list_title'       => 'nullable|string|max:200',
            'meta_title'                => 'nullable|string|max:70',
            'meta_description'          => 'nullable|string|max:170',
        ]);

        $ids = array_values(array_filter($data['featured_painter_ids'] ?? []));
        $rawLabels = $data['featured_painter_labels'] ?? [];

        $labels = [];
        foreach ($ids as $index => $id) {
            $label = trim($rawLabels[$index] ?? '');
            if ($label !== '') {
                $labels[(string) $id] = $label;
            }
        }

        $data['featured_painter_ids'] = json_encode($ids, JSON_THROW_ON_ERROR);
        $data['featured_painter_labels'] = json_encode($labels, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);

        $this->settingService->updateGroup('painters_page', $data);

        return redirect()->route('admin.painters-page.index')
            ->with('success', 'Ressamlar sayfası ayarları başarıyla güncellendi.');
    }
}
