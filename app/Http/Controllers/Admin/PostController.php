<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostStoreRequest;
use App\Http\Requests\Admin\PostUpdateRequest;
use App\Models\Post;
use App\Models\User;
use App\Services\CategoryService;
use App\Services\PostService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    public function __construct(
        private readonly PostService $postService,
        private readonly CategoryService $categoryService,
    ) {}

    public function index(Request $request): View
    {
        $perPage = in_array((int) $request->input('per_page'), [10, 25, 50, 100], true)
            ? (int) $request->input('per_page')
            : 10;

        $filters = $request->only(['search', 'status', 'category_id', 'user_id', 'sort', 'dir']);

        return view('admin.posts.index', [
            'posts'        => $this->postService->paginate($perPage, $filters),
            'stats'        => $this->postService->getAdminStats(),
            'statusCounts' => $this->postService->getStatusCounts(),
            'categories'   => $this->categoryService->activeList(),
            'authors'      => User::select('id', 'name')->orderBy('name')->get(),
            'filters'      => $filters,
            'perPage'      => $perPage,
        ]);
    }

    public function create(): View
    {
        return view('admin.posts.create', [
            'categories' => $this->categoryService->activeList(),
        ]);
    }

    public function store(PostStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')
                ->store('posts/covers', 'public_uploads');
        }

        $this->postService->create($data);

        return redirect()->route('admin.posts.index')
            ->with('success', 'İçerik başarıyla oluşturuldu.');
    }

    public function edit(Post $post): View
    {
        $post->load(['category', 'author']);

        return view('admin.posts.edit', [
            'post'       => $post,
            'categories' => $this->categoryService->activeList(),
        ]);
    }

    public function update(PostUpdateRequest $request, Post $post): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')
                ->store('posts/covers', 'public_uploads');
        }

        $this->postService->update($post, $data);

        return redirect()->route('admin.posts.index')
            ->with('success', 'İçerik başarıyla güncellendi.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->postService->delete($post);

        return redirect()->route('admin.posts.index')
            ->with('success', 'İçerik başarıyla silindi.');
    }
}
