<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MenuItemStoreRequest;
use App\Http\Requests\Admin\MenuItemUpdateRequest;
use App\Http\Requests\Admin\MenuStoreRequest;
use App\Http\Requests\Admin\MenuUpdateRequest;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Services\MenuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function __construct(
        private readonly MenuService $menuService,
    ) {}

    // ─── Menu CRUD ───

    public function index(): View
    {
        return view('admin.menus.index', [
            'menus' => $this->menuService->allMenus(),
            'stats' => $this->menuService->getAdminStats(),
        ]);
    }

    public function create(): View
    {
        return view('admin.menus.create');
    }

    public function store(MenuStoreRequest $request): RedirectResponse
    {
        $this->menuService->createMenu($request->validated());

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menü başarıyla oluşturuldu.');
    }

    public function edit(Menu $menu): View
    {
        return view('admin.menus.edit', [
            'menu' => $menu,
        ]);
    }

    public function update(MenuUpdateRequest $request, Menu $menu): RedirectResponse
    {
        $this->menuService->updateMenu($menu, $request->validated());

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menü başarıyla güncellendi.');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $this->menuService->deleteMenu($menu);

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menü başarıyla silindi.');
    }

    // ─── Menu Items ───

    public function items(Menu $menu): View
    {
        return view('admin.menus.items', [
            'menu'  => $menu,
            'items' => $this->menuService->getMenuItems($menu),
        ]);
    }

    public function storeItem(MenuItemStoreRequest $request, Menu $menu): RedirectResponse
    {
        $this->menuService->createItem($menu, $request->validated());

        return redirect()->route('admin.menus.items', $menu)
            ->with('success', 'Menü öğesi başarıyla eklendi.');
    }

    public function updateItem(MenuItemUpdateRequest $request, Menu $menu, MenuItem $item): RedirectResponse
    {
        $this->menuService->updateItem($item, $request->validated());

        return redirect()->route('admin.menus.items', $menu)
            ->with('success', 'Menü öğesi başarıyla güncellendi.');
    }

    public function destroyItem(Menu $menu, MenuItem $item): RedirectResponse
    {
        $this->menuService->deleteItem($item);

        return redirect()->route('admin.menus.items', $menu)
            ->with('success', 'Menü öğesi başarıyla silindi.');
    }

    public function reorderItems(Request $request, Menu $menu): JsonResponse
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'integer|exists:menu_items,id',
        ]);

        $this->menuService->reorderItems($request->input('ids'));

        return response()->json(['success' => true]);
    }
}
