<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Services\PostService;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly PostService $postService,
    ) {}

    public function show(string $slug): View
    {
        $category = $this->categoryService->findActiveBySlug($slug);

        if (! $category) {
            abort(404);
        }

        return view('front.category.show', [
            'category'     => $category,
            'posts'        => $this->postService->getPublishedPosts(9, $category->id),
            'categories'   => $this->categoryService->activeList(),
            'popularPosts' => $this->postService->getPopularPosts(5),
        ]);
    }
}
