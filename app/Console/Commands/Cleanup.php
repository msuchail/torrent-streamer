<?php

namespace App\Console\Commands;

use App\Models\Movie;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Transmission\Transmission;

class Cleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup';

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
        Storage::disk('public')->deleteDirectory('downloads/incomplete');
        Movie::whereNot('status', 'done')->delete();

        $completed = collect(Storage::disk('public')->directories('downloads/complete'));

        $completed->each(function($dir) {
            $movie = Movie::firstWhere('title', collect(explode('/', $dir))->last());
            if (!$movie) {
                Storage::disk('public')->deleteDirectory($dir);
            }
        });

        Storage::delete('torrents/*');

        $images = collect(Storage::files('images'));
        $images->each(function($image) {
            $movie = Movie::firstWhere('image', $image);
            if (!$movie) {
                Storage::delete($image);
            }
        });
    }
}
