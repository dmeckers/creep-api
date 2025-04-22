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
        Schema::create('stations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_live')->default(false);
            $table->string('mount_point')->unique();
            $table->string('stream_url')->unique();
            $table->string('stream_type')->default('audio/mpeg');
            $table->string('stream_format')->default('mp3');
            $table->string('stream_bitrate')->default('128k');
            $table->string('stream_sample_rate')->default('44100');
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->boolean('is_public')->default(true);
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('genres', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('genreables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('genre_id')->constrained()->cascadeOnDelete();
            $table->morphs('genreable');
            $table->timestamps();
        });

        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->morphs('likeable');
            $table->bigInteger('like_count')->unsigned()->default(0);
            $table->timestamps();
        });

        Schema::create('artists', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->string('description')->nullable();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });

        Schema::create('playlists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });

        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->foreignId('artist_id')->nullable()->constrained('artists')->nullOnDelete()->cascadeOnUpdate();
            $table->string('description')->nullable();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });

        Schema::create('playlist_song', function (Blueprint $table) {
            $table->id();
            $table->foreignId('playlist_id')->constrained('playlists')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('song_id')->constrained('songs')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });

        Schema::create('station_playlist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->constrained('stations')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('playlist_id')->constrained('playlists')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });

        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->morphs('imageable');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('path');
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
        Schema::dropIfExists('station_playlist');
        Schema::dropIfExists('playlist_song');
        Schema::dropIfExists('songs');
        Schema::dropIfExists('playlists');
        Schema::dropIfExists('artists');
        Schema::dropIfExists('likes');
        Schema::dropIfExists('genres');
        Schema::dropIfExists('stations');
    }
};
