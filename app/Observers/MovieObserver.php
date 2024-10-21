<?php

namespace App\Observers;

use App\Jobs\FollowUpload;
use App\Models\Movie;
use Illuminate\Support\Facades\Storage;
use TransmissionPHP\Facades\Transmission;


class MovieObserver
{
    /**
     * Handle the Movie "created" event.
     */
    public function created(Movie $movie): void
    {
        $torrent = Transmission::add('/torrents/'.$movie->torrent);

        $movie->update([
            'torrent_id' => $torrent->getId(),
        ]);

        FollowUpload::dispatch($movie);
    }

    /**
     * Handle the Movie "updated" event.
     */
    public function updated(Movie $movie): void
    {
    }

    /**
     * Handle the Movie "deleted" event.
     */
    public function deleted(Movie $movie): void
    {
        Storage::delete($movie->torrent);
        Storage::disk('public')->delete('download/complete/'.$movie->filename);
        Storage::disk('public')->delete('download/incomplete/'.$movie->filename);
//        Transmission::get($movie->hash)->remove();
    }

    /**
     * Handle the Movie "restored" event.
     */
    public function restored(Movie $movie): void
    {
        //
    }

    /**
     * Handle the Movie "force deleted" event.
     */
    public function forceDeleted(Movie $movie): void
    {
        //
    }
}
