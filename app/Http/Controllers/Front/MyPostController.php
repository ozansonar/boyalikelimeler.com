<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\PostStoreRequest;
use App\Http\Requests\Front\PostUpdateRequest;
use App\Models\Category;
use App\Models\Post;
use App\Services\MyPostService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class MyPostController extends Controller
{
    public function __construct(
        private readonly MyPostService $myPostService,
    ) {}

    public function index(Request $request): View
    {
        $user = auth()->user();
        $stats = $this->myPostService->getStats($user);
        $posts = $this->myPostService->paginate($user, 10, [
            'search' => $request->query('search'),
            'status' => $request->query('status'),
        ]);

        return view('front.myposts.index', compact('stats', 'posts'));
    }

    public function create(): View
    {
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('front.myposts.form', [
            'post'       => null,
            'categories' => $categories,
            'pageTitle'  => 'Yazı Gönder',
        ]);
    }

    public function store(PostStoreRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $validated = $request->validated();

        $this->myPostService->createPost(
            $user,
            $validated,
            $request->file('cover_image'),
        );

        return redirect()
            ->route('myposts.index')
            ->with('success', 'Yazınız başarıyla gönderildi. Editör onayından sonra yayınlanacaktır.');
    }

    public function edit(Post $post): View
    {
        $user = auth()->user();

        $postForEdit = $this->myPostService->getPostForEdit($user, $post);

        if (! $postForEdit) {
            abort(403);
        }

        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('front.myposts.form', [
            'post'       => $postForEdit,
            'categories' => $categories,
            'pageTitle'  => 'Yazıyı Düzenle',
        ]);
    }

    public function update(PostUpdateRequest $request, Post $post): RedirectResponse
    {
        $user = auth()->user();
        $validated = $request->validated();

        $updatedPost = $this->myPostService->updatePost(
            $user,
            $post,
            $validated,
            $request->file('cover_image'),
        );

        if (! $updatedPost) {
            abort(403);
        }

        return redirect()
            ->route('myposts.index')
            ->with('success', 'Yazınız başarıyla güncellendi.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $user = auth()->user();

        if (! $this->myPostService->deletePost($user, $post)) {
            abort(403);
        }

        return redirect()
            ->route('myposts.index')
            ->with('success', 'Yazınız başarıyla silindi.');
    }
}
