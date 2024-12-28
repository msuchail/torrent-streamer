<div x-data="home">
    <x-slot name="title">TorrentStream</x-slot>
    @isset($selectedSerie)
        <div class="gap-12">
            <div class="flex flex-col gap-10">
                <div class="flex items-center justify-between w-full">
                    <div id="serieList">
                        <div wire:loading.delay.longer wire:target="search">
                            Recherche en cours...
                        </div>
                        <div wire:loading.remove.delay.longer wire:target="search" class="grid lg:grid-cols-4 md:grid-cols-2 grid-cols-1 justify-start gap-5 flex-nowrap w-full p-4">
                            @foreach($paginatedSeries as $serie)
                                <x-ui.card :mascable="true" class="cursor-pointer" :h3="$serie->title" :image="Storage::disk('s3')->temporaryUrl($serie->image, now()->addMinutes())" wire:click="seeDetails({{$serie->id}})"></x-ui.card>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="fixed w-full bottom-0 flex justify-center left-0 py-5 bg-slate-950">
            <div class="container mx-auto flex flex-col gap-5">
                <x-filament::input wire:model.live="search" type="search" placeholder="Rechercher un film..." class="h-10 rounded-full bg-slate-800" />
                @if($paginatedSeries->isNotEmpty())
                    {{ $paginatedSeries->links("vendor.livewire.tailwind") }}
                @endif
            </div>
        </div>
        @if($modal)
            <div>
                <div class="fixed top-0 left-0 bg-black/80 h-screen w-full flex justify-center items-center" wire:click.self="closeModal">
                    <div class="p-5 bg-slate-950 ring-1 ring-indigo-600 2xl:rounded-2xl h-screen 2xl:h-fit 2xl:w-2/3">
                        <div class="flex justify-between items-center">
                            <h2>{{ $selectedSerie->title }}</h2>
                            <div class="relative">
                                <x-heroicon-c-x-mark class="w-10 cursor-pointer absolute right-0 -top-10" wire:click="closeModal"/>
                            </div>
                        </div>
                        <div class="flex md:flex-row flex-col items-stretch gap-5">
                            <img src="{{ Storage::disk('s3')->temporaryUrl($selectedSerie->image, now()->addMinutes()) }}" alt="{{ $selectedSerie->title }}" class="w-full md:w-1/3 rounded-2xl object-cover">
                            <div class="md:w-2/3 self-stretch flex flex-col justify-between">
                                <div class="flex flex-col items-start">
                                    <h3 class="mt-5 sm:mt-0">Description de la série</h3>
                                    <p>{{ $selectedSerie->description }}</p>
                                    <div class="flex items-center gap-3 mt-5 md:flex-nowrap flex-wrap sm:mb-0 mb-3">
                                        @isset($selectedSeason)
                                            <h3 class="sm:block">Saisons disponibles</h3>
                                            <x-filament::tabs class="!bg-transparent mx-0 ring-slate-800 h-7">
                                                @foreach($selectedSerie->seasons->where("status", "done") as $key=>$season)
                                                    <x-filament::tabs.item wire:key="{{ $key }}" class="!bg-transparent" active="{{ $selectedSeason->id === $season->id }}" wire:click="setSelectedSeason('{{ $season->id }}')">
                                                        Saison {{ $season->order }}
                                                    </x-filament::tabs.item>
                                                @endforeach
                                            </x-filament::tabs>
                                        @endisset
                                    </div>
                                    @isset($selectedSeason)
                                    <div class="grid sm:grid-cols-12 gap-5 mt-2">
                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk("s3")->temporaryUrl($selectedSeason->image, now()->addMinutes()) }}" alt="" class="sm:col-span-4">
                                        <p class="sm:col-span-8"><span class="font-semibold"></span>{{ $selectedSeason->description }}</p>
                                    </div>
                                    @else
                                        <div class="w-full">
                                            <x-ui.alert-warning >Aucune saison disponible</x-ui.alert-warning>
                                        </div>
                                    @endisset
                                </div>
                                @isset($selectedSeason)
                                    <div class="flex gap-5 justify-end">
                                        <a href="{{ route('serie.show', [$selectedSerie->id, 'seasonId' => $selectedSeason->id, 'episodeId' => $selectedSeason->episodes->map(fn ($episode) => $episode->video->watching)->where('segment', '>', 3)?->last()?->video->watchable->id ?? $selectedSeason->episodes->first()->id]) }}" wire:navigate class="">
                                            <x-filament::button class="hidden 2xl:block bg-indigo-800 hover:bg-indigo-600 rounded-xl">Regarder</x-filament::button>
                                        </a>
                                    </div>
                                @endisset
                            </div>
                        </div>
                    </div>
                    @isset($selectedSeason)
                        <a href="{{ route('serie.show', [$selectedSerie->id, 'seasonId' => $selectedSeason->id, 'episodeId' => $selectedSeason->episodes->map(fn ($episode) => $episode->video->watching)->where('segment', '>', 3)?->last()?->video->watchable->id ?? $selectedSeason->episodes->first()->id]) }}" wire:navigate class="2xl:hidden fixed bottom-10 w-full flex justify-center">
                            <x-filament::button class="bg-indigo-800 hover:bg-indigo-600 md:w-fit px-12 w-2/3 rounded-xl">Regarder</x-filament::button>
                        </a>
                    @endisset
                </div>
            </div>
        @endif

    @else
        <x-ui.alert-warning title="Aucune série disponible actuellement">
            Il n'y a aucune série disponible actuellement, veuillez réessayer plus tard.
        </x-ui.alert-warning>
    @endisset
</div>
