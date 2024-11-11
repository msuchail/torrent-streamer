<?php

namespace App\Models;

use App\Observers\EpisodeObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

#[ObservedBy(EpisodeObserver::class)]
class Episode extends Model
{
    protected $fillable = ['title', 'season_id'];
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }


    public function video(): MorphOne
    {
        return $this->morphOne(Video::class, 'watchable');
    }
}
