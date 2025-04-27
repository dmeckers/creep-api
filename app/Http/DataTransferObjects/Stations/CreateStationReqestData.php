<?php

declare(strict_types=1);

namespace App\Http\DataTransferObjects\Stations;

use App\Models\Station\Station;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

class CreateStationReqestData extends Data
{
    public function __construct(
        public string $name,
        public ?string $description = null,
        #[Unique(table: Station::TABLE_NAME, column: Station::MOUNT_POINT)]
        public ?string $mountPoint = null,
    ) {
    }
}
