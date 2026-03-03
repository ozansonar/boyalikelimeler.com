<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LiteraryCategoryStoreRequest;
use App\Http\Requests\Admin\LiteraryCategoryUpdateRequest;
use App\Models\LiteraryCategory;
use App\Services\LiteraryCategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class LiteraryCategoryController extends Controller
{
    public function __construct(
        private readonly LiteraryCategoryService $service,
    ) {}

    public function index(Request $request): View
    {
        $perPage = in_array((int) $request->input('per_page'), [10, 25, 50, 100], true)
            ? (int) $request->input('per_page')
            : 10;

        $filters = $request->only(['search', 'is_active']);

        return view('admin.literary-categories.index', [
            'categories' => $this->service->paginate($perPage, $filters),
            'filters'    => $filters,
            'perPage'    => $perPage,
        ]);
    }

    public function create(): View
    {
        return view('admin.literary-categories.create');
    }

    public function store(LiteraryCategoryStoreRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('admin.literary-categories.index')
            ->with('success', 'Edebiyat kategorisi başarıyla oluşturuldu.');
    }

    public function edit(LiteraryCategory $literaryCategory): View
    {
        return view('admin.literary-categories.edit', ['category' => $literaryCategory]);
    }

    public function update(LiteraryCategoryUpdateRequest $request, LiteraryCategory $literaryCategory): RedirectResponse
    {
        $this->service->update($literaryCategory, $request->validated());

        return redirect()->route('admin.literary-categories.index')
            ->with('success', 'Edebiyat kategorisi başarıyla güncellendi.');
    }

    public function destroy(LiteraryCategory $literaryCategory): RedirectResponse
    {
        if ($literaryCategory->works()->exists()) {
            return back()->with('error', 'Bu kategoriye ait eserler var, önce onları silin veya başka kategoriye taşıyın.');
        }

        $this->service->delete($literaryCategory);

        return redirect()->route('admin.literary-categories.index')
            ->with('success', 'Edebiyat kategorisi başarıyla silindi.');
    }
}
