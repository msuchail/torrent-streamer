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
        Schema::table('movie_groupes', function (Blueprint $table) {
            $table->renameColumn('groupe_id', 'group_id');
        });
        Schema::rename('movie_groupes', 'movie_groups');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movie_groups', function (Blueprint $table) {
            $table->renameColumn('group_id', 'groupe_id');
        });
        Schema::rename('movie_groups', 'movie_groupes');
    }
};
