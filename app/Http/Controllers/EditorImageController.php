<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\EditorImageUploadRequest;
use App\Models\EditorImage;
use App\Models\User;
use App\Services\EditorImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $user = $this->resolveContextUser($request);
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
    public function index(Request $request): JsonResponse
    {
        $user = $this->resolveContextUser($request);
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
    public function destroy(EditorImage $editorImage, Request $request): JsonResponse
    {
        $user = $this->resolveContextUser($request);
        $deleted = $this->editorImageService->delete($user, $editorImage);

        if (! $deleted) {
            return response()->json(['success' => false, 'message' => 'Bu görseli silme yetkiniz yok.'], 403);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Resolve the target user for editor images.
     *
     * Admin/SuperAdmin can pass context_user_id to manage images
     * on behalf of another user (e.g. when editing a literary work).
     */
    private function resolveContextUser(Request $request): User
    {
        $authUser = $request->user();

        $contextUserId = (int) $request->input('context_user_id', 0);

        if ($contextUserId > 0 && $contextUserId !== (int) $authUser->id) {
            if ($authUser->isAdmin() || $authUser->isSuperAdmin()) {
                $contextUser = User::find($contextUserId);
                if ($contextUser) {
                    return $contextUser;
                }
            }
        }

        return $authUser;
    }
}
