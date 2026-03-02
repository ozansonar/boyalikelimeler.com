<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PageStoreRequest;
use App\Http\Requests\Admin\PageUpdateRequest;
use App\Models\Page;
use App\Services\PageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(
        private readonly PageService $pageService,
    ) {}

    public function index(Request $request): View
    {
        $perPage = in_array((int) $request->input('per_page'), [10, 25, 50, 100], true)
            ? (int) $request->input('per_page')
            : 10;

        $filters = $request->only(['search', 'is_active']);

        return view('admin.pages.index', [
            'pages'   => $this->pageService->paginate($perPage, $filters),
            'stats'   => $this->pageService->getAdminStats(),
            'filters' => $filters,
            'perPage' => $perPage,
        ]);
    }

    public function create(): View
    {
        return view('admin.pages.create');
    }

    public function store(PageStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')
                ->store('pages/covers', 'public_uploads');
        }

        $this->pageService->create($data);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Sayfa başarıyla oluşturuldu.');
    }

    public function edit(Page $page): View
    {
        $page->load('author');

        return view('admin.pages.edit', [
            'page' => $page,
        ]);
    }

    public function update(PageUpdateRequest $request, Page $page): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')
                ->store('pages/covers', 'public_uploads');
        }

        $this->pageService->update($page, $data);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Sayfa başarıyla güncellendi.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $this->pageService->delete($page);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Sayfa başarıyla silindi.');
    }
}
