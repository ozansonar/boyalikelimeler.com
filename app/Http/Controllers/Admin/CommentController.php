<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class CommentController extends Controller
{
    public function __construct(
        private readonly CommentService $commentService,
    ) {}

    public function index(Request $request): View
    {
        $filters = [
            'type'   => $request->query('type'),
            'status' => $request->query('status'),
            'search' => $request->query('search'),
        ];

        $perPage = (int) $request->query('per_page', '20');
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : 20;

        $comments = $this->commentService->paginate($filters, $perPage);
        $stats    = $this->commentService->getStats();

        return view('admin.comments.index', compact('comments', 'stats', 'filters', 'perPage'));
    }

    public function approve(Comment $comment): JsonResponse
    {
        $this->commentService->approve($comment, auth()->user());

        return response()->json([
            'success' => true,
            'message' => 'Yorum onaylandı.',
        ]);
    }

    public function reject(Comment $comment): JsonResponse
    {
        $this->commentService->reject($comment);

        return response()->json([
            'success' => true,
            'message' => 'Yorum reddedildi.',
        ]);
    }

    public function destroy(Comment $comment): JsonResponse
    {
        $this->commentService->destroy($comment);

        return response()->json([
            'success' => true,
            'message' => 'Yorum silindi.',
        ]);
    }
}
