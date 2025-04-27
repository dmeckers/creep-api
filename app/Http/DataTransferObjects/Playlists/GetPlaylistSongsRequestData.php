<?php

declare(strict_types=1);

namespace App\Http\DataTransferObjects\Playlists;

use Spatie\LaravelData\Data;

class GetPlaylistSongsRequestData extends Data
{
    public function __construct(
        public int $playlistId,
        public ?int $page = 1,
        public ?int $per_page = 10,
        public ?string $sort_by = 'created_at',
    ) {
    }
}
