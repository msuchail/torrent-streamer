<?php

namespace App\Models;

use App\Observers\SeasonObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ObservedBy(SeasonObserver::class)]
class Season extends Model
{
    protected $fillable = [
        'environment',
        'serie_id',
        'image',
        'torrent',
        'torrent_id',
        'description',
        'status',
        'order'
    ];

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'serie_groups');
    }

    public function serie()
    {
        return $this->belongsTo(Serie::class);
    }
    public function episodes()
    {
        return $this->hasMany(Episode::class);
    }
}
