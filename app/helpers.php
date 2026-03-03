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

if (! function_exists('responsive_body')) {
    /**
     * Process HTML body content: add srcset to editor-uploaded images
     * so the browser picks the best variant for the viewport.
     */
    function responsive_body(?string $html): string
    {
        if (! $html) {
            return '';
        }

        return (string) preg_replace_callback(
            '/<img\b([^>]*)>/i',
            static function (array $matches): string {
                $tag   = $matches[0];
                $attrs = $matches[1];

                // Skip if already has srcset
                if (stripos($attrs, 'srcset') !== false) {
                    return $tag;
                }

                // Only process uploads/*.webp images
                if (! preg_match('/\bsrc=["\']([^"\']*\/uploads\/[^"\']+\.webp)["\']/i', $attrs, $srcMatch)) {
                    return $tag;
                }

                $src = $srcMatch[1];

                // Skip variant files (already a sized file)
                if (preg_match('/-(thumb|sm|md|lg)\.webp$/i', $src)) {
                    return $tag;
                }

                $base   = substr($src, 0, -5); // strip .webp
                $srcset = $base . '-sm.webp 480w, ' . $base . '-md.webp 768w, ' . $base . '-lg.webp 1200w';
                $sizes  = '(max-width: 480px) 480px, (max-width: 768px) 768px, 1200px';

                return str_replace(
                    $srcMatch[0],
                    $srcMatch[0] . ' srcset="' . $srcset . '" sizes="' . $sizes . '"',
                    $tag,
                );
            },
            $html,
        ) ?? $html;
    }
}
