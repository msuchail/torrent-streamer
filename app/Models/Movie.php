<?php

namespace App\Models;

use App\Observers\MovieObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

#[ObservedBy(MovieObserver::class)]
class Movie extends Model
{
    protected $fillable = ['torrent', 'title', 'status', 'torrent_id', 'description', 'image', 'environment'];


    public function scopeActive($query)
    {
        return $query->where('status', 'done');
    }

    public function videoUrl(): Attribute
    {
        return new Attribute(function () {
            return Storage::disk('s3')->url("downloads/complete/{$this->id}/master.m3u8");
        });
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
}
