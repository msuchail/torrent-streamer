<?php

namespace App\Console\Commands;

use App\Models\Movie;
use App\Models\Season;
use App\Models\Serie;
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
        Serie::all()->each(function (Serie $serie) {
            $serie->seasons()->each(function (Season $season, $key) {
                $season->update(['order' => $key]);
            });
        });
    }
}
