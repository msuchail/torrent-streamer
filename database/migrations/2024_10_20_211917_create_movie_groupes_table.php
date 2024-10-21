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
        Schema::create('movie_groupes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('movie_id')->nullable(false)->constrained()->cascadeOnDelete();
            $table->foreignId('groupe_id')->nullable(false)->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movie_groupes');
    }
};
