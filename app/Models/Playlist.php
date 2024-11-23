<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Playlist extends Model
{
    protected $fillable = ["user_id", "title"];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function videos(): HasManyThrough
    {
        return $this->hasManyThrough(Video::class, PlaylistVideo::class);
    }
}
