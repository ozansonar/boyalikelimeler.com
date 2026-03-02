<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class MenuService
{
    // ─── Menu CRUD ───

    public function allMenus(): Collection
    {
        return Menu::withCount('items')->orderBy('name')->get();
    }

    public function findMenu(int $id): Menu
    {
        return Menu::with(['items' => fn ($q) => $q->orderBy('sort_order')])->findOrFail($id);
    }

    public function createMenu(array $data): Menu
    {
        return DB::transaction(function () use ($data): Menu {
            $menu = Menu::create($data);
            $this->clearCache();
            return $menu;
        });
    }

    public function updateMenu(Menu $menu, array $data): Menu
    {
        return DB::transaction(function () use ($menu, $data): Menu {
            $menu->update($data);
            $this->clearCache();
            return $menu->fresh();
        });
    }

    public function deleteMenu(Menu $menu): void
    {
        DB::transaction(function () use ($menu): void {
            $menu->items()->delete();
            $menu->delete();
            $this->clearCache();
        });
    }

    // ─── MenuItem CRUD ───

    public function getMenuItems(Menu $menu): Collection
    {
        return $menu->items()
            ->with('children')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();
    }

    public function createItem(Menu $menu, array $data): MenuItem
    {
        return DB::transaction(function () use ($menu, $data): MenuItem {
            $data['menu_id'] = $menu->id;

            if (! isset($data['sort_order'])) {
                $data['sort_order'] = ($menu->items()->max('sort_order') ?? 0) + 1;
            }

            $item = MenuItem::create($data);
            $this->clearCache();
            return $item;
        });
    }

    public function updateItem(MenuItem $item, array $data): MenuItem
    {
        return DB::transaction(function () use ($item, $data): MenuItem {
            $item->update($data);
            $this->clearCache();
            return $item->fresh();
        });
    }

    public function deleteItem(MenuItem $item): void
    {
        DB::transaction(function () use ($item): void {
            $item->children()->delete();
            $item->delete();
            $this->clearCache();
        });
    }

    public function reorderItems(array $orderedIds): void
    {
        DB::transaction(function () use ($orderedIds): void {
            foreach ($orderedIds as $index => $id) {
                MenuItem::where('id', $id)->update(['sort_order' => $index]);
            }
            $this->clearCache();
        });
    }

    // ─── Frontend ───

    /**
     * @return Collection<int, MenuItem>
     */
    public function getByLocation(string $location): Collection
    {
        return Cache::remember('menu.' . $location, 600, function () use ($location): Collection {
            $menu = Menu::where('location', $location)->where('is_active', true)->first();

            if (! $menu) {
                return new Collection();
            }

            return $menu->activeRootItems()
                ->with(['activeChildren'])
                ->get();
        });
    }

    public function getAdminStats(): array
    {
        return Cache::remember('admin.menus.stats', 300, function (): array {
            return [
                'total_menus' => Menu::count(),
                'total_items' => MenuItem::count(),
                'active_items' => MenuItem::where('is_active', true)->count(),
            ];
        });
    }

    private function clearCache(): void
    {
        Cache::forget('admin.menus.stats');

        $locations = Menu::pluck('location');
        foreach ($locations as $location) {
            Cache::forget('menu.' . $location);
        }
    }
}
