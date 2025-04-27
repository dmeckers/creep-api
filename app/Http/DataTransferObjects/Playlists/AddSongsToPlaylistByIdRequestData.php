<?php

declare(strict_types=1);

namespace App\Http\DataTransferObjects\Playlists;

use App\Models\Playlist;
use App\Models\Song;
use Spatie\LaravelData\Data;

class AddSongsToPlaylistByIdRequestData extends Data
{
    public function __construct(
        #[Exists(Playlist::TABLE_NAME, Playlist::ID)]
        public int $playlistId,
        #[Exists(Song::TABLE_NAME, Song::ID)]
        public int $songId,
    ) {
    }
}