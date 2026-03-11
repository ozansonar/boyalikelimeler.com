<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdvertisementStoreRequest;
use App\Http\Requests\Admin\AdvertisementUpdateRequest;
use App\Enums\AdvertisementPosition;
use App\Services\AdvertisementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdvertisementController extends Controller
{
    public function __construct(
        private readonly AdvertisementService $advertisementService,
    ) {}

    public function index(): View
    {
        return view('admin.advertisements.index', [
            'advertisements' => $this->advertisementService->getAll(),
            'stats'          => $this->advertisementService->getAdminStats(),
        ]);
    }

    public function create(): View
    {
        return view('admin.advertisements.create', [
            'positions' => AdvertisementPosition::cases(),
        ]);
    }

    public function store(AdvertisementStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $this->advertisementService->store($data);

        return redirect()->route('admin.advertisements.index')
            ->with('success', 'Reklam başarıyla oluşturuldu.');
    }

    public function edit(int $id): View
    {
        return view('admin.advertisements.edit', [
            'advertisement' => $this->advertisementService->find($id),
            'positions'     => AdvertisementPosition::cases(),
        ]);
    }

    public function update(AdvertisementUpdateRequest $request, int $id): RedirectResponse
    {
        $ad = $this->advertisementService->find($id);
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $this->advertisementService->update($ad, $data);

        return redirect()->route('admin.advertisements.index')
            ->with('success', 'Reklam başarıyla güncellendi.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $ad = $this->advertisementService->find($id);
        $this->advertisementService->destroy($ad);

        return redirect()->route('admin.advertisements.index')
            ->with('success', 'Reklam başarıyla silindi.');
    }
}
