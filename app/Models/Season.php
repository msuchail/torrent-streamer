<?php

namespace App\Models;

use App\Observers\SeasonObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(SeasonObserver::class)]
class Season extends Model
{
    protected $fillable = [
        'serie_id',
        'torrent',
        'torrent_id',
        'title',
        'description',
        'status',
    ];

    public function serie()
    {
        return $this->belongsTo(Serie::class);
    }
    public function episodes()
    {
        return $this->hasMany(Episode::class);
    }
}
