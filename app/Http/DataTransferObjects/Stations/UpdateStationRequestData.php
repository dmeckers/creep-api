<?php

declare(strict_types=1);

namespace App\Http\DataTransferObjects\Stations;

use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;

class UpdateStationRequestData extends Data
{
    public function __construct(
        #[Exists('stations', 'id'),]
        public int $stationId,
        public string $name,
        public ?string $description = null,
        // public ?string $url = null,
        // public ?string $genre = null,
        // public ?string $language = null,
    ) {
    }
}