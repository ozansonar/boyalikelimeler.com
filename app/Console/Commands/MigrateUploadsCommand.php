<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\LiteraryWork;
use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use App\Services\UploadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Migrate existing UUID-named uploads to the new naming convention
 * with WebP conversion and responsive variants.
 *
 * Usage: php artisan uploads:migrate
 */
class MigrateUploadsCommand extends Command
{
    protected $signature = 'uploads:migrate {--dry-run : Show what would be done without making changes}';
    protected $description = 'Eski UUID görselleri yeni format + WebP + responsive varyantlara dönüştür';

    private readonly UploadService $uploadService;

    public function __construct(UploadService $uploadService)
    {
        parent::__construct();
        $this->uploadService = $uploadService;
    }

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY RUN — herhangi bir değişiklik yapılmayacak.');
        }

        $this->migrateModel(User::class, 'avatar', 'avatars', fn (User $u) => Str::slug($u->name));
        $this->migrateModel(User::class, 'cover_image', 'covers', fn (User $u) => Str::slug($u->name));
        $this->migrateModel(LiteraryWork::class, 'cover_image', 'literary', fn (LiteraryWork $w) => $w->slug ?? Str::slug($w->title));
        $this->migrateModel(Post::class, 'cover_image', 'posts', fn (Post $p) => $p->slug ?? Str::slug($p->title));
        $this->migrateModel(Page::class, 'cover_image', 'pages', fn (Page $p) => $p->slug ?? Str::slug($p->title));

        $this->newLine();
        $this->info('Migrasyon tamamlandı.');

        return self::SUCCESS;
    }

    /**
     * @param  class-string  $modelClass
     */
    private function migrateModel(string $modelClass, string $field, string $directory, \Closure $slugCallback): void
    {
        $dryRun = $this->option('dry-run');
        $modelName = class_basename($modelClass);

        $records = $modelClass::whereNotNull($field)->where($field, '!=', '')->get();
        $total = $records->count();

        if ($total === 0) {
            $this->line("  {$modelName}.{$field}: 0 kayıt — atlanıyor.");
            return;
        }

        $this->info("  {$modelName}.{$field}: {$total} kayıt işlenecek...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $migrated = 0;
        $skipped = 0;

        foreach ($records as $record) {
            $oldPath = $record->{$field};
            $fullPath = public_path('uploads/' . $oldPath);

            // Already migrated (has date pattern in name) or file doesn't exist
            if (! File::exists($fullPath)) {
                $skipped++;
                $bar->advance();
                continue;
            }

            // Check if already in new format (contains date pattern like -20260303-)
            if (preg_match('/-\d{14}-[a-z0-9]{5}\.webp$/', $oldPath)) {
                $skipped++;
                $bar->advance();
                continue;
            }

            if ($dryRun) {
                $slug = $slugCallback($record);
                $this->line("  [DRY] {$oldPath} → {$directory}/{$slug}-*.webp");
                $bar->advance();
                continue;
            }

            try {
                $slug = $slugCallback($record);

                // Create a fake UploadedFile from existing file
                $tempFile = new \Illuminate\Http\UploadedFile(
                    $fullPath,
                    basename($fullPath),
                    mime_content_type($fullPath) ?: 'image/jpeg',
                    null,
                    true,
                );

                $newPath = $this->uploadService->uploadImage($tempFile, $directory, $slug);

                // Update DB record
                $record->update([$field => $newPath]);

                // Move old file to originals (it's already copied by uploadImage, so delete old location)
                // Note: uploadImage already copies the source to originals/ via $file->move()
                // The original at $fullPath is now the source that was processed

                $migrated++;
            } catch (\Throwable $e) {
                $this->error("  HATA: {$oldPath} — {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->line("  → {$migrated} migrated, {$skipped} skipped");
    }
}
