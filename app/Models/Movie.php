<?php

namespace App\Models;

use App\Observers\MovieObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

#[ObservedBy(MovieObserver::class)]
class Movie extends Model
{
    protected $fillable = ['torrent', 'title', 'status', 'torrent_id', 'description', 'image', 'environment'];


    public function scopeActive($query)
    {
        return $query->where('status', 'done');
    }

    public function storagePath(): Attribute
    {
        return new Attribute(function () {
            return "downloads/complete/{$this->id}";
        });
    }
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'movie_groups');
    }
    public function video(): MorphOne
    {
        return $this->morphOne(Video::class, 'watchable');
    }
}
