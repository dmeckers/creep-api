<?php

declare(strict_types=1);

namespace App\Http\Requests\Stations;

use App\Http\DataTransferObjects\Stations\GetCurrentSongFromQueueRequestData;
use App\Http\Resources\TrackBroadcastResource;
use App\Models\Station\Station;
use App\Models\TrackBroadcast;
use App\Rules\IsStationLiveRule;
use Illuminate\Foundation\Http\FormRequest;

class GetCurrentSongFromQueueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(IsStationLiveRule $isStationLiveRule): array
    {
        return [
            'mountPoint' => [
                'required',
                'string',
                'exists:' . Station::TABLE_NAME . ',' . Station::MOUNT_POINT,
                $isStationLiveRule
            ],
        ];
    }

    public function data(): string
    {
        return $this->validated()['mountPoint'];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'mountPoint' => $this->route('station_mount_point'),
        ]);
    }

    public function responseResource(TrackBroadcast $trackBroadcast): TrackBroadcastResource
    {
        return new TrackBroadcastResource($trackBroadcast);
    }
}
