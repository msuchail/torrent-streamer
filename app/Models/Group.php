<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use SoftDeletes;

    protected $fillable = ['name'];


    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function movies(): HasManyThrough
    {
        return $this->hasManyThrough(Movie::class, MovieGroup::class, 'group_id', 'id', 'id', 'movie_id');
    }
}
