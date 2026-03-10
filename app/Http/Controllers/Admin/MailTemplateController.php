<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MailTemplateUpdateRequest;
use App\Models\MailTemplate;
use App\Services\MailTemplateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class MailTemplateController extends Controller
{
    public function __construct(
        private readonly MailTemplateService $mailTemplateService,
    ) {}

    public function index(): View
    {
        return view('admin.mail-templates.index', [
            'templates' => $this->mailTemplateService->getAllForAdmin(),
            'stats'     => $this->mailTemplateService->getAdminStats(),
        ]);
    }

    public function edit(MailTemplate $mailTemplate): View
    {
        return view('admin.mail-templates.edit', [
            'template' => $mailTemplate,
        ]);
    }

    public function update(MailTemplateUpdateRequest $request, MailTemplate $mailTemplate): RedirectResponse
    {
        $this->mailTemplateService->update($mailTemplate, [
            'subject'   => $request->validated('subject'),
            'body'      => $request->validated('body'),
            'is_active' => (bool) $request->validated('is_active'),
        ]);

        return redirect()->route('admin.mail-templates.edit', $mailTemplate)
            ->with('success', '"%s" şablonu başarıyla güncellendi.');
    }

    public function reset(MailTemplate $mailTemplate): RedirectResponse
    {
        $this->mailTemplateService->resetToDefault($mailTemplate);

        return redirect()->route('admin.mail-templates.edit', $mailTemplate)
            ->with('success', 'Şablon varsayılan değerlere sıfırlandı.');
    }

    public function resetAll(): RedirectResponse
    {
        $this->mailTemplateService->resetAllToDefaults();

        return redirect()->route('admin.mail-templates.index')
            ->with('success', 'Tüm şablonlar varsayılan değerlere sıfırlandı.');
    }
}
