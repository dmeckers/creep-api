<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;


class StationPlaylistPivot extends Pivot
{
    public const TABLE_NAME = 'station_playlist';

    public const STATION_ID  = 'station_id';
    public const PLAYLIST_ID = 'playlist_id';
    public const IS_PLAYING  = 'is_playing';

    protected $table = 'station_playlist';

    protected $fillable = [
        'station_id',
        'playlist_id',
        'is_playing',
    ];

    public function playlist()
    {
        return $this->belongsTo(Playlist::class);
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }
}
