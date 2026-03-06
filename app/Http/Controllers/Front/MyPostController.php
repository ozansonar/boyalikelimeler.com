<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\LiteraryWorkStoreRequest;
use App\Http\Requests\Front\LiteraryWorkUpdateRequest;
use App\Enums\LiteraryWorkStatus;
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
        $workType = $request->query('work_type');
        $stats = $this->workService->getAuthorStats($user, $workType);
        $works = $this->workService->authorPaginate($user, 10, [
            'search'    => $request->query('search'),
            'status'    => $request->query('status'),
            'work_type' => $workType,
        ]);

        return view('front.myposts.index', compact('stats', 'works', 'workType'));
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

        $redirect = redirect()
            ->route('myposts.index')
            ->with('success', 'Eseriniz başarıyla gönderildi. Editör onayından sonra yayınlanacaktır.');

        if (! $this->workService->wasMailSent()) {
            $redirect->with('warning', 'Editörlere bildirim maili gönderilemedi, ancak eseriniz kaydedildi.');
        }

        return $redirect;
    }

    public function show(LiteraryWork $work): View
    {
        $user = auth()->user();

        $workForShow = $this->workService->getWorkForEdit($user, $work);

        if (! $workForShow) {
            abort(403, 'Bu eseri görüntüleme yetkiniz yok.');
        }

        return view('front.myposts.show', [
            'work' => $workForShow,
        ]);
    }

    public function edit(LiteraryWork $work): View
    {
        $user = auth()->user();

        $workForEdit = $this->workService->getWorkForEdit($user, $work);

        if (! $workForEdit) {
            abort(403, 'Bu eseri düzenleme yetkiniz yok.');
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
        $wasApproved = $work->status === LiteraryWorkStatus::Approved;

        $updatedWork = $this->workService->updateWork(
            $user,
            $work,
            $validated,
            $request->file('cover_image'),
        );

        if (! $updatedWork) {
            abort(403, 'Bu eseri güncelleme yetkiniz yok.');
        }

        $successMessage = $wasApproved
            ? 'Eseriniz güncellendi. Yayından kaldırılmış olup editör onayından sonra tekrar yayınlanacaktır.'
            : 'Eseriniz başarıyla güncellendi ve tekrar incelemeye gönderildi.';

        $redirect = redirect()
            ->route('myposts.index')
            ->with('success', $successMessage);

        if (! $this->workService->wasMailSent()) {
            $redirect->with('warning', 'Editörlere bildirim maili gönderilemedi, ancak eseriniz güncellendi.');
        }

        return $redirect;
    }

    public function unpublish(LiteraryWork $work): RedirectResponse
    {
        $user = auth()->user();

        if (! $this->workService->unpublishWork($user, $work)) {
            abort(403, 'Bu eseri yayından kaldırma yetkiniz yok.');
        }

        return redirect()
            ->route('myposts.index')
            ->with('success', 'Eseriniz yayından kaldırıldı. Tekrar yayınlamak istediğinizde editör onayı gerekecektir.');
    }

    public function republish(LiteraryWork $work): RedirectResponse
    {
        $user = auth()->user();

        if (! $this->workService->republishWork($user, $work)) {
            abort(403, 'Bu eseri tekrar yayına gönderme yetkiniz yok.');
        }

        $redirect = redirect()
            ->route('myposts.index')
            ->with('success', 'Eseriniz tekrar incelemeye gönderildi. Editör onayından sonra yayınlanacaktır.');

        if (! $this->workService->wasMailSent()) {
            $redirect->with('warning', 'Editörlere bildirim maili gönderilemedi, ancak eseriniz incelemeye gönderildi.');
        }

        return $redirect;
    }

    public function destroy(LiteraryWork $work): RedirectResponse
    {
        $user = auth()->user();

        if (! $this->workService->deleteWork($user, $work)) {
            abort(403, 'Bu eseri silme yetkiniz yok.');
        }

        return redirect()
            ->route('myposts.index')
            ->with('success', 'Eseriniz başarıyla silindi.');
    }
}
