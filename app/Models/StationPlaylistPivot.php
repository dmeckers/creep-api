<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;


class StationPlaylistPivot extends Pivot
{
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
