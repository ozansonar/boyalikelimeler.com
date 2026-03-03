<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LiteraryWorkRevisionRequest;
use App\Http\Requests\Admin\LiteraryWorkUpdateRequest;
use App\Models\LiteraryWork;
use App\Services\LiteraryCategoryService;
use App\Services\LiteraryWorkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class LiteraryWorkController extends Controller
{
    public function __construct(
        private readonly LiteraryWorkService $workService,
        private readonly LiteraryCategoryService $categoryService,
    ) {}

    public function index(Request $request): View
    {
        $perPage = in_array((int) $request->input('per_page'), [10, 25, 50, 100], true)
            ? (int) $request->input('per_page')
            : 10;

        $filters = $request->only(['search', 'status', 'category', 'author']);

        return view('admin.literary-works.index', [
            'works'      => $this->workService->adminPaginate($perPage, $filters),
            'stats'      => $this->workService->getAdminStats(),
            'categories' => $this->categoryService->activeList(),
            'authors'    => $this->workService->getAuthorsWithWorks(),
            'filters'    => $filters,
            'perPage'    => $perPage,
        ]);
    }

    public function show(int $id): View
    {
        $work = $this->workService->findForAdmin($id);

        if (! $work) {
            abort(404);
        }

        return view('admin.literary-works.show', compact('work'));
    }

    public function edit(LiteraryWork $literaryWork): View
    {
        $literaryWork->load(['category', 'revisions.admin']);

        return view('admin.literary-works.edit', [
            'work'       => $literaryWork,
            'categories' => $this->categoryService->activeList(),
        ]);
    }

    public function update(LiteraryWorkUpdateRequest $request, LiteraryWork $literaryWork): RedirectResponse
    {
        $this->workService->adminUpdateWork(
            $literaryWork,
            $request->validated(),
            $request->file('cover_image'),
        );

        return redirect()->route('admin.literary-works.show', $literaryWork->id)
            ->with('success', 'Eser başarıyla güncellendi.');
    }

    public function destroy(LiteraryWork $literaryWork): RedirectResponse
    {
        $this->workService->adminDeleteWork($literaryWork);

        return redirect()->route('admin.literary-works.index')
            ->with('success', 'Eser başarıyla silindi.');
    }

    public function unpublish(LiteraryWork $literaryWork): RedirectResponse
    {
        $result = $this->workService->adminUnpublishWork($literaryWork);

        if (! $result) {
            return redirect()->back()->with('error', 'Yalnızca onaylanmış eserler yayından kaldırılabilir.');
        }

        return redirect()->route('admin.literary-works.index')
            ->with('success', 'Eser yayından kaldırıldı.');
    }

    public function approve(LiteraryWork $literaryWork): RedirectResponse
    {
        $mailSent = $this->workService->approve($literaryWork);

        $redirect = redirect()->route('admin.literary-works.index')
            ->with('success', 'Eser başarıyla onaylandı.');

        if (! $mailSent) {
            $redirect->with('warning', 'Bildirim maili gönderilemedi. Lütfen mail ayarlarını kontrol edin.');
        }

        return $redirect;
    }

    public function reject(LiteraryWork $literaryWork): RedirectResponse
    {
        $mailSent = $this->workService->reject($literaryWork);

        $redirect = redirect()->route('admin.literary-works.index')
            ->with('success', 'Eser reddedildi.');

        if (! $mailSent) {
            $redirect->with('warning', 'Bildirim maili gönderilemedi. Lütfen mail ayarlarını kontrol edin.');
        }

        return $redirect;
    }

    public function requestRevision(LiteraryWorkRevisionRequest $request, LiteraryWork $literaryWork): RedirectResponse
    {
        $mailSent = $this->workService->requestRevision(
            $literaryWork,
            auth()->user(),
            $request->validated()['reason'],
        );

        $redirect = redirect()->route('admin.literary-works.index')
            ->with('success', 'Revize talebi başarıyla oluşturuldu.');

        if (! $mailSent) {
            $redirect->with('warning', 'Bildirim maili gönderilemedi. Lütfen mail ayarlarını kontrol edin.');
        }

        return $redirect;
    }
}
