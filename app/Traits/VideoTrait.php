<?php

namespace App\Traits;

use App\Models\Movie;
use Illuminate\Support\Facades\Storage;

trait VideoTrait
{
    use LocalesTrait;
    private string $path;
    private string $storagePath;
    private string $baseFile;
    private string $hlsFormat;

    private string $master="#EXTM3U\n#EXT-X-VERSION:3\n\n";


    public function __construct(
        private readonly Movie $movie,
        private readonly int   $segmentDuration = 60
    ){
        $this->storagePath = "downloads/complete/{$this->movie->id}";
        $this->hlsFormat = "-f hls -hls_time $this->segmentDuration -hls_list_size 0";
        $this->path = "storage/app/public/downloads/complete/{$this->movie->id}";
    }


    public function convertAll(): void
    {
        $this->convertVideo();
        $this->convertAudio();
        $this->convertSubtitles();
        Storage::disk('public')->put("$this->storagePath/master.m3u8", $this->master);
    }

    public function convertVideo(): void
    {
        Storage::disk('public')->makeDirectory("$this->storagePath/video");
        error_log($this->baseFile);
        error_log($this->path);
        shell_exec("ffmpeg -i '$this->baseFile' -map 0:v:0 -c:v copy $this->hlsFormat '{$this->path}/video/prog_index.m3u8' -y");

        $this->master .= "\n#EXT-X-STREAM-INF:AUDIO=\"stereo\",SUBTITLES=\"subs\"\nvideo/prog_index.m3u8\n";

    }

    public function convertAudio(): void
    {
        Storage::disk('public')->makeDirectory("$this->storagePath/audio");
        for ($i = 0; $i < 5; $i++) {
            Storage::disk('public')->makeDirectory("$this->storagePath/audio/$i");
            shell_exec("ffmpeg -i '$this->baseFile' -map 0:a:$i -c:a copy $this->hlsFormat '{$this->path}/audio/$i/prog_index.m3u8' -y");

            if(Storage::disk('public')->exists("$this->storagePath/audio/$i/prog_index.m3u8")) {
                // On récupère le nom de la langue
                $lang = trim(strtolower(shell_exec("ffprobe -v error -show_entries stream_tags=language -of default=noprint_wrappers=1:nokey=1 '$this->baseFile' -select_streams a:$i")));
                $language = $this->matchingLanguage($lang, $i);

                $default = $i === 1 ? 'YES' : 'NO';
                $this->master .= "\n#EXT-X-MEDIA:TYPE=AUDIO,GROUP-ID=\"stereo\",LANGUAGE=\"$lang\",NAME=\"$language\",DEFAULT=$default,AUTOSELECT=YES,URI=\"audio/$i/prog_index.m3u8\"\n";
            } else {
                Storage::disk('public')->deleteDirectory("$this->storagePath/audio/$i");
            }
        }
    }

    public function convertSubtitles(): void
    {
        Storage::disk('public')->makeDirectory("$this->storagePath/srt");
        for($i = 0; $i < 5; $i++) {
            //On récupère le nom de la langue
            $lang = trim(strtolower(shell_exec("ffprobe -v error -show_entries stream_tags=language -of default=noprint_wrappers=1:nokey=1 '$this->baseFile' -select_streams s:$i")));

            //On vérifie que les sous titres ne sont pas forcés
            $forced = str_contains(trim(strtolower(shell_exec("ffprobe -v error -show_entries stream_tags=title -of default=noprint_wrappers=1:nokey=1 '$this->baseFile' -select_streams s:$i"))), "force");

            $addition = $forced ? 'forced' : 'regular';
            shell_exec("ffmpeg -i '$this->baseFile' -map 0:s:$i -c:s webvtt '{$this->path}/srt/$lang.$i.$addition.vtt' -y");
        }
    }
}