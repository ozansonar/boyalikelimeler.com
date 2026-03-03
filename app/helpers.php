<?php

declare(strict_types=1);

if (! function_exists('upload_url')) {
    /**
     * Generate URL for an uploaded file in public/uploads directory.
     * Delegates to UploadService::url() for consistent URL generation.
     */
    function upload_url(?string $path, ?string $size = null): ?string
    {
        return \App\Services\UploadService::url($path, $size);
    }
}
