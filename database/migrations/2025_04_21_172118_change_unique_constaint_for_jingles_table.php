<?php

use App\Models\Jingle;
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
        Schema::table('jingles', function (Blueprint $table) {
            $table->dropUnique([Jingle::CODE]);
            $table->unique([Jingle::CODE, 'station_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jingles', function (Blueprint $table) {
            $table->dropUnique([Jingle::CODE, 'station_id']);
            $table->unique([Jingle::CODE]);
        });
    }
};
