<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\CommentStoreRequest;
use App\Models\LiteraryWork;
use App\Models\Post;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;

final class CommentController extends Controller
{
    public function __construct(
        private readonly CommentService $commentService,
    ) {}

    public function store(CommentStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $morphClass = match ($validated['commentable_type']) {
            'literary_work' => LiteraryWork::class,
            'post'          => Post::class,
            default         => null,
        };

        if (!$morphClass) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz içerik türü.',
            ], 422);
        }

        $model = $morphClass::find((int) $validated['commentable_id']);

        if (!$model) {
            return response()->json([
                'success' => false,
                'message' => 'İçerik bulunamadı.',
            ], 404);
        }

        $this->commentService->store([
            'commentable_type' => $morphClass,
            'commentable_id'   => $model->id,
            'first_name'       => $validated['first_name'],
            'last_name'        => $validated['last_name'],
            'email'            => $validated['email'],
            'body'             => $validated['body'],
            'rating'           => (int) $validated['rating'],
            'ip_address'       => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Yorumunuz başarıyla gönderildi. Onaylandıktan sonra yayınlanacaktır.',
        ]);
    }
}
