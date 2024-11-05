<div x-data="page">
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
                    html5: {
                        vhs: {
                            overrideNative: true,
                        },
                        nativeAudioTracks: false,
                        nativeVideoTracks: false
                    },
                    fluid: true,
                    controls: true,
                    language: 'fr',
                    playbackRates: [0.5, 1, 1.5, 2],
                })

                player.src({
                    src: $wire.videoUrl,
                    type: "application/x-mpegURL",
                });

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
                document.querySelector('.vjs-big-play-button').click();
                document.querySelector('.vjs-fullscreen-control').click();


            },
            destroy() {
                videojs('video').dispose();
            }
        }));
    </script>
@endscript
