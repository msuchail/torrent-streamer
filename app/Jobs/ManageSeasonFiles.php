<?php

namespace App\Jobs;

use App\Models\Season;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class ManageSeasonFiles implements ShouldQueue
{
    use Queueable;

    public $tries = 5;
    public $timeout = 36000;
    public  $backoff = 10;

    /**
     * Create a new job instance.
     */
    public function __construct(public Season $season, public string $temporaryPath)
    {
        $allFiles = collect(Storage::disk('public')->files(directory: $this->temporaryPath, recursive: true));
        $prefixs = collect(["e", "e0", "ep", "ep0", "ep.", "ep.0", "episode ", "episode 0", "episode.", "episode.0"]);


        $allFiles->filter(function ($file) use ($prefixs) {
            $ext = collect(explode('.', $file))->last();
            return in_array($ext, ["mp4", "mkv"]);
        })->values()->each(function ($file, $key) use ($prefixs) {
            $prefixs->each(function ($prefix) use (&$found, $key, $file) {
                if (!$found && str_contains($file, $prefix . $key+1)) {
                    $episode = $this->season->episodes()->create([
                        "title" => "Episode " . $key+1,
                    ]);

                    $video = $episode->video()->create();

                    Storage::disk('public')->makeDirectory($video->path);

                    Storage::disk('public')->move($file, $video->path.'/input.'.collect(explode('.', $file))->last());

                    ConvertVideo::dispatchSync($video);
                    return true;
                }
                return false;
            });
        });
//        Storage::delete($this->temporaryPath);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->season->update(['status' => 'failed to manage files']);
        Storage::disk('public')->deleteDirectory($this->temporaryPath);
        if(isset($this->movie->video)) {
            $this->movie->video->delete();
        }
    }
}
