<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\FavoriteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class FavoriteController extends Controller
{
    public function __construct(
        private readonly FavoriteService $favoriteService,
    ) {}

    public function toggle(Request $request): JsonResponse
    {
        $request->validate([
            'type' => ['required', 'string', 'in:post,literary_work'],
            'id'   => ['required', 'integer', 'min:1'],
        ]);

        $result = $this->favoriteService->toggle(
            $request->user(),
            $request->input('type'),
            (int) $request->input('id'),
        );

        return response()->json($result);
    }
}
