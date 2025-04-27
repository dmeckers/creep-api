<?php

declare(strict_types=1);

namespace App\Models\Station;

use App\Models\StationQueue;
use App\Models\User;

class Station extends StationIndex
{
    public function relatedStationQueue(): ?StationQueue
    {
        return $this->stationQueue()->first();
    }

    public function relatedOwner(): ?User
    {
        return $this->owner()->first();
    }
}
