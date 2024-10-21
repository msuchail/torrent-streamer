<?php

namespace App\Jobs;

use App\Models\Movie;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Transmission\Model\Torrent;
use TransmissionPHP\Facades\Transmission;

class FollowUpload implements ShouldQueue
{
    use Queueable;


    /**
     * Determine number of times the job may be attempted.
     */
    public function tries(): int
    {
        return 5;
    }
    /**
     * Determine the time at which the job should timeout.
     */
    public function retryUntil(): \Illuminate\Support\Carbon
    {
        return now()->addMinutes(60);
    }


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


        $this->movie->update([
            'filename' => $torrent->getName(),
        ]);

        do {
            $this->movie->update(['status', $torrent->getPercentDone()]);
            sleep(5);
        } while (!Storage::disk('public')->exists('download/complete/'.$torrent->getName()));


        $baseFile = Storage::disk('public')->path("download/complete/{$torrent->getName()}");
        $newFile = str_replace('.mkv', '.mp4', $baseFile);


        shell_exec("ffmpeg -i $baseFile  -codec copy $newFile");

        $torrent = Transmission::get($this->movie->torrent_id);
        Transmission::remove($torrent);

        Storage::delete('download/complete/'.$torrent->getName());

        $this->movie->update(['status' => 'done', 'filename' => collect(explode('/',$newFile))->last()]);
    }
}
