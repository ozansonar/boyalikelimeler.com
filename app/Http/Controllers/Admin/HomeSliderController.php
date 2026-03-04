<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HomeSliderRequest;
use App\Services\HomeSliderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeSliderController extends Controller
{
    public function __construct(
        private readonly HomeSliderService $homeSliderService,
    ) {}

    public function index(): View
    {
        return view('admin.home-sliders.index', [
            'sliders' => $this->homeSliderService->getAll(),
        ]);
    }

    public function create(): View
    {
        return view('admin.home-sliders.create');
    }

    public function store(HomeSliderRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $this->homeSliderService->store($data);

        return redirect()->route('admin.home-sliders.index')
            ->with('success', 'Slider öğesi başarıyla oluşturuldu.');
    }

    public function edit(int $id): View
    {
        return view('admin.home-sliders.edit', [
            'slider' => $this->homeSliderService->find($id),
        ]);
    }

    public function update(HomeSliderRequest $request, int $id): RedirectResponse
    {
        $slider = $this->homeSliderService->find($id);
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $this->homeSliderService->update($slider, $data);

        return redirect()->route('admin.home-sliders.index')
            ->with('success', 'Slider öğesi başarıyla güncellendi.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $slider = $this->homeSliderService->find($id);
        $this->homeSliderService->destroy($slider);

        return redirect()->route('admin.home-sliders.index')
            ->with('success', 'Slider öğesi başarıyla silindi.');
    }

    public function updateOrder(Request $request): JsonResponse
    {
        $request->validate([
            'order'   => ['required', 'array'],
            'order.*' => ['required', 'integer', 'exists:home_sliders,id'],
        ]);

        $this->homeSliderService->updateOrder($request->input('order'));

        return response()->json(['success' => true]);
    }
}
