<?php

declare(strict_types=1);

namespace App\Http\Requests\Stations;

use App\Http\DataTransferObjects\Stations\GetNowPlayingPlaylistRequestData;
use App\Http\Resources\PlaylistResource;
use App\Models\Playlist;
use App\Models\Station\Station;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class GetNowPlayingPlaylistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function data(): GetNowPlayingPlaylistRequestData
    {
        return GetNowPlayingPlaylistRequestData::from([
            'stationId' => $this->route('station_id'),
        ]);
    }

    public function resourceResponse(Playlist $playlist): PlaylistResource
    {
        return PlaylistResource::make($playlist);
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $station = Station::where('id', $this->route('station_id'))->firstOrFail();

                if (false === $station->isLive()) {
                    $validator->errors()->add('station', 'Station is not live.');
                }
            }
        ];
    }
}
