<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QnaAnswer;
use App\Services\QnaAnswerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class QnaAnswerController extends Controller
{
    public function __construct(
        private readonly QnaAnswerService $answerService,
    ) {}

    public function index(Request $request): View
    {
        $filters = [
            'status' => $request->query('status'),
            'search' => $request->query('search'),
        ];

        $perPage = (int) $request->query('per_page', '20');
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : 20;

        $answers = $this->answerService->paginate($filters, $perPage);
        $stats   = $this->answerService->getAdminStats();

        return view('admin.qna-answers.index', compact('answers', 'stats', 'filters', 'perPage'));
    }

    public function approve(int $id): JsonResponse
    {
        $answer = QnaAnswer::with(['question', 'user'])->find($id);

        if (!$answer) {
            return response()->json(['success' => false, 'message' => 'Cevap bulunamadı.'], 404);
        }

        $this->answerService->approve($answer);

        return response()->json([
            'success' => true,
            'message' => 'Cevap başarıyla onaylandı.',
        ]);
    }

    public function reject(int $id): JsonResponse
    {
        $answer = QnaAnswer::find($id);

        if (!$answer) {
            return response()->json(['success' => false, 'message' => 'Cevap bulunamadı.'], 404);
        }

        $this->answerService->reject($answer);

        return response()->json([
            'success' => true,
            'message' => 'Cevap reddedildi.',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $answer = QnaAnswer::find($id);

        if (!$answer) {
            return response()->json(['success' => false, 'message' => 'Cevap bulunamadı.'], 404);
        }

        $this->answerService->destroy($answer);

        return response()->json([
            'success' => true,
            'message' => 'Cevap başarıyla silindi.',
        ]);
    }
}
