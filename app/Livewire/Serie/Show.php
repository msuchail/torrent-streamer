<?php

namespace App\Livewire\Serie;

use App\Models\Episode;
use App\Models\Season;
use App\Models\Serie;
use App\Traits\LocalesTrait;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Url;
use Livewire\Component;

class Show extends Component
{
    use LocalesTrait;

    public Serie $serie;

    #[Url]
    public int $seasonId;

    public Season $season;

    #[Url]
    public int $episodeId;
    public Episode $episode;

    public array $subtitles = [];
    public string $videoUrl = '';

    public int $initialSegment;


    public function render()
    {
        return view('livewire.serie.show');
    }
    public function mount()
    {
        $this->season = Season::find($this->seasonId);
        $this->episode = Episode::find($this->episodeId);

        $this->initialSegment = auth()->user()->watching()->where('video_id', $this->episode->video->id)->first()?->segment ?? 0;

        $this->setVideoUrl();

        $this->subtitles = collect(Storage::disk('s3')->files($this->episode->video->path.'/srt', true))
            ->map(function($file, $key) {
                $fileName = collect(explode('/', $file))->last();
                $lang = explode('.', $fileName)[0];
                $forced = explode('.', $fileName)[2] === 'forced';
                $name = $this->matchingLanguage($lang, $key) . ($forced ? ' (Forcé)' : '');

                return [
                    'url' => route('video.subtitle', [$this->episode->video->id, $fileName]),
                    'forced' => $forced,
                    'name' => $name,
                    'lang' => $fileName
                ];
            })->values()
            ->toArray();
    }

    public function updated($propertyName) {
        if($propertyName == 'seasonId') {
            $this->season = Season::find($this->seasonId);
            $this->episode = $this->season->episodes->first();
            $this->episodeId = $this->episode->id;
            $this->setVideoUrl();
            $this->dispatch("video-changed");
        }

        if($propertyName == 'episodeId') {
            $this->episode = Episode::find($this->episodeId);
            $this->setVideoUrl();
            $this->dispatch("video-changed");
        }
    }

    private function setVideoUrl() : void
    {
        $this->videoUrl = route('video.master', [$this->episode->video->id]);
    }
}
