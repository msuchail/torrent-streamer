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
        $movie->video()->create();
        $movie->update(['environment' => config('app.env')]);

        FollowUpload::dispatch($movie);
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
            $torrent = Transmission::get($movie->torrent_id ?? 0);
            $incompletePath = 'downloads/incomplete/'.$torrent->getName();
            Storage::disk('public')->deleteDirectory($incompletePath);
            Storage::disk('public')->delete($incompletePath);
            Transmission::remove($torrent);
        } catch (\Exception $e) {
            //
        }

        Storage::delete($movie->torrent);
        Storage::disk('public')->deleteDirectory($movie->video->path);
        Storage::disk('s3')->deleteDirectory($movie->video->path);
        $movie->video->delete();
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
