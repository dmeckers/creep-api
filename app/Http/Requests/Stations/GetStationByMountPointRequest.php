<?php

declare(strict_types=1);

namespace App\Http\Requests\Stations;

use App\Http\DataTransferObjects\Stations\GetStationByMountPointRequestData;
use App\Http\Resources\PlaylistResource;
use App\Http\Resources\StationResource;
use App\Models\Station\Station;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class GetStationByMountPointRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function data(): GetStationByMountPointRequestData
    {
        return GetStationByMountPointRequestData::validateAndCreate([
            'stationMountPoint' => $this->route('station_mount_point'),
        ]);
    }

    public function responseResource(Station $station): StationResource
    {
        return StationResource::make($station);
    }
}
