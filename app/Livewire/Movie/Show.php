<?php

namespace App\Livewire\Movie;

use App\Models\Movie;
use App\Traits\LocalesTrait;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class Show extends Component
{
    use LocalesTrait;

    public Movie $movie;
    public array $subtitles = [];


    public function render()
    {
        return view('livewire.movie.show');
    }
    public function mount()
    {
        $this->subtitles = collect(Storage::disk('s3')->files($this->movie->storagePath.'/srt', true))
            ->map(function($file, $key) {
                $fileName = collect(explode('/', $file))->last();
                $lang = explode('.', $fileName)[0];
                $forced = explode('.', $fileName)[2] === 'forced';
                $name = $this->matchingLanguage($lang, $key) . ($forced ? ' (Forcé)' : '');
                return [
                    'url' => Storage::disk('s3')->url($file),
                    'forced' => $forced,
                    'name' => $name,
                    'lang' => $fileName
                ];
            })->values()
            ->toArray();
    }

}
