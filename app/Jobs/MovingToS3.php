<?php

namespace App\Jobs;

use App\Models\Movie;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class MovingToS3 implements ShouldQueue
{
    use Queueable;
    public $tries = 5;
    public $timeout = 3600 * 2;
    public  $backoff = 10;

    /**
     * Create a new job instance.
     */
    public function __construct(public Movie $movie)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->movie->update(['status' => 'moving to S3']);
        //On supprime input.mkv
        Storage::disk('public')->delete($this->movie->video->path.'/input.mkv');

        //On déplace tout  dans le S3
        $allFiles = collect(Storage::disk('public')->files(directory: $this->movie->video->path, recursive: true))->each(function($file) {
            Storage::disk('s3')->put($file, Storage::disk('public')->get($file));
        });

        //On supprime tout dans le public
        Storage::disk('public')->deleteDirectory($this->movie->video->path);

        $this->movie->update(['status' => 'done']);
    }

    /**
     * Handle a job failure.
     */
    public function failed(?Throwable $exception): void
    {
        $this->movie->update(['status' => 'failed (MovingToS3)']);
    }
}
