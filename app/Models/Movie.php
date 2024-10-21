<?php

namespace App\Models;

use App\Observers\MovieObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[ObservedBy(MovieObserver::class)]
class Movie extends Model
{
    protected $fillable = ['torrent', 'title', 'status', 'filename', 'torrent_id', 'description', 'image'];

    public function groupes()
    {
        return $this->belongsToMany(Groupe::class, 'movie_groupes');
    }


    public function scopeActive($query)
    {
        return $query->where('status', 'done');
    }
}
