<?php

namespace App\Livewire\Movie;

use App\Models\Movie;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    public Collection $movies;
    public ?Movie $selectedMovie;
    public Collection $filteredMovies;
    public string $search = '';
    public bool $modal = false;

    public function render()
    {
        return view(
            'livewire.movie.index',
            [
                'paginatedMovies' =>  $this->filteredMovies->isNotEmpty() ? $this->filteredMovies->toQuery()?->paginate(8) : collect()
            ]
        );
    }

    public function mount()
    {
        $movies = Movie::active()->get()->filter(function ($movie) {
            return Auth::user()->can('view', $movie);
        });
        $this->fill([
            'movies' => $movies,
            'filteredMovies' => $movies,
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
    public function updatedSearch()
    {
        $this->resetPage();
        $this->filteredMovies = $this->movies->filter(function ($movie) {
            return str_contains(strtolower($movie->title), strtolower($this->search));
        });
    }
}
