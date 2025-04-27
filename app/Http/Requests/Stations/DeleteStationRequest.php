<?php

declare(strict_types=1);

namespace App\Http\Requests\Stations;

use App\Http\DataTransferObjects\Stations\DeleteStationRequestData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

class DeleteStationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function data(): DeleteStationRequestData
    {
        return DeleteStationRequestData::validateAndCreate(
            [
                'stationId' => (int) $this->route('station_id'),
            ]
        );
    }

    public function responseResource(): JsonResponse
    {
        return response()->json(['message' => 'Station deleted successfully.']);
    }
}
