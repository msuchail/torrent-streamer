<div x-data="page">
    <x-slot name="title">{{ $movie->title }}</x-slot>
    <div id="video-container" class="rounded-xl overflow-hidden"></div>
    <div class="hidden md:block">
        <h1>
            {{ $movie->title }}
        </h1>
        <p class="">
            {{ $movie->description }}
        </p>
    </div>

</div>
@script
    <script>
        Alpine.data('page', () => ({
            init() {
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
            destroy() {
                videojs('video').dispose();
            }
        }));
    </script>
@endscript
