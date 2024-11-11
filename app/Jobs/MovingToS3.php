<?php

namespace App\Jobs;

use App\Models\Movie;
use App\Models\Video;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class MovingToS3 implements ShouldQueue
{
    use Queueable;
    public $tries = 5;
    public $timeout = 36000;
    public  $backoff = 10;

    /**
     * Create a new job instance.
     */
    public function __construct(public Video $video)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->video->watchable->update(['status' => 'moving to S3']);
        //On supprime input.mkv
        Storage::disk('public')->delete($this->video->path.'/input.mkv');

        //On déplace tout  dans le S3
        $allFiles = collect(Storage::disk('public')->files(directory: $this->video->path, recursive: true))->each(function($file) {
            Storage::disk('s3')->put($file, Storage::disk('public')->get($file));
        });

        //On supprime tout dans le public
        Storage::disk('public')->deleteDirectory($this->video->path);

        $this->video->watchable->update(['status' => 'done']);
    }

    /**
     * Handle a job failure.
     */
    public function failed(?Throwable $exception): void
    {
        Storage::disk('public')->deleteDirectory($this->video->path);
        $this->video->watchable->update(['status' => 'failed (MovingToS3)']);
    }
}
