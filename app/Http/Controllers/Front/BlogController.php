<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\CategoryService;
use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function __construct(
        private readonly PostService $postService,
        private readonly CategoryService $categoryService,
    ) {}

    public function index(Request $request): View
    {
        $categoryId = $request->input('kategori') ? Category::where('slug', $request->input('kategori'))->value('id') : null;

        return view('front.blog.index', [
            'posts'           => $this->postService->getPublishedPosts(9, $categoryId),
            'categories'      => $this->categoryService->activeList(),
            'featuredPosts'   => $this->postService->getFeaturedPosts(3),
            'popularPosts'    => $this->postService->getPopularPosts(5),
            'stats'           => $this->postService->getPublishedStats(),
            'currentCategory' => $request->input('kategori'),
        ]);
    }

    public function show(string $slug): View
    {
        $post = $this->postService->findPublishedBySlug($slug);

        if (! $post) {
            abort(404);
        }

        $this->postService->incrementViews($post);

        return view('front.blog.show', [
            'post'         => $post,
            'categories'   => $this->categoryService->activeList(),
            'popularPosts' => $this->postService->getPopularPosts(4),
        ]);
    }
}
