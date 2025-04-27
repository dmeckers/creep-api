<?php

declare(strict_types=1);

namespace App\Http\Requests\Stations;

use App\Http\DataTransferObjects\Stations\SpinDownStationRequestData;
use App\Http\DataTransferObjects\Stations\SpinUpStationRequestData;
use Illuminate\Foundation\Http\FormRequest;

class SpinDownStationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function data(): SpinDownStationRequestData
    {
        return resolve(SpinDownStationRequestData::class);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'stationId' => $this->route('station_id'),
        ]);
    }
}
