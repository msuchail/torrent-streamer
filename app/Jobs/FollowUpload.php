<?php

namespace App\Jobs;

use App\Models\Movie;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use TransmissionPHP\Facades\Transmission;

class FollowUpload implements ShouldQueue
{
    use Queueable;


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
        $torrent = Transmission::get($this->movie->torrent_id);
        $path = "downloads/complete/{$this->movie->title}/";

        do {
            sleep(5);
        } while (!Storage::disk('public')->exists("downloads/complete/{$this->movie->title}"));

        Transmission::remove($torrent);
        Storage::delete($this->movie->torrent);



        $allFiles = collect(Storage::disk('public')->files(directory: $path, recursive: true));

        $isFirst = true;
        $file = $allFiles->filter(function($file) use (&$isFirst, &$keep) {
            if (!$isFirst) {
                Storage::disk('public')->delete($file);
                return false;
            }

            $ext = collect(explode('.', $file))->last();
            if(in_array($ext, ['mkv', 'avi', 'mov'])) {
                $isFirst = false;
                $newFile = explode('.', $file);
                array_pop($newFile);
                $newFile = implode('.', $newFile). '.mp4';
                shell_exec("ffmpeg -i 'storage/app/public/$file'  -codec copy 'storage/app/public/$newFile' -y");
                $this->movie->update(['status' => 'done', 'filename' => $newFile]);
                Storage::disk('public')->delete($file);
                return true;
            } elseif ($ext === 'mp4') {
                $isFirst = false;
                return true;
            } else {
                Storage::disk('public')->delete($file);
                return false;
            }
        })->first();

    }
}
