<?php

namespace App\Models;

use App\Observers\SerieObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


#[ObservedBy(SerieObserver::class)]
class Serie extends Model
{
    protected $fillable = [
        'title',
        'description',
        'status',
        'image',
        'environment',
    ];

    public function seasons(): HasMany
    {
        return $this->hasMany(Season::class);
    }
}
