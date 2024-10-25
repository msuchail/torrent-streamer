<div x-data="home" x-on:keyup.escape="closeMovie">
    <x-slot name="title">TorrentStream</x-slot>
    @isset($selectedMovie)
        <div class="grid grid-cols-12 gap-12">
            <div class="col-span-4 bg-slate-800/50 p-5 rounded-2xl h-[75vh] flex flex-col justify-between">
                <div>
                    <h2>Détails du film</h2>
                    <img src="{{  Storage::temporaryUrl($selectedMovie->image, now()->addMinutes()) }}" alt="">
                    <h3>{{ $selectedMovie->title }}</h3>
                    <p>{{ $selectedMovie->description }}</p>
                </div>
                <div class="flex justify-center">
                    <button
                        x-on:click="watchMovie"
                        class="px-3 py-1.5 text-xl text-white duration-150 bg-indigo-600 rounded-full hover:bg-indigo-500 active:bg-indigo-700 w-1/2"
                    >
                        Regarder
                    </button>
                </div>
            </div>
            <div class="col-span-8 bg-slate-800/50 p-5 rounded-2xl">
                <h2>Films disponibles</h2>
                <div class="grid grid-cols-3 gap-5">
                    @foreach($movies as $movie)
                        <x-ui.card class="" :h3="$movie->title" :image="Storage::temporaryUrl($movie->image, now()->addMinutes())" wire:click.prevent="setMovie({{$movie}})" wire:key="{{ $movie->id }}"></x-ui.card>
                    @endforeach
                </div>
            </div>
        </div>
        <template x-if="open">
            <div class="fixed top-0 left-0 h-screen w-full bg-black/50 flex justify-center items-center" x-on:click="closeMovie">
                <div class="fixed w-2/3" x-on:click.stop="" id="video-container">
                </div>
            </div>
        </template>
    @else
        <x-ui.alert-warning title="Aucun film disponible actuellement">
            Il n'y a aucun film disponible actuellement, veuillez réessayer plus tard.
        </x-ui.alert-warning>
    @endisset
@script
<script>
    Alpine.data('home', () => ({
        open: false,
        changeMe: 0,

        closeMovie() {
            videojs('video').dispose();
            this.open = false;
        },
        watchMovie() {
            this.open = true;

            setTimeout(() => {
                this.initPlayer();
            }, 1);
        },

        initPlayer() {
            const video = document.createElement('video');
            video.setAttribute('id', 'video');
            video.setAttribute(
                'class',
                'w-full video-js rounded-xl',
            );

            document.getElementById('video-container').append(video);


            const player = videojs('video', {
                autoplay: true,
                controls: true,
                language: 'fr',
                playbackRates: [0.5, 1, 1.5, 2],
                sources: [
                    {
                        src: $wire.videoUrl,
                        type: 'video/mp4',
                    },
                ],
            })
        },

    }));
</script>
@endscript
