<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\DailyQuestionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class DailyQuestionController extends Controller
{
    public function __construct(
        private readonly DailyQuestionService $dailyQuestionService,
    ) {}

    public function answer(Request $request): JsonResponse
    {
        $request->validate([
            'question_id'  => ['required', 'integer', 'exists:daily_questions,id'],
            'answer_text'  => ['required', 'string', 'max:1000'],
            'cookie_token' => ['nullable', 'string', 'max:64'],
        ]);

        $questionId = (int) $request->input('question_id');
        $userId = auth()->id();
        $ipAddress = $request->ip();
        $cookieToken = $request->input('cookie_token');

        if ($this->dailyQuestionService->hasAnswered($questionId, $userId, $ipAddress, $cookieToken)) {
            return response()->json([
                'success' => false,
                'message' => 'Bu soruya zaten cevap verdiniz.',
            ], 422);
        }

        $this->dailyQuestionService->storeAnswer([
            'daily_question_id' => $questionId,
            'answer_text'       => $request->input('answer_text'),
            'user_id'           => $userId,
            'ip_address'        => $ipAddress,
            'cookie_token'      => $cookieToken,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Teşekkürler! Cevabınız başarıyla kaydedildi.',
        ]);
    }
}
