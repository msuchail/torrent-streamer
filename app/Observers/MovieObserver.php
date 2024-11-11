<?php

namespace App\Observers;

use App\Jobs\DownloadTorrent;
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
        $movie->update(['environment' => config('app.env')]);
        DownloadTorrent::dispatch($movie);
    }

    /**
     * Handle the Movie "updated" event.
     */
    public function updated(Movie $movie): void
    {
        if($movie->isDirty('image')) {
            $lastImage = $movie->getOriginal('image');
            if(isset($lastImage)) {
                Storage::disk('s3')->delete($lastImage);
                Storage::delete($lastImage);
            }
        }
    }

    /**
     * Handle the Movie "deleted" event.
     */
    public function deleted(Movie $movie): void
    {
        try {

            $torrent = Transmission::get($movie->torrent_id);

            Transmission::remove($torrent, true);
        } catch (\Exception $e) {
            // do nothing
        }


        if(isset($movie->video))
        {
            $movie->video->delete();
        }
        Storage::disk('s3')->delete($movie->image);
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
