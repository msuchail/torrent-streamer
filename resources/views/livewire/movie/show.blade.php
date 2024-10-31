<div x-data="page">
    <h1>
        {{ $movie->title }}
    </h1>
    <div id="video-container" class="rounded-xl overflow-hidden"></div>
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
                            overrideNative: !videojs.browser.IS_SAFARI
                        },
                        nativeAudioTracks: false,
                        nativeVideoTracks: false
                    },
                    autoplay: true,
                    controls: true,
                    language: 'fr',
                    playbackRates: [0.5, 1, 1.5, 2],
                })

                player.src({
                    src: $wire.videoUrl,
                    type: "application/x-mpegURL",
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
