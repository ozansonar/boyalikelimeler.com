<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\QnaAnswerService;
use App\Services\QnaCategoryService;
use App\Services\QnaQuestionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class QnaQuestionController extends Controller
{
    public function __construct(
        private readonly QnaQuestionService $questionService,
        private readonly QnaCategoryService $categoryService,
        private readonly QnaAnswerService   $answerService,
    ) {}

    public function index(Request $request): View
    {
        $filters = [
            'status'   => $request->query('status'),
            'category' => $request->query('category'),
            'search'   => $request->query('search'),
        ];

        $perPage = (int) $request->query('per_page', '20');
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : 20;

        $questions  = $this->questionService->paginate($filters, $perPage);
        $stats      = $this->questionService->getAdminStats();
        $categories = $this->categoryService->getActiveCategories();

        return view('admin.qna-questions.index', compact('questions', 'stats', 'filters', 'perPage', 'categories'));
    }

    public function show(int $id): View
    {
        $question = $this->questionService->findById($id);

        if (!$question) {
            abort(404);
        }

        return view('admin.qna-questions.show', compact('question'));
    }

    public function approve(int $id): JsonResponse
    {
        $question = $this->questionService->findById($id);

        if (!$question) {
            return response()->json(['success' => false, 'message' => 'Soru bulunamadı.'], 404);
        }

        $this->questionService->approve($question);

        return response()->json([
            'success' => true,
            'message' => 'Soru başarıyla onaylandı.',
        ]);
    }

    public function reject(int $id): JsonResponse
    {
        $question = $this->questionService->findById($id);

        if (!$question) {
            return response()->json(['success' => false, 'message' => 'Soru bulunamadı.'], 404);
        }

        $this->questionService->reject($question);

        return response()->json([
            'success' => true,
            'message' => 'Soru reddedildi.',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $question = $this->questionService->findById($id);

        if (!$question) {
            return response()->json(['success' => false, 'message' => 'Soru bulunamadı.'], 404);
        }

        $this->questionService->destroy($question);

        return response()->json([
            'success' => true,
            'message' => 'Soru başarıyla silindi.',
        ]);
    }
}
