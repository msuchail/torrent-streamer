<div x-data="home" x-on:keyup.escape="closeMovie">
    <x-slot name="title">TorrentStream</x-slot>
    @isset($selectedMovie)
        <div class="gap-12">
            <div class="flex flex-col gap-10">
                <div class="flex items-center justify-between w-full">
                    <div id="movieList">
                        <div wire:loading.delay.longer wire:target="search">
                            Recherche en cours...
                        </div>
                        <div wire:loading.remove.delay.longer wire:target="search" class="grid lg:grid-cols-4 md:grid-cols-2 grid-cols-1 justify-start gap-5 flex-nowrap w-full p-4">
                            @foreach($filteredMovies as $movie)
                                <x-ui.card class="cursor-pointer" :h3="$movie->title" :image="Storage::disk('s3')->temporaryUrl($movie->image, now()->addMinutes())" wire:click="seeDetails({{$movie->id}})"></x-ui.card>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <div class="fixed w-full bottom-0 flex justify-center left-0 py-5 bg-slate-950">
        <div class="container mx-auto ">
            <x-filament::input wire:model.live="search" type="search" placeholder="Rechercher un film..." class="h-10 rounded-full bg-slate-800" />
        </div>

    </div>
    @if($modal)
        <div>
            <div class="fixed top-0 left-0 bg-black/80 h-screen w-full flex justify-center items-center" wire:click="closeModal">
                <div class="p-5 bg-slate-950 ring-1 ring-indigo-600 2xl:rounded-2xl h-screen 2xl:h-fit 2xl:w-2/3">
                    <div class="flex justify-between items-center">
                        <h2>{{ $selectedMovie->title }}</h2>
                        <div class="relative">
                            <x-heroicon-c-x-mark class="w-10 cursor-pointer absolute right-0 -top-10" wire:click="closeModal"/>
                        </div>
                    </div>
                    <div class="flex md:flex-row flex-col items-stretch gap-5">
                        <img src="{{ Storage::disk('s3')->temporaryUrl($selectedMovie->image, now()->addMinutes()) }}" alt="{{ $selectedMovie->title }}" class="w-full md:w-1/3 rounded-2xl aspect-video">
                        <div class="md:w-2/3 self-stretch flex flex-col justify-between">
                            <p>{{ $selectedMovie->description }}</p>
                            <div class="flex gap-5 justify-end">
                                <a href="{{ route('movie.show', $selectedMovie->id) }}" wire:navigate class="">
                                    <x-filament::button class="hidden 2xl:block bg-indigo-800 hover:bg-indigo-600 rounded-xl">Regarder</x-filament::button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="{{ route('movie.show', $selectedMovie->id) }}" wire:navigate class="2xl:hidden fixed bottom-10 w-full flex justify-center">
                    <x-filament::button class="bg-indigo-800 hover:bg-indigo-600 md:w-fit px-12 w-2/3 rounded-xl">Regarder</x-filament::button>
                </a>
            </div>
        </div>
    @endif

    @else
        <x-ui.alert-warning title="Aucun film disponible actuellement">
            Il n'y a aucun film disponible actuellement, veuillez réessayer plus tard.
        </x-ui.alert-warning>
    @endisset
</div>
@script
<script>
    Alpine.data('home', () => ({
    }));
</script>
@endscript
