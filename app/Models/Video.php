<?php

namespace App\Models;

use App\Observers\VideoObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[ObservedBy(VideoObserver::class)]

class Video extends Model
{
    protected $fillable = ['watchable_id', 'watchable_type', 'segments_number'];


    protected function path(): Attribute
    {
        return Attribute::make(
            get: fn() => "downloads/complete/{$this->id}"
        );
    }
    public function watchable()
    {
        return $this->morphTo();
    }
    public function watching(): HasOne
    {
        return $this->hasOne(Watching::class);
    }
}
