<?php

namespace App\Jobs;

use App\Traits\VideoTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class ConvertVideo implements ShouldQueue
{
    use Queueable;
    use VideoTrait;
    public $tries = 5;
    public $timeout = 36000;
    public  $backoff = 10;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //On convertit le fichier
        $this->video->watchable->update(['status' => 'converting']);
        $this->convertAll();
        $this->video->update(['segments_number' => count(Storage::disk('public')->files($this->video->path.'/video'))]);

        //On déplace le fichier dans le S3
        MovingToS3::dispatchSync($this->video);
    }

    public function failed()
    {
        $this->video->delete();
    }
}
