<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\PwaInstallPlatform;
use App\Http\Requests\PwaInstallRequest;
use App\Services\PwaInstallService;
use Illuminate\Http\JsonResponse;

class PwaInstallController extends Controller
{
    public function __construct(
        private readonly PwaInstallService $pwaInstallService,
    ) {}

    public function store(PwaInstallRequest $request): JsonResponse
    {
        $this->pwaInstallService->record([
            'platform'   => $request->input('platform', PwaInstallPlatform::Unknown->value),
            'user_agent' => $request->userAgent(),
            'referrer'   => $request->input('referrer'),
            'ip_hash'    => hash('sha256', (string) $request->ip()),
        ]);

        return response()->json(['success' => true]);
    }
}
