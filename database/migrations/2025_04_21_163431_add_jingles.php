<?php

use App\Models\Jingle;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jingles', function (Blueprint $table) {
            $table->id();
            $table->string(Jingle::CODE)->unique();
            $table->foreignId('station_id')->constrained('stations')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jingles');
    }
};
