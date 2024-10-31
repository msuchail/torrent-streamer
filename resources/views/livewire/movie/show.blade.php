<div x-data="page">
    <h1>
        {{ $movie->title }}
    </h1>
    <div class="rounded-xl overflow-hidden w-full">
        <video id="video" class="video-js"></video>
    </div>
    <div class="hidden md:block">
        <h2>Synopsis :</h2>
        <p class="">
            {{ $movie->description }}
        </p>
    </div>

</div>
@script
    <script>
        Alpine.data('page', () => ({
            init() {
                const player = videojs('video', {
                    html5: {
                        vhs: {
                            overrideNative: false
                        },
                        nativeAudioTracks: false,
                        nativeVideoTracks: false
                    },
                    fluid: true,
                    autoplay: true,
                    controls: true,
                    language: 'fr',
                    playbackRates: [0.5, 1, 1.5, 2],
                });

                player.src({
                    src: '{{ $movie->videoUrl }}',
                    type: 'application/x-mpegURL',
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
