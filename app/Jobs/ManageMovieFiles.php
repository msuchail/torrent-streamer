<?php

namespace App\Jobs;

use App\Models\Movie;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;
use TransmissionPHP\Facades\Transmission;

class ManageMovieFiles implements ShouldQueue
{
    use Queueable;
    public $tries = 5;
    public $timeout = 36000;
    public  $backoff = 10;

    /**
     * Create a new job instance.
     */
    public function __construct(public Movie $movie, public string $temporaryPath)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // On tri les fichiers pour garder le premier fichier vidéo
        $allFiles = collect(Storage::disk('public')->files(directory: $this->temporaryPath, recursive: true));
        $isFirst = true;

        $file = $allFiles->filter(function($file) use (&$isFirst) {
            if (!$isFirst) {
                Storage::disk('public')->delete($file);
                return false;
            }
            $ext = collect(explode('.', $file))->last();
            if(in_array($ext, ['mkv', 'avi', 'mov', 'mp4'])) {
                $isFirst = false;
                return true;
            } else {
                Storage::disk('public')->delete($file);
                return false;
            }
        })->first();

        Storage::disk('public')->move($file, $this->temporaryPath.'/input.'.collect(explode('.', $file))->last());

        collect(Storage::disk('public')->directories($this->temporaryPath))->each(function($dir) {
            $shortDir = collect(explode('/', $dir))->last();

            if(!in_array($shortDir, ['video', 'audio', 'srt'])) {
                Storage::disk('public')->deleteDirectory($dir);
            }
        });

        $this->movie->video()->create();


        Storage::disk('public')->makeDirectory($this->movie->video->path);
        collect(Storage::disk('public')->files($this->temporaryPath))->each(function($file) {
            Storage::disk('public')->move($file, $this->movie->video->path. '/' .collect(explode('/', $file))->last());
        });
        Storage::disk('public')->deleteDirectory($this->temporaryPath);

        ConvertVideo::dispatchSync($this->movie->video);
    }

    public function failed()
    {
        $this->movie->update(['status' => 'failed to manage files']);
        Storage::disk('public')->deleteDirectory($this->temporaryPath);
        if(isset($this->movie->video)) {
            $this->movie->video->delete();
        }
    }
}
