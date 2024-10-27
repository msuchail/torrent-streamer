<?php

namespace App\Models;

use App\Observers\MovieObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[ObservedBy(MovieObserver::class)]
class Movie extends Model
{
    protected $fillable = ['torrent', 'title', 'status', 'torrent_id', 'description', 'image'];

    public function groupes()
    {
        return $this->belongsToMany(Groupe::class, 'movie_groupes');
    }


    public function scopeActive($query)
    {
        return $query->where('status', 'done');
    }

    public function videoUrl(): Attribute
    {
        return new Attribute(function () {
            return Storage::disk('public')->url("downloads/complete/{$this->title}/master.m3u8");
        });
    }
    public function storagePath(): Attribute
    {
        return new Attribute(function () {
            return "downloads/complete/{$this->title}";
        });
    }
}
