<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class PlaylistVideo extends Model
{
    protected $fillable = ["playlist_id", "video_id"];
}
