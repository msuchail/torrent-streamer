<?php

namespace App\Http\Controllers;

use App\Models\Video;
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
