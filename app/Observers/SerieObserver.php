<?php

namespace App\Observers;

use App\Models\Serie;
use Illuminate\Support\Facades\Storage;

class SerieObserver
{
    /**
     * Handle the Serie "created" event.
     */
    public function created(Serie $serie): void
    {
        $serie->update(['environment' => config('app.env')]);
    }

    /**
     * Handle the Serie "updated" event.
     */
    public function updated(Serie $serie): void
    {
        //
    }

    /**
     * Handle the Serie "deleted" event.
     */
    public function deleted(Serie $serie): void
    {
        Storage::disk('s3')->delete($serie->image);
    }

    public function deleting(Serie $serie): void
    {
        $serie->seasons->each(fn($season) => $season->delete());
    }

    /**
     * Handle the Serie "restored" event.
     */
    public function restored(Serie $serie): void
    {
        //
    }

    /**
     * Handle the Serie "force deleted" event.
     */
    public function forceDeleted(Serie $serie): void
    {
        //
    }
}
