<?php

declare(strict_types=1);

namespace App\Http\Requests\Stations;

use App\Http\DataTransferObjects\Stations\UpdateStationRequestData;
use App\Http\Resources\StationResource;
use App\Models\Station\Station;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function data(): UpdateStationRequestData
    {
        return resolve(UpdateStationRequestData::class);
    }

    public function responseResource(Station $station): StationResource
    {
        return StationResource::make($station);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'stationId' => $this->route('station_id'),
        ]);
    }
}
