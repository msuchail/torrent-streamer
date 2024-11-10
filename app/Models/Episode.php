<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Episode extends Model
{
    protected $fillable = ['title', 'season_id'];
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }
}
