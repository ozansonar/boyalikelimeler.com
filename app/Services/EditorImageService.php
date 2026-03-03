<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EditorImage;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

final class EditorImageService
{
    public function __construct(
        private readonly UploadService $uploadService,
    ) {}

    /**
     * Upload an editor image for a user.
     * Directory: images/{user_id}/
     */
    public function upload(User $user, UploadedFile $file): EditorImage
    {
        $directory = 'images/' . $user->id;
        $slug = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        // Capture metadata BEFORE file is moved by uploadImage
        $originalName = $file->getClientOriginalName();
        $fileSize = (int) $file->getSize();
        $imageInfo = @getimagesize($file->getPathname());
        $width = $imageInfo ? (int) $imageInfo[0] : 0;
        $height = $imageInfo ? (int) $imageInfo[1] : 0;

        $path = $this->uploadService->uploadImage($file, $directory, $slug);

        return DB::transaction(function () use ($user, $path, $originalName, $fileSize, $width, $height): EditorImage {
            return EditorImage::create([
                'user_id'       => $user->id,
                'path'          => $path,
                'original_name' => $originalName,
                'file_size'     => $fileSize,
                'width'         => $width,
                'height'        => $height,
            ]);
        });
    }

    /**
     * List all editor images for a user, newest first.
     */
    public function listForUser(User $user): Collection
    {
        return $user->editorImages()
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Delete an editor image (soft delete record, hard delete files).
     */
    public function delete(User $user, EditorImage $image): bool
    {
        if ((int) $image->user_id !== (int) $user->id) {
            return false;
        }

        return DB::transaction(function () use ($image): bool {
            $this->uploadService->deleteImage($image->path);
            $image->delete();
            return true;
        });
    }
}
