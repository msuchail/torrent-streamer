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
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $allFiles = collect(Storage::disk('public')->files(directory: $this->temporaryPath, recursive: true));
        $this->season->update(["status" => "converting"]);
        $this->season->save();

        $episodes = $allFiles->filter(function ($file) {
            $ext = collect(explode('.', $file))->last();
            return in_array($ext, ["mp4", "mkv"]);
        })->sortBy(fn($file) => $file);

        $episodes->each(function ($file, $key) {
            $episode = $this->season->episodes()->create([
                "title" => "Episode " . $key+1,
            ]);
            $video = $episode->video()->create();
            Storage::disk('public')->makeDirectory($video->path);
            Storage::disk('public')->move($file, $video->path.'/input.'.collect(explode('.', $file))->last());
            ConvertVideo::dispatchSync($video);
        });
//        Storage::delete($this->temporaryPath);
        $this->season->update(["status" => "done"]);
    }


    public function failed(\Exception $exception): void
    {
        $this->season->update(['status' => 'failed to manage files']);
        Storage::disk('public')->deleteDirectory($this->temporaryPath);
        if(isset($this->movie->video)) {
            $this->movie->video->delete();
        }
    }
}
