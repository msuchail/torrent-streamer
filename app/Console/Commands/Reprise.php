<?php

namespace App\Console\Commands;

use App\Models\Movie;
use App\Models\Video;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class Reprise extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reprise';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Video::all()->each(function ($video) {
            $video->update([
                'segments_number' => count(Storage::disk('s3')->files($video->path.'/video'))
            ]);
        });
    }
}
