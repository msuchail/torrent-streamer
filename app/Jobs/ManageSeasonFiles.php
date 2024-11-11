<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ManageSeasonFiles implements ShouldQueue
{
    use Queueable;

    public $tries = 5;
    public $timeout = 36000;
    public  $backoff = 10;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
    }
}
