<?php

namespace App\Jobs;

use App\Traits\VideoTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use TransmissionPHP\Facades\Transmission;
use Throwable;

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
        Storage::disk('public')->delete($file);
        $this->movie->update(['status' => 'done']);
    }


    /**
     * Handle a job failure.
     */
    public function failed(?Throwable $exception): void
    {
        $this->movie->update(['status' => 'failed']);
    }
}
