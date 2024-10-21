<div>
    <x-slot name="title">TorrentStream</x-slot>
    @isset($selectedMovie)

        <div class="grid grid-cols-12 gap-12" wire:keydown.escape="closeMovie">
            <div class="col-span-4 bg-slate-800/50 p-5 rounded-2xl">
                <h2>Détails du film</h2>
                <img src="{{  Storage::temporaryUrl($selectedMovie->image, now()->addMinutes()) }}" alt="">
                <h3>{{ $selectedMovie->title }}</h3>
                <p>{{ $selectedMovie->description }}</p>
                <div class="flex justify-end">
                    <button
                        wire:click="watchMovie"
                        class="px-3 py-1.5 text-sm text-white duration-150 bg-indigo-600 rounded-full hover:bg-indigo-500 active:bg-indigo-700"
                    >
                        Regarder
                    </button>
                </div>
            </div>
            <div class="col-span-8 bg-slate-800/50 p-5 rounded-2xl">
                <h2>Films disponibles</h2>
                <div class="grid grid-cols-3">
                    @foreach($movies as $movie)
                        <x-ui.card class="" :h3="$movie->title" :image="Storage::temporaryUrl($movie->image, now()->addMinutes())" wire:click.prevent="setMovie({{$movie}})"></x-ui.card>
                    @endforeach
                </div>
            </div>
        </div>

        @if($showModal)
            @teleport('body')
            <div class="fixed top-0 left-0 h-screen w-full bg-black/50 flex justify-center items-center" wire:keydown.escape="closeMovie" wire:click="closeMovie">
                <div class="w-2/3 aspect-video">
                    <video class="rounded-xl w-full" src="{{ route('stream', ['filename' => $selectedMovie->filename]) }}" autoplay controls />

                </div>
            </div>
            @endteleport
        @endif
    @else
        <x-ui.alert-warning title="Aucun film disponible actuellement">
            Il n'y a aucun film disponible actuellement, veuillez réessayer plus tard.
        </x-ui.alert-warning>
    @endisset
</div>
