<?php

namespace App\Console\Commands;

use App\Models\Movie;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class Images extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:images';

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
        $images = collect(Storage::disk('s3')->files('downloads/complete/', true))->filter(
            function ($image) {
                $ext = collect(explode('.', $image))->last();
                return in_array($ext, ['jpg', 'jpeg', 'png']);
            }
        );

        $images->each(function ($image) {
            $name = collect(explode('/', $image))->last();
            Storage::disk('s3')->move($image, 'images/' . $name);
            Movie::firstWhere('image', $image)->update(['image' => 'images/' . $name]);
        });
    }
}
