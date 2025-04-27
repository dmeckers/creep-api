<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Station\Station;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StationQueue extends Model
{
    public const TABLE_NAME = 'station_queue';
    public const ID         = 'id';
    public const STATION_ID = 'station_id';

    protected $table = self::TABLE_NAME;

    protected $fillable = [
        self::STATION_ID,
    ];

    public function station(): BelongsTo
    {
        return $this->belongsTo(
            Station::class,
            self::STATION_ID,
            'id'
        );
    }

    public function trackBroadcasts(): HasMany
    {
        return $this->hasMany(
            TrackBroadcast::class,
            'station_queue_id',
            'id'
        );
    }

    public function getStationId(): int
    {
        return (int) $this->{self::STATION_ID};
    }

    public function relatedStation(): ?Station
    {
        return $this->station()->first();
    }
}
