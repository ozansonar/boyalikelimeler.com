<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PollStoreRequest;
use App\Http\Requests\Admin\PollUpdateRequest;
use App\Services\PollService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class PollController extends Controller
{
    public function __construct(
        private readonly PollService $pollService,
    ) {}

    public function index(): View
    {
        return view('admin.polls.index', [
            'polls' => $this->pollService->getAll(),
            'stats' => $this->pollService->getAdminStats(),
        ]);
    }

    public function create(): View
    {
        return view('admin.polls.create');
    }

    public function store(PollStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $options = $data['options'];
        unset($data['options']);

        $data['is_active'] = $request->boolean('is_active');

        $this->pollService->store($data, $options);

        return redirect()->route('admin.polls.index')
            ->with('success', 'Anket başarıyla oluşturuldu.');
    }

    public function edit(int $id): View
    {
        return view('admin.polls.edit', [
            'poll' => $this->pollService->find($id),
        ]);
    }

    public function update(PollUpdateRequest $request, int $id): RedirectResponse
    {
        $poll = $this->pollService->find($id);
        $data = $request->validated();
        $options = $data['options'];
        unset($data['options']);

        $data['is_active'] = $request->boolean('is_active');

        $this->pollService->update($poll, $data, $options);

        return redirect()->route('admin.polls.index')
            ->with('success', 'Anket başarıyla güncellendi.');
    }

    public function results(int $id): View
    {
        $poll = $this->pollService->find($id);
        $results = $this->pollService->getResults($id);
        $totalVotes = $this->pollService->getTotalVotes($id);

        return view('admin.polls.results', [
            'poll'       => $poll,
            'results'    => $results,
            'totalVotes' => $totalVotes,
        ]);
    }

    public function toggleActive(int $id): RedirectResponse
    {
        $poll = $this->pollService->find($id);
        $this->pollService->toggleActive($poll);

        $status = $poll->fresh()->is_active ? 'aktif' : 'pasif';

        return redirect()->route('admin.polls.index')
            ->with('success', "Anket {$status} yapıldı.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $poll = $this->pollService->find($id);
        $this->pollService->destroy($poll);

        return redirect()->route('admin.polls.index')
            ->with('success', 'Anket başarıyla silindi.');
    }
}
