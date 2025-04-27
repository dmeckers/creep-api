<?php

declare(strict_types=1);

namespace App\Http\Requests\Playlists;

use App\Http\DataTransferObjects\Playlists\GetStationPlaylistRequestData;
use App\Http\Resources\PlaylistResource;
use App\Http\Resources\PlaylistResourceCollection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;

class GetStationPlaylistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'stationId' => [
                'required',
                'string',
                'exists:stations,id',
            ],
        ];
    }

    public function data(): GetStationPlaylistRequestData
    {
        return GetStationPlaylistRequestData::from([
            'stationId' => $this->route('station_id'),
        ]);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'stationId' => $this->route('station_id'),
        ]);
    }

    public function responseResource(Collection $playlists): PlaylistResourceCollection
    {
        return PlaylistResourceCollection::make($playlists);
    }
}
