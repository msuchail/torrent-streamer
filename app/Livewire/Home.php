<?php

namespace App\Livewire;

use App\Models\Movie;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class Home extends Component
{
    public Collection $movies;
    public Movie|null $selectedMovie;
    public string $videoUrl;

    public function render()
    {
        return view('livewire.home');
    }

    public function mount()
    {
        $this->movies = Movie::active()->get();
        $this->selectedMovie = $this->selectedMovie ?? $this->movies->first();
        $this->setVideoUrl();
    }

    public function setMovie(Movie $movie)
    {
        $this->selectedMovie = $movie;
        $this->setVideoUrl();
    }

    private function setVideoUrl()
    {
        $filename = collect(explode('/', $this->selectedMovie->filename))->last();
        $this->videoUrl = route('videostream', ['filename' => $filename, 'moviename' => $this->selectedMovie->title]);
    }


}
