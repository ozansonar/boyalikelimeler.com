<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\CommentReplyStoreRequest;
use App\Http\Requests\Front\CommentStoreRequest;
use App\Models\Comment;
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

        $data = [
            'commentable_type' => $morphClass,
            'commentable_id'   => $model->id,
            'body'             => $validated['body'],
            'rating'           => (int) $validated['rating'],
            'ip_address'       => $request->ip(),
        ];

        $user = $request->user();

        if ($user) {
            $data['user_id'] = $user->id;
        } else {
            $data['first_name'] = $validated['first_name'];
            $data['last_name']  = $validated['last_name'];
            $data['email']      = $validated['email'];
        }

        $this->commentService->store($data);

        return response()->json([
            'success' => true,
            'message' => 'Yorumunuz başarıyla gönderildi. Onaylandıktan sonra yayınlanacaktır.',
        ]);
    }

    public function storeReply(CommentReplyStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $parentComment = Comment::where('id', (int) $validated['comment_id'])
            ->where('is_approved', true)
            ->whereNull('parent_id')
            ->first();

        if (!$parentComment) {
            return response()->json([
                'success' => false,
                'message' => 'Yorum bulunamadı veya yanıt verilemez.',
            ], 404);
        }

        $reply = $this->commentService->storeReply(
            $parentComment,
            $request->user(),
            $validated,
            $request->ip(),
        );

        $message = $reply->is_approved
            ? 'Yanıtınız başarıyla yayınlandı.'
            : 'Yanıtınız başarıyla gönderildi. Onaylandıktan sonra yayınlanacaktır.';

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }
}
