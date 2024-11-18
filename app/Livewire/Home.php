<?php

namespace App\Livewire;

use App\Models\Movie;
use App\Traits\LocalesTrait;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class Home extends Component
{
    public function mount()
    {
        $this->redirect(route('movie.index'), navigate: true);
    }
}
