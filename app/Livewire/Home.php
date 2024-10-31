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
    public Collection $movies;
    public Movie $selectedMovie;
    public bool $modal = false;

    public function render()
    {
        return view('livewire.home');
    }

    public function mount()
    {
        $movies = Movie::active()->get();
        $this->fill([
            'movies' => $movies,
            'selectedMovie' => $movies->first(),
        ]);
    }
    public function seeDetails(Movie $movie)
    {
        $this->selectedMovie = $movie;
        $this->modal = true;
    }
    public function closeModal()
    {
        $this->modal = false;
    }
}
