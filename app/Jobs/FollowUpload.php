<?php

namespace App\Jobs;

use App\Traits\VideoTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use TransmissionPHP\Facades\Transmission;

class FollowUpload implements ShouldQueue
{
    use Queueable;
    use VideoTrait;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $torrent = Transmission::get($this->movie->torrent_id);
            do {
                $this->movie->update(['status' => 'downloading '. ($torrent->getPercentDone()) . '%']);
                sleep(2);
            } while (!Storage::disk('public')->exists($this->storagePath));
            Transmission::remove($torrent);
            Storage::delete($this->movie->torrent);
        } catch (\Exception $e) {
            return;
        }

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

        Storage::disk('public')->move($file, $this->movie->storagePath.'/'.collect(explode('/', $file))->last());
        $file = collect(Storage::disk('public')->files(directory: $this->storagePath, recursive: false))->first();
        $this->baseFile = $this->path. '/' . collect(explode('/', $file))->last();

        collect(Storage::disk('public')->directories($this->storagePath))->each(function($dir) {
            $shortDir = collect(explode('/', $dir))->last();

            if(!in_array($shortDir, ['video', 'audio', 'srt'])) {
                Storage::disk('public')->deleteDirectory($dir);
            }
        });
        $this->convertAll();
        Storage::disk('public')->delete($file);
        $this->movie->update(['status' => 'done']);
    }
}
