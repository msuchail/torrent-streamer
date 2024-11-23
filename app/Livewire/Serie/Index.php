<?php

namespace App\Livewire\Serie;

use App\Models\Season;
use App\Models\Serie;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public Collection $series;
    public ?Serie $selectedSerie;
    public Collection $filteredSeries;
    public string $search = '';
    public bool $modal = false;
    public ?Season $selectedSeason;


    public function render()
    {
        return view('livewire.serie.index');
    }

    public function mount()
    {
        $series = Serie::active()->get()->filter(function ($serie) {
            return Auth::user()->can('view', $serie);
        });
        $this->fill([
            'series' => $series,
            'filteredSeries' => $series,
            'selectedSerie' => $series->first(),
        ]);
    }
    public function seeDetails(Serie $serie)
    {
        $this->selectedSerie = $serie;
        $this->selectedSeason = $serie->seasons->firstWhere("status", "done");
        $this->modal = true;
    }
    public function closeModal()
    {
        $this->modal = false;
    }
    public function updatedSearch()
    {
        $this->filteredSeries = $this->series->filter(function ($serie) {
            return str_contains(strtolower($serie->title), strtolower($this->search));
        });
    }
    public function setSelectedSeason(Season $season)
    {
        $this->selectedSeason = $season;
    }
}