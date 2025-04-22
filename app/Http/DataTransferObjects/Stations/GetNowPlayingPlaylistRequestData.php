<?php

declare(strict_types=1);

namespace App\Http\DataTransferObjects\Stations;

use Spatie\LaravelData\Data;

class GetNowPlayingPlaylistRequestData extends Data
{
    public function __construct(
        #[Exists('stations', 'id'),]
        public int $stationId,
    ) {
    }
}
