<?php

declare(strict_types=1);

namespace App\Http\DataTransferObjects\Playlists;

use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;

class GetStationPlaylistRequestData extends Data
{
    public function __construct(
        #[Exists('station', 'id'),]
        public string $stationId,
    ) {
    }
}
