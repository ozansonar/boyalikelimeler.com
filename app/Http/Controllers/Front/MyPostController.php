<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
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
        // Faz 3'te implement edilecek
        abort(501, 'Yakında hizmete açılacak.');
    }

    public function store(Request $request): RedirectResponse
    {
        // Faz 3'te implement edilecek
        abort(501, 'Yakında hizmete açılacak.');
    }

    public function edit(Post $post): View
    {
        // Faz 3'te implement edilecek
        abort(501, 'Yakında hizmete açılacak.');
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        // Faz 3'te implement edilecek
        abort(501, 'Yakında hizmete açılacak.');
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
