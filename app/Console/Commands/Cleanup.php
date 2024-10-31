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

        Storage::delete('torrents/*');

        collect(Storage::disk('s3')->directories())->each(function($directory) {
            $id = collect(explode('/', $directory))->last();
            if(!Movie::find($id)) {
                Storage::disk('s3')->deleteDirectory($directory);
            }
        });

        $images = collect(Storage::files('images'));
        $images->each(function($image) {
            $movie = Movie::firstWhere('image', $image);
            if (!$movie) {
                Storage::delete($image);
            }
        });
    }
}
