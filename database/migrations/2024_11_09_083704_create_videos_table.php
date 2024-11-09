<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('title')->nullable();
            $table->morphs('watchable');
            $table->string('path');
        });

        \App\Models\Movie::all()->each(function (\App\Models\Movie $movie) {
            \App\Models\Video::create([
                'watchable_id' => $movie->id,
                'watchable_type' => \App\Models\Movie::class,
                'path' => "downloads/complete/{$movie->id}",
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
