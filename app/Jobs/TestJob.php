<?php

namespace App\Jobs;

use App\Models\Movie;
use App\Traits\LocalesTrait;
use App\Traits\VideoTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TestJob implements ShouldQueue
{
    use LocalesTrait;
    use VideoTrait;
    use Queueable;

    CONST SEGMENT_DURATION = 60;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

    }
}
