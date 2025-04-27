<?php

declare(strict_types=1);

namespace App\Http\DataTransferObjects\Playlists;

use App\Models\Station\Station;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;

class CreatePlaylistRequestData extends Data
{
    public function __construct(
        public string $name,
        public ?string $description = null,
        public ?string $image = null,
        public ?string $type = 'public',
        public ?int $owner_id = null,
        #[Exists(table: Station::TABLE_NAME, column: Station::ID)]
        public ?int $station_id = null,
    ) {
    }
}
