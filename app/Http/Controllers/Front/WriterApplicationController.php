<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\WriterApplicationRequest;
use App\Services\WriterApplicationService;
use Illuminate\Http\JsonResponse;

class WriterApplicationController extends Controller
{
    public function __construct(
        private readonly WriterApplicationService $service,
    ) {}

    public function store(WriterApplicationRequest $request): JsonResponse
    {
        $user = $request->user();
        $check = $this->service->canUserApply($user);

        if (!$check['can_apply']) {
            $message = match ($check['reason']) {
                'already_writer'   => 'Zaten yazar rolüne sahipsiniz.',
                'pending'          => 'Zaten bekleyen bir başvurunuz var.',
                'cooldown'         => 'Reddedilen başvurudan sonra 30 gün beklemeniz gerekmektedir.',
                'already_approved' => 'Başvurunuz zaten onaylanmış.',
                default            => 'Başvuru yapılamaz.',
            };

            return response()->json(['success' => false, 'message' => $message], 422);
        }

        $this->service->store($user, $request->validated('motivation'));

        return response()->json([
            'success' => true,
            'message' => 'Başvurunuz başarıyla alındı! E-posta adresinize onay bilgisi gönderildi.',
        ]);
    }
}
