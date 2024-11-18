<?php

namespace App\Observers;

use App\Jobs\DownloadTorrent;
use App\Models\Season;
use Illuminate\Support\Facades\Storage;
use TransmissionPHP\Facades\Transmission;

class SeasonObserver
{
    /**
     * Handle the Season "created" event.
     */
    public function created(Season $season): void
    {
        $season->update(['environment' => config('app.env')]);
        DownloadTorrent::dispatch($season);
    }

    /**
     * Handle the Season "updated" event.
     */
    public function updated(Season $season): void
    {
        if($season->isDirty('image')) {
            $lastImage = $season->getOriginal('image');
            if(isset($lastImage)) {
                Storage::disk('s3')->delete($lastImage);
                Storage::delete($lastImage);
            }
        }
    }


    public function deleting(Season $season): void
    {
        $season->episodes()->each(fn($episode) => $episode->delete());
    }

    /**
     * Handle the Season "deleted" event.
     */
    public function deleted(Season $season): void
    {
        try {

            $torrent = Transmission::get($season->torrent_id);

            Transmission::remove($torrent, true);
        } catch (\Exception $e) {
            // do nothing
        }


        if(isset($season->video))
        {
            $season->video->delete();
        }
        Storage::disk('s3')->delete($season->image);
    }

    /**
     * Handle the Season "restored" event.
     */
    public function restored(Season $season): void
    {
        //
    }

    /**
     * Handle the Season "force deleted" event.
     */
    public function forceDeleted(Season $season): void
    {
        //
    }
}
