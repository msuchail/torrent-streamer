<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
