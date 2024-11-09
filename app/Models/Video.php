<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = ['watchable_id', 'watchable_type'];


    protected function path(): Attribute
    {
        return Attribute::make(
            get: fn() => "downloads/complete/{$this->id}"
        );
    }
}
