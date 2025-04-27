<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PlaylistSongPivot extends Pivot
{
    public const TABLE_NAME  = 'playlist_song';
    public const ID          = 'id';
    public const PLAYLIST_ID = 'playlist_id';
    public const SONG_ID     = 'song_id';

    protected $table = 'playlist_song';

    protected $fillable = [
        'song_id',
        'playlist_id',
    ];

    public function playlist()
    {
        return $this->belongsTo(Playlist::class);
    }

    public function song()
    {
        return $this->belongsTo(Song::class);
    }
}
