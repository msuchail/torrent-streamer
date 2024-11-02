<?php

namespace App\Jobs;

use App\Models\Movie;
use App\Traits\VideoTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use TransmissionPHP\Facades\Transmission;
use Throwable;

class FollowUpload implements ShouldQueue
{
    use Queueable;


    public $tries = 5;
    public $timeout = 3600;
    public  $backoff = 10;


    public function __construct(public Movie $movie)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //On déplace l'image dans le S3
        $fileName = collect(explode('/', $this->movie->image))->last();
        Storage::disk('s3')->put($this->movie->storagePath.'/'.$fileName, Storage::get($this->movie->image));
        Storage::delete($this->movie->image);
        $this->movie->update(['image' => $this->movie->storagePath.'/'.$fileName]);

        try {
            do {
                $torrent = Transmission::get($this->movie->torrent_id);
                $this->movie->update(['status' => 'downloading '. ($torrent->getPercentDone()) . '%']);
                sleep(2);
            } while (!Storage::disk('public')->exists($this->movie->storagePath));
            Transmission::remove($torrent);
            Storage::delete($this->movie->torrent);
        } catch (\Exception $e) {
            return;
        }
        $this->movie->update(['status' => 'downloaded']);


        // On tri les fichiers pour garder le premier fichier vidéo
        $allFiles = collect(Storage::disk('public')->files(directory: $this->movie->storagePath, recursive: true));
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
        Storage::disk('public')->move($file, $this->movie->storagePath.'/input.'.collect(explode('.', $file))->last());
        error_log('1');
        $file = collect(Storage::disk('public')->files(directory: $this->movie->storagePath, recursive: false))->first();
        error_log('1.1');
        error_log('2');
        collect(Storage::disk('public')->directories($this->movie->storagePath))->each(function($dir) {
            $shortDir = collect(explode('/', $dir))->last();

            if(!in_array($shortDir, ['video', 'audio', 'srt'])) {
                Storage::disk('public')->deleteDirectory($dir);
            }
        });
        error_log('3');

        // On convertit le fichier
        ConvertVideo::dispatchSync($this->movie);
    }


    /**
     * Handle a job failure.
     */
    public function failed(?Throwable $exception): void
    {
        $this->movie->update(['status' => 'failed (follow upload)']);
    }
}
