<?php

declare(strict_types=1);

namespace App\Http\DataTransferObjects\Playlists;

use App\Models\Playlist;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;

class UpdatePlaylistRequestData extends Data
{
    public function __construct(
        #[Exists(Playlist::TABLE_NAME, 'id')]
        public int $playlistId,
        public string $name,
        public ?string $description,
    ) {
    }
}
