<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminQnaCategoryRequest;
use App\Services\QnaCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class QnaCategoryController extends Controller
{
    public function __construct(
        private readonly QnaCategoryService $categoryService,
    ) {}

    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->query('search'),
        ];

        $categories = $this->categoryService->paginate($filters);

        return view('admin.qna-categories.index', compact('categories', 'filters'));
    }

    public function create(): View
    {
        return view('admin.qna-categories.form');
    }

    public function store(AdminQnaCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active');

        $this->categoryService->store($data);

        return redirect()
            ->route('admin.qna.categories.index')
            ->with('success', 'Kategori başarıyla oluşturuldu.');
    }

    public function edit(int $id): View
    {
        $category = $this->categoryService->findById($id);

        if (!$category) {
            abort(404);
        }

        return view('admin.qna-categories.form', compact('category'));
    }

    public function update(AdminQnaCategoryRequest $request, int $id): RedirectResponse
    {
        $category = $this->categoryService->findById($id);

        if (!$category) {
            abort(404);
        }

        $data = $request->validated();
        $data['is_active'] = $request->has('is_active');

        $this->categoryService->update($category, $data);

        return redirect()
            ->route('admin.qna.categories.index')
            ->with('success', 'Kategori başarıyla güncellendi.');
    }

    public function destroy(int $id): JsonResponse
    {
        $category = $this->categoryService->findById($id);

        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Kategori bulunamadı.'], 404);
        }

        $this->categoryService->destroy($category);

        return response()->json([
            'success' => true,
            'message' => 'Kategori başarıyla silindi.',
        ]);
    }
}
