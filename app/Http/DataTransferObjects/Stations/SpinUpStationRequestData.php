<?php

declare(strict_types=1);

namespace App\Http\DataTransferObjects\Stations;

use App\Models\Playlist;
use App\Models\Station\Station;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;

class SpinUpStationRequestData extends Data
{
    public function __construct(
        #[Exists(table: Station::TABLE_NAME, column: Station::ID)]
        public int $stationId,
        #[Exists(table: Playlist::TABLE_NAME, column: Playlist::ID)]
        public ?int $playlistId = null,
    ) {
    }
}
