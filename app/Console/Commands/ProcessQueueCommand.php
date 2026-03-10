<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Shared-hosting compatible queue processor.
 * Does NOT require pcntl extension (unlike queue:work).
 */
class ProcessQueueCommand extends Command
{
    protected $signature = 'queue:process
        {--max=50 : Maximum number of jobs to process}
        {--tries=3 : Maximum attempts per job}
        {--timeout=60 : Job timeout in seconds}';

    protected $description = 'Process queued jobs without pcntl (shared hosting compatible)';

    public function handle(): int
    {
        $max = (int) $this->option('max');
        $maxTries = (int) $this->option('tries');
        $timeout = (int) $this->option('timeout');
        $processed = 0;

        while ($processed < $max) {
            $job = $this->getNextJob();

            if ($job === null) {
                if ($processed === 0) {
                    $this->info('No jobs to process.');
                } else {
                    $this->info("Processed {$processed} job(s). Queue empty.");
                }

                return self::SUCCESS;
            }

            if ($job->attempts >= $maxTries) {
                $this->moveToFailed($job, "Max attempts ({$maxTries}) exceeded");
                $this->warn("Job #{$job->id} exceeded max attempts, moved to failed_jobs.");
                $processed++;

                continue;
            }

            $this->reserveJob($job);

            try {
                $this->info("Processing job #{$job->id}...");

                $payload = json_decode($job->payload, true);
                $jobClass = $payload['displayName'] ?? 'Unknown';

                set_time_limit($timeout + 30);

                $queueJob = new \Illuminate\Queue\Jobs\DatabaseJob(
                    app(),
                    app('queue')->connection('database'),
                    $job,
                    'database',
                    $job->queue,
                );

                $queueJob->fire();

                DB::table('jobs')->where('id', $job->id)->delete();
                $this->info("Job #{$job->id} ({$jobClass}) completed.");
                $processed++;
            } catch (\Throwable $e) {
                $attempts = $job->attempts + 1;

                if ($attempts >= $maxTries) {
                    $this->moveToFailed($job, $e->getMessage());
                    $this->error("Job #{$job->id} failed permanently: {$e->getMessage()}");
                } else {
                    DB::table('jobs')->where('id', $job->id)->update([
                        'attempts' => $attempts,
                        'reserved_at' => null,
                        'available_at' => now()->addSeconds($attempts * 30)->timestamp,
                    ]);
                    $this->warn("Job #{$job->id} failed (attempt {$attempts}), will retry.");
                    $this->error("  Error: {$e->getMessage()}");
                    $this->line("  File: {$e->getFile()}:{$e->getLine()}");
                }

                $processed++;
            }
        }

        $this->info("Processed {$processed} job(s). Limit reached.");

        return self::SUCCESS;
    }

    private function getNextJob(): ?object
    {
        return DB::table('jobs')
            ->where('queue', 'default')
            ->whereNull('reserved_at')
            ->where('available_at', '<=', now()->timestamp)
            ->orderBy('id')
            ->first();
    }

    private function reserveJob(object $job): void
    {
        DB::table('jobs')->where('id', $job->id)->update([
            'reserved_at' => now()->timestamp,
            'attempts' => $job->attempts + 1,
        ]);
    }

    private function moveToFailed(object $job, string $exception): void
    {
        $payload = json_decode($job->payload, true);

        DB::table('failed_jobs')->insert([
            'uuid' => $payload['uuid'] ?? \Illuminate\Support\Str::uuid()->toString(),
            'connection' => 'database',
            'queue' => $job->queue,
            'payload' => $job->payload,
            'exception' => mb_substr($exception, 0, 5000),
            'failed_at' => now(),
        ]);

        DB::table('jobs')->where('id', $job->id)->delete();
    }
}
