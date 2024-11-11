<?php

namespace App\Jobs;

use App\Models\Movie;
use App\Models\Season;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use TransmissionPHP\Facades\Transmission;
use Throwable;

class DownloadTorrent implements ShouldQueue
{
    use Queueable;


    public $tries = 5;
    public $timeout = 36000;
    public  $backoff = 10;
    public string $temporaryPath;


    public function __construct(public Movie|Season $downlodable)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->temporaryPath = "/downloads/complete/".rand();
        $torrent = Transmission::add(torrent: '/'.$this->downlodable->torrent, savepath: "$this->temporaryPath");

        $this->downlodable->update([
            'torrent_id' => $torrent->getId(),
        ]);


        do {
            $torrent = Transmission::get($this->downlodable->torrent_id);
            $this->downlodable->update(['status' => 'downloading '. ($torrent->getPercentDone()) . '%']);
            sleep(2);
        } while (!Storage::disk('public')->exists($this->temporaryPath));

        Transmission::remove($torrent);
        Storage::delete($this->downlodable->torrent);

        $this->downlodable->update(['status' => 'downloaded']);


        if($this->downlodable instanceof Season) {
            ManageSeasonFiles::dispatchSync($this->downlodable, $this->temporaryPath);
        } elseif ($this->downlodable instanceof Movie) {
            ManageMovieFiles::dispatchSync($this->downlodable, $this->temporaryPath);
        }
    }


    /**
     * Handle a job failure.
     */
    public function failed(?Throwable $exception): void
    {
        $this->downlodable->update(['status' => 'failed to download']);

        Storage::disk('public')->deleteDirectory($this->temporaryPath);
        Storage::disk('public')->delete( str_replace('complete', 'incomplete', $this->temporaryPath));
        Transmission::remove($this->downlodable->torrent_id);
        Storage::delete($this->downlodable->torrent);
    }
}
