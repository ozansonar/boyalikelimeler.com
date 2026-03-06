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
     * Respects author-set width attributes and inline styles.
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

                // Detect author-set width from HTML attribute or inline style
                $authorWidth = null;
                if (preg_match('/\bwidth=["\'](\d+)["\']/i', $attrs, $wMatch)) {
                    $authorWidth = (int) $wMatch[1];
                } elseif (preg_match('/\bstyle=["\'][^"\']*\bwidth\s*:\s*(\d+)px/i', $attrs, $sMatch)) {
                    $authorWidth = (int) $sMatch[1];
                }

                $base = substr($src, 0, -5); // strip .webp

                // Build srcset with only variants smaller than author width (or all if no width set)
                $variants = [
                    ['suffix' => '-sm.webp', 'w' => 480],
                    ['suffix' => '-md.webp', 'w' => 768],
                    ['suffix' => '-lg.webp', 'w' => 1200],
                ];

                $srcsetParts = [];
                foreach ($variants as $v) {
                    if ($authorWidth && $v['w'] > $authorWidth) {
                        continue;
                    }
                    $srcsetParts[] = $base . $v['suffix'] . ' ' . $v['w'] . 'w';
                }

                // If no variants fit (author width < 480), skip srcset entirely
                if (empty($srcsetParts)) {
                    return $tag;
                }

                $srcset = implode(', ', $srcsetParts);

                // Build sizes: respect author width or use content-column-aware default
                if ($authorWidth) {
                    $sizes = "(max-width: {$authorWidth}px) 100vw, {$authorWidth}px";
                } else {
                    // Content column is ~66% of container on desktop (col-lg-8)
                    $sizes = '(max-width: 575.98px) calc(100vw - 2rem), (max-width: 991.98px) calc(100vw - 3rem), calc(66.667vw - 4rem)';
                }

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
