<?php

namespace App\Jobs;

use App\Traits\VideoTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use TransmissionPHP\Facades\Transmission;
use Throwable;
use function Laravel\Prompts\error;

class FollowUpload implements ShouldQueue
{
    use Queueable;
    use VideoTrait;

    public $tries = 5;
    public $timeout = 36000;
    public  $backoff = 10;


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //On dplace l'image dans le S3
        $fileName = collect(explode('/', $this->movie->image))->last();
        Storage::disk('s3')->put($this->movie->storagePath.'/'.$fileName, Storage::get(Storage::get($this->movie->image)));
        Storage::delete($this->movie->image);
        $this->movie->update->image($this->movie->storagePath.'/'.$fileName);

        try {
            do {
                $torrent = Transmission::get($this->movie->torrent_id);
                $this->movie->update(['status' => 'downloading '. ($torrent->getPercentDone()) . '%']);
                sleep(2);
            } while (!Storage::disk('public')->exists($this->storagePath));
            Transmission::remove($torrent);
            Storage::delete($this->movie->torrent);
        } catch (\Exception $e) {
            return;
        }
        $this->movie->update(['status' => 'downloaded']);
        $allFiles = collect(Storage::disk('public')->files(directory: $this->storagePath, recursive: true));

        $isFirst = true;
        $file = $allFiles->filter(function($file) use (&$isFirst, &$keep) {
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
        $file = collect(Storage::disk('public')->files(directory: $this->storagePath, recursive: false))->first();
        $this->baseFile = $this->path. '/' . collect(explode('/', $file))->last();

        collect(Storage::disk('public')->directories($this->storagePath))->each(function($dir) {
            $shortDir = collect(explode('/', $dir))->last();

            if(!in_array($shortDir, ['video', 'audio', 'srt'])) {
                Storage::disk('public')->deleteDirectory($dir);
            }
        });
        $this->movie->update(['status' => 'converting']);
        $this->convertAll();

        $this->movie->update(['status' => 'moving to S3']);

        //On supprime input.mkv
        Storage::disk('public')->delete($this->movie->storagePath.'/input.mkv');

        //On déplace tout  dans le S3
        $allFiles = collect(Storage::disk('public')->files(directory: $this->storagePath, recursive: true))->each(function($file) {
            Storage::disk('s3')->put($file, Storage::disk('public')->get($file));
        });

        //On supprime tout dans le public
        Storage::disk('public')->deleteDirectory($this->storagePath);

        $this->movie->update(['status' => 'done']);
    }


    /**
     * Handle a job failure.
     */
    public function failed(?Throwable $exception): void
    {
        Log::error('Job failed : {error}', ['error' => $exception->getMessage()]);
    }
}
