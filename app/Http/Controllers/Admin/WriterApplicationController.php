<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\WriterApplicationStatus;
use App\Http\Controllers\Controller;
use App\Models\WriterApplication;
use App\Services\WriterApplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class WriterApplicationController extends Controller
{
    public function __construct(
        private readonly WriterApplicationService $service,
    ) {}

    public function index(Request $request): View
    {
        $statusFilter = $request->query('status');
        $status = $statusFilter ? WriterApplicationStatus::tryFrom($statusFilter) : null;

        $statusCounts = $this->service->getStatusCounts();
        $applications = $this->service->getPaginated($status);

        return view('admin.writer-applications.index', compact('applications', 'statusCounts', 'statusFilter'));
    }

    public function show(WriterApplication $writerApplication): View
    {
        $writerApplication->load(['user', 'reviewer']);

        return view('admin.writer-applications.show', [
            'application' => $writerApplication,
        ]);
    }

    public function approve(WriterApplication $writerApplication): RedirectResponse
    {
        if ($writerApplication->status !== WriterApplicationStatus::Pending) {
            return redirect()
                ->route('admin.writer-applications.show', $writerApplication)
                ->with('error', 'Bu başvuru zaten değerlendirilmiş.');
        }

        $this->service->approve($writerApplication, auth()->user());

        return redirect()
            ->route('admin.writer-applications.show', $writerApplication)
            ->with('success', 'Başvuru onaylandı ve kullanıcı yazar rolüne yükseltildi.');
    }

    public function reject(Request $request, WriterApplication $writerApplication): RedirectResponse
    {
        if ($writerApplication->status !== WriterApplicationStatus::Pending) {
            return redirect()
                ->route('admin.writer-applications.show', $writerApplication)
                ->with('error', 'Bu başvuru zaten değerlendirilmiş.');
        }

        $request->validate([
            'admin_note' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'admin_note.required' => 'Red gerekçesi zorunludur.',
            'admin_note.min'      => 'Red gerekçesi en az 10 karakter olmalıdır.',
        ]);

        $this->service->reject($writerApplication, auth()->user(), $request->input('admin_note'));

        return redirect()
            ->route('admin.writer-applications.show', $writerApplication)
            ->with('success', 'Başvuru reddedildi ve kullanıcıya bildirim gönderildi.');
    }
}
