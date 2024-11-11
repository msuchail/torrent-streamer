<?php

namespace App\Observers;

use App\Jobs\DownloadTorrent;
use App\Models\Season;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Handle the Season "deleted" event.
     */
    public function deleted(Season $season): void
    {
        Storage::disk('s3')->delete($season->image);
        $season->episodes->each(fn ($episode) => $episode->delete());
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
