<div x-data="home" x-on:keyup.escape="closeMovie">
    <x-slot name="title">TorrentStream</x-slot>
    @isset($selectedMovie)
        <div class="gap-12">
            <div class="col-span-8 p-5 rounded-2xl">
                <h2>Films conseillés</h2>
                <div class="flex items-center justify-between w-full">
                    <x-heroicon-c-arrow-left-circle class="w-10 shrink-0 mx-5 cursor-pointer" @click="scrollLeft"/>
                    <div id="movieList" class="flex justify-start gap-5 flex-nowrap overflow-x-scroll w-full">
                        @foreach($movies as $movie)
                            <x-ui.card class="shrink-0 cursor-pointer" :h3="$movie->title" :image="Storage::disk('s3')->temporaryUrl($movie->storagePath.'/poster.jpg', now()->addMinutes())" wire:click="seeDetails({{$movie->id}})" wire:key="{{ $movie->id }}"></x-ui.card>
                        @endforeach
                    </div>
                    <x-heroicon-c-arrow-right-circle class="w-10 shrink-0 mx-5 cursor-pointer" @click="scrollRight"/>
                </div>
            </div>
        </div>
    @if($modal)
        <div>
            <div class="fixed top-0 left-0 bg-black/80 h-screen w-full flex justify-center items-center" wire:click="closeModal">
                <div class="p-5 bg-slate-950 ring-1 ring-indigo-600 rounded-2xl">
                    <div class="flex justify-between items-center">
                        <h2>{{ $selectedMovie->title }}</h2>
                        <div class="relative">
                            <x-heroicon-c-x-mark class="w-10 cursor-pointer absolute right-0 -top-10" wire:click="closeModal"/>
                        </div>
                    </div>
                    <div class="flex items-stretch gap-5">
                        <img src="{{ Storage::disk('s3')->temporaryUrl($movie->storagePath.'/poster.jpg', now()->addMinutes()) }}" alt="{{ $selectedMovie->title }}" class="w-1/3 rounded-2xl">
                        <div class="w-2/3 self-stretch flex flex-col justify-between">
                            <p>{{ $selectedMovie->description }}</p>
                            <div class="flex gap-5 justify-end">
                                <a href="{{ route('movie.show', $selectedMovie->id) }}" wire:navigate>
                                    <x-filament::button class="bg-indigo-800 hover:bg-indigo-600">Regarder</x-filament::button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
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
        scrollLeft() {
            document.getElementById('movieList').scrollBy({
                left: -400,
                behavior: 'smooth',
            });
        },
        scrollRight() {
            document.getElementById('movieList').scrollBy({
                left: 400,
                behavior: 'smooth',
            });
        },


    }));
</script>
@endscript
