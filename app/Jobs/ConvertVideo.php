<?php

namespace App\Jobs;

use App\Traits\VideoTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class ConvertVideo implements ShouldQueue
{
    use Queueable;
    use VideoTrait;
    public $tries = 5;
    public $timeout = 3600 * 15;
    public  $backoff = 10;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //On convertit le fichier
        $this->movie->update(['status' => 'converting']);
        $this->convertAll();

        //On déplace le fichier dans le S3
        MovingToS3::dispatchSync($this->movie);
    }

    /**
     * Handle a job failure.
     */
    public function failed(?Throwable $exception): void
    {
        $this->movie->update(['status' => 'failed (ConvertVideo)']);
    }
}
