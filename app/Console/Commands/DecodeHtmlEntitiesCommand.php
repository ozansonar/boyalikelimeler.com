<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\LiteraryWork;
use App\Models\Page;
use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DecodeHtmlEntitiesCommand extends Command
{
    protected $signature = 'app:decode-html-entities {--dry-run : Show affected rows without updating}';

    protected $description = 'Decode HTML entities (e.g. &uuml; → ü) in body/excerpt/title fields saved by TinyMCE';

    /** @var array<string, list<string>> */
    private array $targets = [
        LiteraryWork::class => ['title', 'body', 'excerpt'],
        Post::class         => ['title', 'body', 'excerpt'],
        Page::class         => ['title', 'body', 'excerpt'],
    ];

    public function handle(): int
    {
        $dryRun  = (bool) $this->option('dry-run');
        $total   = 0;

        foreach ($this->targets as $model => $columns) {
            $table = (new $model())->getTable();
            $this->info("Tablo: {$table}");

            foreach ($columns as $column) {
                $rows = $model::query()
                    ->where($column, 'LIKE', '%&%')
                    ->whereNotNull($column)
                    ->get(['id', $column]);

                $updated = 0;

                foreach ($rows as $row) {
                    $original = $row->{$column};
                    $decoded  = html_entity_decode($original, ENT_QUOTES | ENT_HTML5, 'UTF-8');

                    if ($decoded !== $original) {
                        if ($dryRun) {
                            $this->line("  [{$column}] ID #{$row->id} — değişecek");
                        } else {
                            DB::table($table)
                                ->where('id', $row->id)
                                ->update([$column => $decoded]);
                        }
                        $updated++;
                    }
                }

                if ($updated > 0) {
                    $this->info("  {$column}: {$updated} kayıt " . ($dryRun ? 'etkilenecek' : 'güncellendi'));
                }

                $total += $updated;
            }
        }

        if ($total === 0) {
            $this->info('Düzeltilecek kayıt bulunamadı.');
        } else {
            $label = $dryRun ? 'etkilenecek' : 'güncellendi';
            $this->info("Toplam: {$total} kayıt {$label}.");
        }

        return self::SUCCESS;
    }
}
