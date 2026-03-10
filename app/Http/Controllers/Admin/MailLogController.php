<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailLog;
use App\Services\MailLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MailLogController extends Controller
{
    public function __construct(
        private readonly MailLogService $mailLogService,
    ) {}

    public function index(Request $request): View
    {
        $perPage = in_array((int) $request->input('per_page'), [10, 25, 50, 100], true)
            ? (int) $request->input('per_page')
            : 25;

        $filters = $request->only(['search', 'status', 'date_from', 'date_to']);

        return view('admin.mail-logs.index', [
            'logs'    => $this->mailLogService->paginate($perPage, $filters),
            'stats'   => $this->mailLogService->getAdminStats(),
            'filters' => $filters,
            'perPage' => $perPage,
        ]);
    }

    public function show(MailLog $mailLog): View
    {
        $mailLog->load('user');

        $isHtml = $mailLog->body !== null
            && $mailLog->body !== ''
            && preg_match('/<[a-z][\s\S]*>/i', $mailLog->body) === 1;

        return view('admin.mail-logs.show', [
            'log'    => $mailLog,
            'isHtml' => $isHtml,
        ]);
    }

    public function resend(MailLog $mailLog): RedirectResponse
    {
        if (!$mailLog->body) {
            return redirect()->route('admin.mail-logs.show', $mailLog)
                ->with('error', 'Bu mailin içeriği kayıtlı değil, yeniden gönderilemez.');
        }

        $newLog = $this->mailLogService->resend($mailLog);

        if ($newLog->isSent()) {
            return redirect()->route('admin.mail-logs.show', $newLog)
                ->with('success', 'Mail başarıyla yeniden gönderildi.');
        }

        return redirect()->route('admin.mail-logs.show', $newLog)
            ->with('error', 'Mail gönderilemedi: ' . ($newLog->error_message ?? 'Bilinmeyen hata'));
    }

    public function destroy(MailLog $mailLog): RedirectResponse
    {
        $this->mailLogService->delete($mailLog);

        return redirect()->route('admin.mail-logs.index')
            ->with('success', 'Mail kaydı başarıyla silindi.');
    }
}
