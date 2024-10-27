<?php

namespace App\Livewire;

use App\Models\Movie;
use App\Traits\LocalesTrait;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class Home extends Component
{
    use LocalesTrait;

    public Collection $movies;
    public Movie|null $selectedMovie;
    public string $videoUrl;
    public array $subtitles;

    public function render()
    {
        return view('livewire.home');
    }

    public function mount()
    {
        $this->fill([
            'movies' => Movie::active()->get(),
        ]);
        $this->setMovie($this->movies->first());

    }

    public function setMovie(Movie $movie)
    {

        $this->fill([
            'selectedMovie' => $movie,
            'videoUrl' => $movie->videoUrl,
            'subtitles' => collect(Storage::disk('public')->files($movie->storagePath.'/srt', true))
                ->map(function($file, $key) {
                    $fileName = collect(explode('/', $file))->last();
                    $lang = explode('.', $fileName)[0];
                    $forced = explode('.', $fileName)[2] === 'forced';
                    $name = $this->matchingLanguage($lang, $key) . ($forced ? ' (Forcé)' : '');
                    return [
                        'url' => Storage::disk('public')->url($file),
                        'forced' => $forced,
                        'name' => $name,
                        'lang' => $fileName
                    ];
                })->values()
                ->toArray(),
        ]);


    }



}
