<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Services\PostService;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function __construct(
        private readonly PostService $postService,
        private readonly CategoryService $categoryService,
    ) {}

    public function index(?string $categorySlug = null): View
    {
        $category = null;

        if ($categorySlug) {
            $category = $this->categoryService->findActiveBySlug($categorySlug);

            if (! $category) {
                abort(404);
            }
        }

        return view('front.blog.index', [
            'posts'           => $this->postService->getPublishedPosts(9, $category?->id),
            'categories'      => $this->categoryService->activeList(),
            'featuredPosts'   => $this->postService->getFeaturedPosts(3),
            'popularPosts'    => $this->postService->getPopularPosts(5),
            'stats'           => $this->postService->getPublishedStats(),
            'currentCategory' => $categorySlug,
        ]);
    }

    public function show(string $categorySlug, string $slug): View
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
