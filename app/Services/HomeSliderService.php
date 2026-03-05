<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\HomeSlider;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeSliderService
{
    public function getAll(): Collection
    {
        return HomeSlider::ordered()->get();
    }

    public function getActiveSliders(): Collection
    {
        return Cache::remember('home_sliders_active', 300, function () {
            return HomeSlider::active()->ordered()->get();
        });
    }

    public function find(int $id): HomeSlider
    {
        return HomeSlider::findOrFail($id);
    }

    public function store(array $data): HomeSlider
    {
        return DB::transaction(function () use ($data) {
            $slider = HomeSlider::create($data);
            $this->clearCache();

            return $slider;
        });
    }

    public function update(HomeSlider $slider, array $data): HomeSlider
    {
        return DB::transaction(function () use ($slider, $data) {
            $slider->update($data);
            $this->clearCache();

            return $slider;
        });
    }

    public function destroy(HomeSlider $slider): void
    {
        DB::transaction(function () use ($slider) {
            $slider->delete();
            $this->clearCache();
        });
    }

    public function updateOrder(array $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order as $position => $id) {
                HomeSlider::where('id', $id)->update(['sort_order' => $position]);
            }
            $this->clearCache();
        });
    }

    private function clearCache(): void
    {
        Cache::forget('home_sliders_active');
    }
}
