<?php

namespace App\Observers;

use App\Jobs\ConvertVideo;
use App\Models\Video;
use Illuminate\Support\Facades\Storage;
use TransmissionPHP\Facades\Transmission;

class VideoObserver
{
    /**
     * Handle the Video "created" event.
     */
    public function created(Video $video): void
    {
    }

    /**
     * Handle the Video "updated" event.
     */
    public function updated(Video $video): void
    {
        //
    }

    /**
     * Handle the Video "deleted" event.
     */
    public function deleted(Video $video): void
    {
        Storage::disk('public')->deleteDirectory($video->path);
        Storage::disk('s3')->deleteDirectory($video->path);
    }

    /**
     * Handle the Video "restored" event.
     */
    public function restored(Video $video): void
    {
        //
    }

    /**
     * Handle the Video "force deleted" event.
     */
    public function forceDeleted(Video $video): void
    {
        //
    }
}
