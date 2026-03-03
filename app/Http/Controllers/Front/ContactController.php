<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\ContactStoreRequest;
use App\Services\ContactService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

final class ContactController extends Controller
{
    public function __construct(
        private readonly ContactService $contactService,
    ) {}

    public function show(): View
    {
        return view('front.contact');
    }

    public function store(ContactStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $this->contactService->store([
            'name'       => $validated['fullname'],
            'email'      => $validated['email'],
            'subject'    => $validated['subject'],
            'message'    => $validated['message'],
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.',
        ]);
    }
}
