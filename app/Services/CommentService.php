<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\CommentApprovedMail;
use App\Mail\NewCommentMail;
use App\Models\Comment;
use App\Models\LiteraryWork;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

final class CommentService
{
    /**
     * @param array<string, mixed> $data
     */
    public function store(array $data): Comment
    {
        return DB::transaction(function () use ($data): Comment {
            $comment = Comment::create($data);

            $this->clearCountCache();
            $this->notifyAdmins($comment);

            return $comment;
        });
    }

    /**
     * @param array<string, mixed> $data
     */
    public function storeReply(Comment $parentComment, ?User $user, array $data, ?string $ipAddress = null): Comment
    {
        return DB::transaction(function () use ($parentComment, $user, $data, $ipAddress): Comment {
            $parentComment->loadMissing('commentable');
            $commentable = $parentComment->commentable;

            $isContentAuthor = $user !== null
                && $commentable !== null
                && (int) $commentable->user_id === $user->id;

            $replyData = [
                'commentable_type' => $parentComment->commentable_type,
                'commentable_id'   => $parentComment->commentable_id,
                'parent_id'        => $parentComment->id,
                'body'             => $data['body'],
                'rating'           => null,
                'ip_address'       => $ipAddress,
            ];

            if ($user !== null) {
                $replyData['user_id'] = $user->id;
            } else {
                $replyData['first_name'] = $data['first_name'];
                $replyData['last_name']  = $data['last_name'];
                $replyData['email']      = $data['email'];
            }

            if ($isContentAuthor) {
                $replyData['is_approved'] = true;
                $replyData['approved_at'] = now();
                $replyData['approved_by'] = $user->id;
            }

            $reply = Comment::create($replyData);

            $this->clearCountCache();

            if (!$isContentAuthor) {
                $this->notifyAdmins($reply);
            }

            return $reply;
        });
    }

    public function approve(Comment $comment, User $admin): Comment
    {
        return DB::transaction(function () use ($comment, $admin): Comment {
            $comment->update([
                'is_approved' => true,
                'approved_at' => now(),
                'approved_by' => $admin->id,
            ]);

            $this->clearCountCache();
            $this->notifyAuthor($comment);

            return $comment;
        });
    }

    public function reject(Comment $comment): void
    {
        $comment->update([
            'is_approved' => false,
            'approved_at' => null,
            'approved_by' => null,
        ]);

        $this->clearCountCache();
    }

    public function findById(int $id): ?Comment
    {
        return Comment::with(['commentable', 'approver', 'user'])->find($id);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(Comment $comment, array $data): Comment
    {
        $comment->update($data);

        return $comment;
    }

    public function destroy(Comment $comment): void
    {
        $comment->delete();
        $this->clearCountCache();
    }

    /**
     * @param array<string, mixed> $filters
     */
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return Comment::query()
            ->with(['commentable', 'user'])
            ->when($filters['type'] ?? null, function (Builder $q, string $type): void {
                $morphClass = match ($type) {
                    'icerik' => LiteraryWork::class,
                    'blog'   => Post::class,
                    default  => null,
                };
                if ($morphClass) {
                    $q->where('commentable_type', $morphClass);
                }
            })
            ->when($filters['status'] ?? null, function (Builder $q, string $status): void {
                match ($status) {
                    'approved' => $q->where('is_approved', true),
                    'pending'  => $q->where('is_approved', false),
                    default    => null,
                };
            })
            ->when($filters['search'] ?? null, function (Builder $q, string $search): void {
                $q->where(function (Builder $q2) use ($search): void {
                    $q2->where('first_name', 'like', "%{$search}%")
                       ->orWhere('last_name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%")
                       ->orWhere('body', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @return array<string, int>
     */
    public function getStats(): array
    {
        return [
            'total'    => Comment::count(),
            'pending'  => Comment::where('is_approved', false)->count(),
            'approved' => Comment::where('is_approved', true)->count(),
            'icerik'   => Comment::where('commentable_type', LiteraryWork::class)->count(),
            'blog'     => Comment::where('commentable_type', Post::class)->count(),
        ];
    }

    public function getPendingCount(): int
    {
        return Cache::remember('comments.pending_count', 300, function (): int {
            return Comment::where('is_approved', false)->count();
        });
    }

    public function clearCountCache(): void
    {
        Cache::forget('comments.pending_count');
    }

    private function notifyAdmins(Comment $comment): void
    {
        $comment->loadMissing('user');
        $admins = User::whereHas('role', fn (Builder $q) => $q->whereIn('slug', ['admin', 'super-admin']))->get();

        foreach ($admins as $admin) {
            if (!$admin->wantsMailNotification('new_comment')) {
                continue;
            }
            $this->sendMailSafely(
                fn () => Mail::to($admin->email, $admin->name)->send(new NewCommentMail($comment)),
                'notifyAdmins',
                $comment,
            );
        }
    }

    private function notifyAuthor(Comment $comment): void
    {
        $commentable = $comment->commentable;

        if (!$commentable) {
            return;
        }

        $author = $commentable->author;

        if (!$author) {
            return;
        }

        if (!$author->wantsMailNotification('comment_approved')) {
            return;
        }

        $this->sendMailSafely(
            fn () => Mail::to($author->email, $author->name)->send(new CommentApprovedMail($comment)),
            'notifyAuthor',
            $comment,
        );
    }

    private function sendMailSafely(\Closure $mailCallback, string $action, Comment $comment): bool
    {
        try {
            $mailCallback();

            return true;
        } catch (\Throwable $e) {
            Log::error("Mail gönderilemedi [{$action}] — Yorum #{$comment->id}: {$e->getMessage()}");

            return false;
        }
    }
}
