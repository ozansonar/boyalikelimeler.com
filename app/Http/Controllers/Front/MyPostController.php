<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\LiteraryWorkStoreRequest;
use App\Http\Requests\Front\LiteraryWorkUpdateRequest;
use App\Models\LiteraryWork;
use App\Services\LiteraryCategoryService;
use App\Services\LiteraryWorkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class MyPostController extends Controller
{
    public function __construct(
        private readonly LiteraryWorkService $workService,
        private readonly LiteraryCategoryService $categoryService,
    ) {}

    public function index(Request $request): View
    {
        $user = auth()->user();
        $stats = $this->workService->getAuthorStats($user);
        $works = $this->workService->authorPaginate($user, 10, [
            'search' => $request->query('search'),
            'status' => $request->query('status'),
        ]);

        return view('front.myposts.index', compact('stats', 'works'));
    }

    public function create(): View
    {
        $categories = $this->categoryService->activeList();

        return view('front.myposts.form', [
            'work'       => null,
            'categories' => $categories,
            'pageTitle'  => 'Eser Gönder',
        ]);
    }

    public function store(LiteraryWorkStoreRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $validated = $request->validated();

        $this->workService->createWork(
            $user,
            $validated,
            $request->file('cover_image'),
        );

        return redirect()
            ->route('myposts.index')
            ->with('success', 'Eseriniz başarıyla gönderildi. Editör onayından sonra yayınlanacaktır.');
    }

    public function edit(LiteraryWork $work): View
    {
        $user = auth()->user();

        $workForEdit = $this->workService->getWorkForEdit($user, $work);

        if (! $workForEdit) {
            abort(403);
        }

        $categories = $this->categoryService->activeList();

        return view('front.myposts.form', [
            'work'       => $workForEdit,
            'categories' => $categories,
            'pageTitle'  => 'Eseri Düzenle',
        ]);
    }

    public function update(LiteraryWorkUpdateRequest $request, LiteraryWork $work): RedirectResponse
    {
        $user = auth()->user();
        $validated = $request->validated();

        $updatedWork = $this->workService->updateWork(
            $user,
            $work,
            $validated,
            $request->file('cover_image'),
        );

        if (! $updatedWork) {
            abort(403);
        }

        return redirect()
            ->route('myposts.index')
            ->with('success', 'Eseriniz başarıyla güncellendi ve tekrar incelemeye gönderildi.');
    }

    public function destroy(LiteraryWork $work): RedirectResponse
    {
        $user = auth()->user();

        if (! $this->workService->deleteWork($user, $work)) {
            abort(403);
        }

        return redirect()
            ->route('myposts.index')
            ->with('success', 'Eseriniz başarıyla silindi.');
    }
}
