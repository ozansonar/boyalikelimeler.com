<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContactReplyRequest;
use App\Services\ContactService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ContactController extends Controller
{
    public function __construct(
        private readonly ContactService $contactService,
    ) {}

    public function index(Request $request): View
    {
        $stats = $this->contactService->getStats();
        $messages = $this->contactService->paginate(20, [
            'search'  => $request->query('search'),
            'folder'  => $request->query('folder'),
            'subject' => $request->query('subject'),
        ]);

        return view('admin.contacts.index', compact('stats', 'messages'));
    }

    public function show(int $id): JsonResponse
    {
        $message = $this->contactService->find($id);

        if (! $message) {
            return response()->json(['error' => 'Mesaj bulunamadı.'], 404);
        }

        $this->contactService->markAsRead($message);

        return response()->json([
            'id'           => $message->id,
            'name'         => $message->name,
            'email'        => $message->email,
            'subject'      => $message->subject,
            'subject_label' => $message->subjectLabel(),
            'subject_color' => $message->subjectColor(),
            'message'      => nl2br(e($message->message)),
            'is_read'      => $message->is_read,
            'is_starred'   => $message->is_starred,
            'is_archived'  => $message->is_archived,
            'reply_body'   => $message->reply_body,
            'replied_at'   => $message->replied_at?->format('d.m.Y H:i'),
            'replied_by'   => $message->repliedByUser?->name,
            'ip_address'   => $message->ip_address,
            'created_at'   => $message->created_at->format('d.m.Y H:i'),
            'initials'     => mb_strtoupper(mb_substr($message->name, 0, 2)),
        ]);
    }

    public function reply(ContactReplyRequest $request, int $id): JsonResponse
    {
        $message = $this->contactService->find($id);

        if (! $message) {
            return response()->json(['error' => 'Mesaj bulunamadı.'], 404);
        }

        $this->contactService->reply(
            $message,
            $request->validated()['reply_body'],
            $request->user(),
        );

        return response()->json([
            'success'    => true,
            'message'    => 'Yanıt başarıyla gönderildi.',
            'replied_at' => now()->format('d.m.Y H:i'),
            'replied_by' => $request->user()->name,
        ]);
    }

    public function toggleStar(int $id): JsonResponse
    {
        $message = $this->contactService->find($id);

        if (! $message) {
            return response()->json(['error' => 'Mesaj bulunamadı.'], 404);
        }

        $starred = $this->contactService->toggleStar($message);

        return response()->json(['success' => true, 'is_starred' => $starred]);
    }

    public function archive(int $id): JsonResponse
    {
        $message = $this->contactService->find($id);

        if (! $message) {
            return response()->json(['error' => 'Mesaj bulunamadı.'], 404);
        }

        $this->contactService->archive($message);

        return response()->json(['success' => true, 'message' => 'Mesaj arşivlendi.']);
    }

    public function destroy(int $id): JsonResponse
    {
        $message = $this->contactService->find($id);

        if (! $message) {
            return response()->json(['error' => 'Mesaj bulunamadı.'], 404);
        }

        $this->contactService->delete($message);

        return response()->json(['success' => true, 'message' => 'Mesaj silindi.']);
    }

    public function markAllRead(): JsonResponse
    {
        $count = $this->contactService->markAllRead();

        return response()->json([
            'success' => true,
            'message' => $count . ' mesaj okundu olarak işaretlendi.',
        ]);
    }
}
