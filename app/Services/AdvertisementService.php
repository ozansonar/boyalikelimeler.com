<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AdvertisementPosition;
use App\Models\Advertisement;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class AdvertisementService
{
    public function __construct(
        private readonly UploadService $uploadService,
    ) {}

    public function getAll(): Collection
    {
        return Advertisement::ordered()->get();
    }

    /**
     * @return array<string, int>
     */
    public function getAdminStats(): array
    {
        return Cache::remember('admin.advertisements.stats', 300, function (): array {
            return [
                'total'    => Advertisement::count(),
                'active'   => Advertisement::active()->currentlyValid()->count(),
                'inactive' => Advertisement::where('is_active', false)->count(),
                'clicks'   => (int) Advertisement::sum('click_count'),
            ];
        });
    }

    public function find(int $id): Advertisement
    {
        return Advertisement::findOrFail($id);
    }

    /**
     * @return Collection<int, Advertisement>
     */
    public function getActiveByPosition(AdvertisementPosition $position): Collection
    {
        return Cache::remember(
            "advertisements.active.{$position->value}",
            300,
            fn () => Advertisement::active()
                ->currentlyValid()
                ->position($position)
                ->ordered()
                ->get()
        );
    }

    public function store(array $data): Advertisement
    {
        return DB::transaction(function () use ($data): Advertisement {
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $data['image'] = $this->uploadService->uploadImage(
                    $data['image'],
                    'advertisements',
                    $data['title'] ?? null
                );
            }

            $ad = Advertisement::create($data);
            $this->clearCache();

            return $ad;
        });
    }

    public function update(Advertisement $ad, array $data): Advertisement
    {
        return DB::transaction(function () use ($ad, $data): Advertisement {
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $data['image'] = $this->uploadService->replaceImage(
                    $data['image'],
                    'advertisements',
                    $ad->image,
                    $data['title'] ?? $ad->title
                );
            }

            $ad->update($data);
            $this->clearCache();

            return $ad;
        });
    }

    public function destroy(Advertisement $ad): void
    {
        DB::transaction(function () use ($ad): void {
            if ($ad->image) {
                $this->uploadService->deleteImage($ad->image);
            }
            $ad->delete();
            $this->clearCache();
        });
    }

    public function incrementClick(Advertisement $ad): void
    {
        $ad->increment('click_count');
    }

    public function incrementView(Advertisement $ad): void
    {
        $ad->increment('view_count');
    }

    private function clearCache(): void
    {
        Cache::forget('admin.advertisements.stats');
        foreach (AdvertisementPosition::cases() as $position) {
            Cache::forget("advertisements.active.{$position->value}");
        }
    }
}
