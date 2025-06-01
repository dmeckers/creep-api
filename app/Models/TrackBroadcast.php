<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Station\Station;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackBroadcast extends \Illuminate\Database\Eloquent\Model
{
    public const TABLE_NAME       = 'track_broadcasts';
    public const ID               = 'id';
    public const SONG_ID          = 'song_id';
    public const START_AT         = 'start_at';
    public const ORDER            = 'order';
    public const STATION_QUEUE_ID = 'station_queue_id';
    public const BROADCASTED_AT   = 'broadcasted_at';

    protected $table = self::TABLE_NAME;

    protected $fillable = [
        'song_id',
        'start_at',
        'broadcasted_at',
        'order',
        'station_queue_id',
    ];

    public const SONG_RELATION          = 'song';
    
    protected $casts = [
        self::START_AT       => 'datetime',
        self::BROADCASTED_AT => 'datetime',
    ];

    public function song(): BelongsTo
    {
        return $this->belongsTo(
            Song::class,
            self::SONG_ID,
            Song::ID
        );
    }

    public function stationQueue(): BelongsTo
    {
        return $this->belongsTo(
            StationQueue::class,
            self::STATION_QUEUE_ID,
            StationQueue::ID
        );
    }

    public function relatedSong(): Song
    {
        return $this->song()->first();
    }

    public function relatedStationQueue(): ?StationQueue
    {
        return $this->stationQueue()->first();
    }

    public function getStartAt(): Carbon
    {
        return $this->getAttribute(self::START_AT);
    }

    public function getBroadcastedAt(): ?Carbon
    {
        return $this->getAttribute(self::BROADCASTED_AT);
    }

    public function getStationId(): int
    {
        return $this->relatedStationQueue()->getStationId();
    }

    public function getOrder(): int
    {
        return $this->getAttribute(self::ORDER);
    }

    public function relatedStation(): ?Station
    {
        return $this->relatedStationQueue()?->relatedStation();
    }

    public function getId(): int
    {
        return $this->getAttribute(self::ID);
    }
}
