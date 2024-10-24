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
        $torrent = Transmission::add(torrent: '/'.$movie->torrent, savepath: '/downloads/complete/'.$movie->title );

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
        try {
            $torrent = Transmission::get($movie->torrent_id ?? 0);
            Transmission::remove($torrent);
        } catch (\Exception $e) {
            //
        }

        Storage::delete($movie->torrent);
        Storage::disk('public')->deleteDirectory('downloads/complete/'.$movie->title);
        Storage::disk('local')->delete($movie->image);
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
