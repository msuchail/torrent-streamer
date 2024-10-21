<?php

namespace App\Livewire;

use App\Models\Movie;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Url;
use Livewire\Component;

class Home extends Component
{
    public Collection $movies;
    public Movie|null $selectedMovie;
    public bool $showModal = false;

    public function render()
    {
        return view('livewire.home');
    }

    public function mount()
    {
        $this->movies = Movie::active()->get();
        $this->selectedMovie = $this->selectedMovie ?? $this->movies->first();
    }

    public function setMovie(Movie $movie)
    {
        $this->selectedMovie = $movie;
    }

    public function watchMovie()
    {
        $this->showModal = true;
    }
    public function closeMovie() {
        $this->showModal = false;
    }
}
