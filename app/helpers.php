<?php

declare(strict_types=1);

if (! function_exists('upload_url')) {
    /**
     * Generate URL for an uploaded file in public/uploads directory.
     */
    function upload_url(?string $path, ?string $size = null): ?string
    {
        if (! $path) {
            return null;
        }

        if ($size) {
            $info = pathinfo($path);
            $path = $info['dirname'] . '/' . $info['filename'] . '-' . $size . '.' . ($info['extension'] ?? 'webp');
        }

        return asset('uploads/' . ltrim($path, '/'));
    }
}
