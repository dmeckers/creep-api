<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private const TABLE            = 'track_broadcasts';
    private const SONG_ID          = 'song_id';
    private const BROADCAST_ID     = 'broadcast_id';
    private const START_AT         = 'start_at';
    private const BROADCASTED_AT   = 'broadcasted_at';
    private const ORDER            = 'order';
    private const STATION_QUEUE_ID = 'station_queue_id';

    private const STATION_QUEUE = 'station_queue';

    public function up(): void
    {
        Schema::create(self::STATION_QUEUE, function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->constrained('stations')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });

        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger(self::SONG_ID);
            $table->unsignedInteger(self::STATION_QUEUE_ID);
            $table->foreign(self::SONG_ID)->references('id')->on('songs')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign(self::STATION_QUEUE_ID)->references('id')->on(self::STATION_QUEUE)->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamp(self::START_AT);
            $table->timestamp(self::BROADCASTED_AT)->nullable();
            $table->unsignedInteger(self::ORDER)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE);
        Schema::dropIfExists(self::STATION_QUEUE);
    }
};
