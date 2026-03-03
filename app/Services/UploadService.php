<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

final class UploadService
{
    /**
     * Responsive variant definitions.
     * thumb = 150x150 square crop, others = max width with aspect ratio.
     *
     * @var array<string, array{width: int, height: ?int, crop: bool}>
     */
    private const VARIANTS = [
        'thumb' => ['width' => 150,  'height' => 150,  'crop' => true],
        'sm'    => ['width' => 480,  'height' => null,  'crop' => false],
        'md'    => ['width' => 768,  'height' => null,  'crop' => false],
        'lg'    => ['width' => 1200, 'height' => null,  'crop' => false],
    ];

    private const WEBP_QUALITY = 82;

    // ─── Public API ───────────────────────────────────────────────

    /**
     * Upload an image: save original, convert to WebP, create responsive variants.
     *
     * @param  string       $directory   Sub-directory under public/uploads (e.g. "literary", "avatars")
     * @param  string|null  $slug        SEO-friendly slug for filename (falls back to random)
     * @param  array|null   $dimensions  Target dimensions for the main file ['width' => int, 'height' => int, 'crop' => bool]
     * @return string  Relative path stored in DB (e.g. "literary/kahve-fali-20260303143025-a7xk2.webp")
     */
    public function uploadImage(UploadedFile $file, string $directory, ?string $slug = null, ?array $dimensions = null): string
    {
        $baseName = $this->generateBaseName($slug);
        $this->ensureDirectory($directory);
        $this->ensureDirectory($directory . '/originals');

        // Save original file (preserve original format)
        $originalExt = strtolower($file->getClientOriginalExtension()) ?: 'jpg';
        $originalName = $baseName . '.' . $originalExt;
        $file->move($this->uploadsPath($directory . '/originals'), $originalName);

        // Convert to WebP (main file — optionally resize/crop to target dimensions)
        $webpName = $baseName . '.webp';
        $originalFullPath = $this->uploadsPath($directory . '/originals/' . $originalName);

        if ($dimensions) {
            $this->convertToWebp(
                $originalFullPath,
                $this->uploadsPath($directory . '/' . $webpName),
                $dimensions['width'],
                $dimensions['height'],
                $dimensions['crop'] ?? true,
            );
        } else {
            $this->convertToWebp($originalFullPath, $this->uploadsPath($directory . '/' . $webpName));
        }

        // Create responsive variants
        $this->createVariants($originalFullPath, $directory, $baseName);

        return $directory . '/' . $webpName;
    }

    /**
     * Replace an existing image: delete old, upload new.
     */
    public function replaceImage(
        UploadedFile $file,
        string $directory,
        ?string $oldPath,
        ?string $slug = null,
        ?array $dimensions = null,
    ): string {
        $this->deleteImage($oldPath);

        return $this->uploadImage($file, $directory, $slug, $dimensions);
    }

    /**
     * Delete an image and all its variants + original.
     */
    public function deleteImage(?string $path): void
    {
        if (! $path) {
            return;
        }

        $fullPath = $this->uploadsPath($path);

        if (! File::exists($fullPath)) {
            return;
        }

        $info = pathinfo($fullPath);
        $directory = $info['dirname'];
        $baseName = $info['filename']; // e.g. kahve-fali-20260303143025-a7xk2

        // Delete main WebP file
        File::delete($fullPath);

        // Delete variant files
        foreach (array_keys(self::VARIANTS) as $size) {
            $variantPath = $directory . '/' . $baseName . '-' . $size . '.webp';
            if (File::exists($variantPath)) {
                File::delete($variantPath);
            }
        }

        // Delete original file (any extension)
        $originalsDir = $directory . '/originals';
        if (File::isDirectory($originalsDir)) {
            $originals = File::glob($originalsDir . '/' . $baseName . '.*');
            foreach ($originals as $original) {
                File::delete($original);
            }
        }
    }

    /**
     * Generate public URL for an uploaded file.
     */
    public static function url(?string $path, ?string $size = null): ?string
    {
        if (! $path) {
            return null;
        }

        if ($size) {
            $info = pathinfo($path);
            $dir = $info['dirname'] !== '.' ? $info['dirname'] . '/' : '';
            $path = $dir . $info['filename'] . '-' . $size . '.webp';
        }

        return asset('uploads/' . ltrim($path, '/'));
    }

    // ─── Private Helpers ──────────────────────────────────────────

    /**
     * Generate a SEO-friendly base filename: {slug}-{YmdHis}-{uniq5}
     */
    private function generateBaseName(?string $slug): string
    {
        $slugPart = $slug ? Str::slug($slug) : Str::random(10);

        // Limit slug length to prevent overly long filenames
        $slugPart = Str::limit($slugPart, 80, '');

        $datePart = now()->format('YmdHis');
        $uniqPart = strtolower(Str::random(5));

        return $slugPart . '-' . $datePart . '-' . $uniqPart;
    }

    /**
     * Convert an image file to WebP format using GD.
     */
    private function convertToWebp(string $sourcePath, string $destPath, ?int $width = null, ?int $height = null, bool $crop = false): void
    {
        $imageInfo = @getimagesize($sourcePath);
        if ($imageInfo === false) {
            // Fallback: just copy the file if we can't process it
            File::copy($sourcePath, $destPath);
            return;
        }

        $mime = $imageInfo['mime'];
        $srcImage = match ($mime) {
            'image/jpeg'  => @imagecreatefromjpeg($sourcePath),
            'image/png'   => @imagecreatefrompng($sourcePath),
            'image/webp'  => @imagecreatefromwebp($sourcePath),
            'image/gif'   => @imagecreatefromgif($sourcePath),
            default       => false,
        };

        if ($srcImage === false) {
            File::copy($sourcePath, $destPath);
            return;
        }

        $srcWidth = imagesx($srcImage);
        $srcHeight = imagesy($srcImage);

        // If no resize needed, convert directly
        if ($width === null && $height === null) {
            imagepalettetotruecolor($srcImage);
            imagealphablending($srcImage, true);
            imagesavealpha($srcImage, true);
            imagewebp($srcImage, $destPath, self::WEBP_QUALITY);
            imagedestroy($srcImage);
            return;
        }

        if ($crop && $width !== null && $height !== null) {
            // Crop mode (for thumbnails)
            $dstImage = $this->cropResize($srcImage, $srcWidth, $srcHeight, $width, $height);
        } else {
            // Fit mode (maintain aspect ratio)
            $dstImage = $this->fitResize($srcImage, $srcWidth, $srcHeight, $width ?? $srcWidth);
        }

        imagepalettetotruecolor($dstImage);
        imagealphablending($dstImage, true);
        imagesavealpha($dstImage, true);
        imagewebp($dstImage, $destPath, self::WEBP_QUALITY);

        imagedestroy($srcImage);
        imagedestroy($dstImage);
    }

    /**
     * Resize by fitting within max width while maintaining aspect ratio.
     *
     * @return \GdImage
     */
    private function fitResize(\GdImage $srcImage, int $srcWidth, int $srcHeight, int $maxWidth): \GdImage
    {
        // Don't upscale
        if ($srcWidth <= $maxWidth) {
            $newWidth = $srcWidth;
            $newHeight = $srcHeight;
        } else {
            $ratio = $maxWidth / $srcWidth;
            $newWidth = $maxWidth;
            $newHeight = (int) round($srcHeight * $ratio);
        }

        $dstImage = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($dstImage, false);
        imagesavealpha($dstImage, true);

        $transparent = imagecolorallocatealpha($dstImage, 0, 0, 0, 127);
        imagefill($dstImage, 0, 0, $transparent);

        imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $srcWidth, $srcHeight);

        return $dstImage;
    }

    /**
     * Center-crop resize (for square thumbnails).
     *
     * @return \GdImage
     */
    private function cropResize(\GdImage $srcImage, int $srcWidth, int $srcHeight, int $dstWidth, int $dstHeight): \GdImage
    {
        $srcRatio = $srcWidth / $srcHeight;
        $dstRatio = $dstWidth / $dstHeight;

        if ($srcRatio > $dstRatio) {
            $cropHeight = $srcHeight;
            $cropWidth = (int) round($srcHeight * $dstRatio);
            $cropX = (int) round(($srcWidth - $cropWidth) / 2);
            $cropY = 0;
        } else {
            $cropWidth = $srcWidth;
            $cropHeight = (int) round($srcWidth / $dstRatio);
            $cropX = 0;
            $cropY = (int) round(($srcHeight - $cropHeight) / 2);
        }

        $dstImage = imagecreatetruecolor($dstWidth, $dstHeight);
        imagealphablending($dstImage, false);
        imagesavealpha($dstImage, true);

        $transparent = imagecolorallocatealpha($dstImage, 0, 0, 0, 127);
        imagefill($dstImage, 0, 0, $transparent);

        imagecopyresampled(
            $dstImage, $srcImage,
            0, 0, $cropX, $cropY,
            $dstWidth, $dstHeight, $cropWidth, $cropHeight,
        );

        return $dstImage;
    }

    /**
     * Create all responsive variants from original file.
     */
    private function createVariants(string $originalPath, string $directory, string $baseName): void
    {
        foreach (self::VARIANTS as $size => $config) {
            $variantPath = $this->uploadsPath($directory . '/' . $baseName . '-' . $size . '.webp');
            $this->convertToWebp(
                $originalPath,
                $variantPath,
                $config['width'],
                $config['height'],
                $config['crop'],
            );
        }
    }

    /**
     * Ensure a directory exists under public/uploads.
     */
    private function ensureDirectory(string $subDir): void
    {
        $path = $this->uploadsPath($subDir);

        if (! File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true);
        }
    }

    /**
     * Get absolute path for a file under public/uploads.
     */
    private function uploadsPath(string $relativePath = ''): string
    {
        return public_path('uploads/' . ltrim($relativePath, '/'));
    }
}
