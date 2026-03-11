<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DailyQuestionStoreRequest;
use App\Http\Requests\Admin\DailyQuestionUpdateRequest;
use App\Services\DailyQuestionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class DailyQuestionController extends Controller
{
    public function __construct(
        private readonly DailyQuestionService $dailyQuestionService,
    ) {}

    public function index(): View
    {
        return view('admin.daily-questions.index', [
            'questions' => $this->dailyQuestionService->getAll(),
            'stats'     => $this->dailyQuestionService->getAdminStats(),
        ]);
    }

    public function create(): View
    {
        return view('admin.daily-questions.create');
    }

    public function store(DailyQuestionStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $this->dailyQuestionService->store($data);

        return redirect()->route('admin.daily-questions.index')
            ->with('success', 'Günün sorusu başarıyla oluşturuldu.');
    }

    public function edit(int $id): View
    {
        return view('admin.daily-questions.edit', [
            'question' => $this->dailyQuestionService->find($id),
        ]);
    }

    public function update(DailyQuestionUpdateRequest $request, int $id): RedirectResponse
    {
        $question = $this->dailyQuestionService->find($id);
        $this->dailyQuestionService->update($question, $request->validated());

        return redirect()->route('admin.daily-questions.index')
            ->with('success', 'Günün sorusu başarıyla güncellendi.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $question = $this->dailyQuestionService->find($id);
        $this->dailyQuestionService->destroy($question);

        return redirect()->route('admin.daily-questions.index')
            ->with('success', 'Günün sorusu başarıyla silindi.');
    }

    public function answers(int $id): View
    {
        $question = $this->dailyQuestionService->find($id);
        $answers = $this->dailyQuestionService->getAnswers($id);

        return view('admin.daily-questions.answers', [
            'question' => $question,
            'answers'  => $answers,
        ]);
    }

    public function destroyAnswer(int $questionId, int $answerId): RedirectResponse
    {
        $answer = \App\Models\DailyQuestionAnswer::where('daily_question_id', $questionId)
            ->findOrFail($answerId);

        $this->dailyQuestionService->destroyAnswer($answer);

        return redirect()->route('admin.daily-questions.answers', $questionId)
            ->with('success', 'Cevap başarıyla silindi.');
    }
}
