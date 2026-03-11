<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQnaAnswerRequest;
use App\Http\Requests\StoreQnaQuestionRequest;
use App\Models\QnaAnswer;
use App\Models\QnaQuestion;
use App\Services\QnaAnswerService;
use App\Services\QnaCategoryService;
use App\Services\QnaLikeService;
use App\Services\QnaQuestionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

final class QnaController extends Controller
{
    public function __construct(
        private readonly QnaCategoryService $categoryService,
        private readonly QnaQuestionService $questionService,
        private readonly QnaAnswerService   $answerService,
        private readonly QnaLikeService     $likeService,
    ) {}

    public function index(): View
    {
        $categories = $this->categoryService->getActiveCategories();
        $stats      = $this->categoryService->getStats();

        return view('front.qna.index', compact('categories', 'stats'));
    }

    public function category(string $categorySlug, Request $request): View|Response
    {
        $category = $this->categoryService->getBySlug($categorySlug);

        if (!$category || !$category->is_active) {
            abort(404);
        }

        $filters = [
            'sort'   => $request->query('sort', 'newest'),
            'search' => $request->query('search'),
        ];

        $questions       = $this->questionService->getByCategory($category->id, $filters);
        $allCategories   = $this->categoryService->getActiveCategories();
        $categoryStats   = $this->categoryService->getStats();

        return view('front.qna.category', compact('category', 'questions', 'filters', 'allCategories', 'categoryStats'));
    }

    public function show(string $categorySlug, string $questionSlug): View|Response
    {
        $question = $this->questionService->getBySlug($questionSlug);

        if (!$question || !$question->isApproved()) {
            abort(404);
        }

        $sessionKey = 'qna_viewed_' . $question->id;
        if (!session()->has($sessionKey)) {
            $this->questionService->incrementViewCount($question);
            session()->put($sessionKey, true);
        }

        $relatedQuestions = $this->questionService->getRelatedQuestions($question);
        $userStats        = $this->questionService->getUserStats($question->user_id);

        $userLikedQuestion = false;
        $userLikedAnswers  = [];

        if (auth()->check()) {
            $user = auth()->user();
            $userLikedQuestion = $this->likeService->hasLiked($user, $question);

            foreach ($question->approvedAnswers as $answer) {
                if ($this->likeService->hasLiked($user, $answer)) {
                    $userLikedAnswers[$answer->id] = true;
                }
            }
        }

        return view('front.qna.show', compact(
            'question',
            'relatedQuestions',
            'userStats',
            'userLikedQuestion',
            'userLikedAnswers',
        ));
    }

    public function storeQuestion(StoreQnaQuestionRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['ip_address'] = $request->ip();

        $this->questionService->store($validated, $request->user());

        return response()->json([
            'success' => true,
            'message' => 'Sorunuz başarıyla gönderildi. Onaylandıktan sonra yayınlanacaktır.',
        ]);
    }

    public function storeAnswer(StoreQnaAnswerRequest $request, QnaQuestion $question): JsonResponse
    {
        if (!$question->isApproved()) {
            return response()->json([
                'success' => false,
                'message' => 'Bu soruya cevap yazılamaz.',
            ], 422);
        }

        $validated = $request->validated();
        $validated['ip_address'] = $request->ip();

        $this->answerService->store($validated, $question, $request->user());

        return response()->json([
            'success' => true,
            'message' => 'Cevabınız başarıyla gönderildi. Onaylandıktan sonra yayınlanacaktır.',
        ]);
    }

    public function toggleLike(Request $request): JsonResponse
    {
        $request->validate([
            'type' => ['required', 'in:question,answer'],
            'id'   => ['required', 'integer'],
        ]);

        $type  = $request->input('type');
        $id    = (int) $request->input('id');

        $likeable = $type === 'question'
            ? QnaQuestion::approved()->find($id)
            : QnaAnswer::approved()->find($id);

        if (!$likeable) {
            return response()->json([
                'success' => false,
                'message' => 'İçerik bulunamadı.',
            ], 404);
        }

        $result = $this->likeService->toggle($request->user(), $likeable);

        return response()->json([
            'success' => true,
            'liked'   => $result['liked'],
            'count'   => $result['count'],
        ]);
    }
}
