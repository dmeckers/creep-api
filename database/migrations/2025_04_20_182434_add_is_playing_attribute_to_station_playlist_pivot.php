<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('station_playlist', function (Blueprint $table) {
            $table->boolean('is_playing')->default(false)->after('playlist_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('station_playlist', function (Blueprint $table) {
            $table->dropColumn('is_playing');
        });
    }
};
