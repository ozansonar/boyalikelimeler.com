<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\EditorImageUploadRequest;
use App\Models\EditorImage;
use App\Services\EditorImageService;
use Illuminate\Http\JsonResponse;

final class EditorImageController extends Controller
{
    public function __construct(
        private readonly EditorImageService $editorImageService,
    ) {}

    /**
     * POST /editor/images — Upload a new editor image.
     */
    public function store(EditorImageUploadRequest $request): JsonResponse
    {
        $user = $request->user();
        $image = $this->editorImageService->upload($user, $request->file('file'));

        return response()->json([
            'success'  => true,
            'location' => $image->url,
            'image'    => [
                'id'        => $image->id,
                'url'       => $image->url,
                'thumb_url' => $image->thumb_url,
                'name'      => $image->original_name,
                'size'      => $image->file_size,
                'width'     => $image->width,
                'height'    => $image->height,
                'date'      => $image->created_at->format('d.m.Y H:i'),
            ],
        ]);
    }

    /**
     * GET /editor/images — List current user's editor images.
     */
    public function index(): JsonResponse
    {
        $user = auth()->user();
        $images = $this->editorImageService->listForUser($user);

        $data = $images->map(fn (EditorImage $img) => [
            'id'        => $img->id,
            'url'       => $img->url,
            'thumb_url' => $img->thumb_url,
            'name'      => $img->original_name,
            'size'      => $img->file_size,
            'width'     => $img->width,
            'height'    => $img->height,
            'date'      => $img->created_at->format('d.m.Y H:i'),
        ]);

        return response()->json([
            'success' => true,
            'images'  => $data,
        ]);
    }

    /**
     * DELETE /editor/images/{editorImage} — Delete an editor image.
     */
    public function destroy(EditorImage $editorImage): JsonResponse
    {
        $user = auth()->user();
        $deleted = $this->editorImageService->delete($user, $editorImage);

        if (! $deleted) {
            return response()->json(['success' => false, 'message' => 'Bu görseli silme yetkiniz yok.'], 403);
        }

        return response()->json(['success' => true]);
    }
}
