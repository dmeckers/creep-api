<?php

declare(strict_types=1);

namespace App\Http\DataTransferObjects\Stations;

use App\Models\Station\Station;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;

class GetStationByMountPointRequestData extends Data
{
    public function __construct(
        #[Exists(Station::TABLE_NAME, Station::MOUNT_POINT)]
        public string $stationMountPoint,
    ) {
    }
}

