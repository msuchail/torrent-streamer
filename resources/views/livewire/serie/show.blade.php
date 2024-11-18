<div x-data="page">
    <x-slot name="title">{{ $serie->title }}</x-slot>
    <div id="video-container" class="rounded-xl overflow-hidden"></div>
    <div class="hidden md:block">
        <div class="grid grid-cols-12 items-center gap-5">
            <h1 class="col-span-8">
                {{ $serie->title }}
            </h1>
            <x-filament::input.wrapper class="!bg-transparent ring-0 col-span-2">
                <x-filament::input.select wire:model.live="seasonId" class="!bg-transparent rounded-xl">
                    @foreach($serie->seasons->where('status', 'done') as $key=>$season)
                        <option value="{{ $season->id }}">Saison {{ $key + 1 }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>
            <x-filament::input.wrapper class=":bg-transparent ring-0 col-span-2">
                <x-filament::input.select wire:model.live="episodeId"  class="!bg-transparent rounded-xl">
                    @foreach($season->episodes as $key=>$episode)
                        <option value="{{ $episode->id }}">Episode {{ $key + 1 }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </div>
        <h3>
            {{ $season->title }} : {{ $episode->title }}
        </h3>
        <p>
            {{ $season->description }}
        </p>
    </div>

</div>
@script
<script>
    Alpine.data('page', () => ({
        initPlayer() {
            console.log("shfjsqfgf")
            const video = document.createElement('video');
            video.setAttribute('id', 'video');
            video.setAttribute(
                'class',
                'w-full video-js',
            );
            document.getElementById('video-container').append(video);

            const player = videojs('video', {
                fluid: true,
                controls: true,
                language: 'fr',
                playbackRates: [0.5, 1, 1.5, 2],
            })

            player.src({
                src: $wire.videoUrl,
                type: "application/x-mpegURL",
            });
            // Swal.fire({
            //     title: 'Bienvenue',
            //     text: 'Appuyez sur la barre d\'espace pour mettre en pause ou reprendre la lecture. Appuyez sur les flèches gauche et droite pour reculer ou avancer de 10 secondes. Appuyez sur les flèches haut et bas pour augmenter ou diminuer le volume. Appuyez sur la touche F pour activer le mode plein écran.',
            //     icon: 'info',
            //     confirmButtonText: 'Compris',
            // })

            if($wire.initialSegment > 0) {
                Swal.fire({
                    title: 'Reprise de la lecture',
                    text: 'Voulez-vous reprendre la lecture là où vous vous étiez arrêté ?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Oui',
                    cancelButtonText: 'Non',
                }).then((result) => {
                    if (result.isConfirmed) {
                        player.currentTime($wire.initialSegment*60);
                        player.play()
                    } else {
                        player.play()
                    }
                });
            }


            // Keyboard shortcuts
            player.on('keydown', (event) => {
                if (event.which === 32) {
                    event.preventDefault();
                    player.paused() ? player.play() : player.pause();
                }
                if (event.which === 37) {
                    event.preventDefault();
                    player.currentTime(player.currentTime() - 10);
                }
                if (event.which === 39) {
                    event.preventDefault();
                    player.currentTime(player.currentTime() + 10);
                }
                if (event.which === 38) {
                    event.preventDefault();
                    player.volume(player.volume() + 0.1);
                }
                if (event.which === 40) {
                    event.preventDefault();
                    player.volume(player.volume() - 0.1);
                }
                if (event.which === 70) {
                    event.preventDefault();
                    document.querySelector('.vjs-fullscreen-control').click();
                }
            });

            $wire.subtitles.forEach(subtitle => {
                player.addRemoteTextTrack({
                    kind: 'subtitles',
                    default: false,
                    src: subtitle.url,
                    label: subtitle.name,
                });
            });
        },
        init() {
            this.initPlayer()
            $wire.on("video-changed", () => {
                setTimeout(() => {
                    this.destroy()
                    this.initPlayer()
                },500)

            })
        },
        destroy() {
            videojs('video').dispose();
        }
    }));
</script>
@endscript
