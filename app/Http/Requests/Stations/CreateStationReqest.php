<?php

declare(strict_types=1);

namespace App\Http\Requests\Stations;

use App\Http\DataTransferObjects\Stations\CreateStationReqestData;
use App\Http\Resources\StationResource;
use App\Models\Station\Station;
use Illuminate\Foundation\Http\FormRequest;

class CreateStationReqest extends FormRequest
{
    public function responseResource(Station $station): StationResource
    {
        return new StationResource($station);
    }

    public function data(): CreateStationReqestData
    {
        return resolve(CreateStationReqestData::class);
    }
}
