<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final class ContactService
{
    public function store(array $data): ContactMessage
    {
        return DB::transaction(fn (): ContactMessage => ContactMessage::create($data));
    }

    /**
     * @return array{total: int, unread: int, starred: int, replied: int}
     */
    public function getStats(): array
    {
        $row = ContactMessage::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread,
            SUM(CASE WHEN is_starred = 1 THEN 1 ELSE 0 END) as starred,
            SUM(CASE WHEN replied_at IS NOT NULL THEN 1 ELSE 0 END) as replied
        ")->first();

        return [
            'total'   => (int) $row->total,
            'unread'  => (int) $row->unread,
            'starred' => (int) $row->starred,
            'replied' => (int) $row->replied,
        ];
    }

    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator
    {
        $query = ContactMessage::query();

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['folder'])) {
            match ($filters['folder']) {
                'unread'   => $query->where('is_read', false),
                'starred'  => $query->where('is_starred', true),
                'archived' => $query->where('is_archived', true),
                'replied'  => $query->whereNotNull('replied_at'),
                'trash'    => $query->onlyTrashed(),
                default    => $query->where('is_archived', false),
            };
        } else {
            $query->where('is_archived', false);
        }

        if (! empty($filters['subject'])) {
            $query->where('subject', $filters['subject']);
        }

        return $query
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function find(int $id): ?ContactMessage
    {
        return ContactMessage::withTrashed()->find($id);
    }

    public function markAsRead(ContactMessage $message): void
    {
        if (! $message->is_read) {
            $message->update(['is_read' => true]);
        }
    }

    public function toggleStar(ContactMessage $message): bool
    {
        $message->update(['is_starred' => ! $message->is_starred]);

        return $message->is_starred;
    }

    public function reply(ContactMessage $message, string $replyBody, User $admin): ContactMessage
    {
        return DB::transaction(function () use ($message, $replyBody, $admin): ContactMessage {
            $message->update([
                'reply_body' => $replyBody,
                'replied_by' => $admin->id,
                'replied_at' => now(),
                'is_read'    => true,
            ]);

            return $message->fresh();
        });
    }

    public function archive(ContactMessage $message): void
    {
        $message->update(['is_archived' => true]);
    }

    public function unarchive(ContactMessage $message): void
    {
        $message->update(['is_archived' => false]);
    }

    public function delete(ContactMessage $message): void
    {
        $message->delete();
    }

    public function markAllRead(): int
    {
        return ContactMessage::where('is_read', false)->update(['is_read' => true]);
    }
}
