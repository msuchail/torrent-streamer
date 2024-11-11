<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\Watching;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function master(Video $video)
    {
        return Storage::disk('s3')->download($video->path.'/master.m3u8');
    }
    public function video(Video $video, string $segment)
    {
        $segmentNumber = str_replace('prog_index', '', explode('.', $segment)[0]);

        if(!in_array($segmentNumber, ["", 0])) {
            Watching::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'video_id' => $video->id,
                ],
                ['segment' => $segmentNumber]
            );
        }

        return Storage::disk('s3')->download($video->path.'/video/'.$segment);
    }
    public function audio(Video $video, int $piste, string $segment)
    {
        return Storage::disk('s3')->download("$video->path/audio/$piste/$segment");
    }
    public function subtitle(Video $video, string $piste)
    {
        return Storage::disk('s3')->download("$video->path/srt/$piste");
    }
}
