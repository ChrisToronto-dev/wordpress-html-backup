<?php

namespace App\Jobs;

use App\Services\BackupService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunBackupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $url;
    protected string $jobId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $url)
    {
        $this->url = $url;
        $this->jobId = uniqid('backup_');
    }

    /**
     * Execute the job.
     */
    public function handle(BackupService $service): void
    {
        $service->run($this->url, $this->jobId);
    }

    /**
     * Get the unique Job ID assigned to this backup.
     */
    public function getJobId(): string
    {
        return $this->jobId;
    }
}
