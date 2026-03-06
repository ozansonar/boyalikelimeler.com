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
     * Respects img-w-{size} classes set by the editor toolbar.
     */
    function responsive_body(?string $html): string
    {
        if (! $html) {
            return '';
        }

        // Map img-w classes to percentage of content column (~800px at desktop)
        $widthMap = [
            'img-w-20'  => 0.20,
            'img-w-40'  => 0.40,
            'img-w-60'  => 0.60,
            'img-w-80'  => 0.80,
            'img-w-100' => 1.00,
        ];

        return (string) preg_replace_callback(
            '/<img\b([^>]*)>/i',
            static function (array $matches) use ($widthMap): string {
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

                // Detect img-w class to calculate appropriate sizes
                $ratio = 1.00;
                foreach ($widthMap as $cls => $pct) {
                    if (stripos($attrs, $cls) !== false) {
                        $ratio = $pct;
                        break;
                    }
                }

                $base = substr($src, 0, -5); // strip .webp

                // Content column is ~800px on desktop. Calculate max display px.
                $maxDisplayPx = (int) round(800 * $ratio);

                // Build srcset with only useful variants
                $allVariants = [
                    ['suffix' => '-sm.webp', 'w' => 480],
                    ['suffix' => '-md.webp', 'w' => 768],
                    ['suffix' => '-lg.webp', 'w' => 1200],
                ];

                $srcsetParts = [];
                foreach ($allVariants as $v) {
                    $srcsetParts[] = $base . $v['suffix'] . ' ' . $v['w'] . 'w';
                    // Stop adding larger variants once we've exceeded display need
                    if ($v['w'] >= $maxDisplayPx) {
                        break;
                    }
                }

                $srcset = implode(', ', $srcsetParts);

                // Build sizes: percentage of content column width
                if ($ratio < 1.00) {
                    $pctStr = (int) ($ratio * 100);
                    $sizes = '(max-width: 575.98px) calc(' . $pctStr . 'vw), calc(' . $pctStr . 'vw - 2rem)';
                } else {
                    // Full width: use content column aware sizes
                    $sizes = '(max-width: 575.98px) calc(100vw - 2rem), (max-width: 991.98px) calc(100vw - 3rem), 800px';
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
