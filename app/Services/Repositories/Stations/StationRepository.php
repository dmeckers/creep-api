<?php

declare(strict_types=1);

namespace App\Services\Repositories\Stations;

use App\Http\DataTransferObjects\Stations\GetNowPlayingPlaylistRequestData;
use App\Models\Playlist;
use App\Models\Station;
use Illuminate\Database\Eloquent\Model;

class StationRepository
{
    public function __construct(
        private readonly Station $stationModel
    ) {
    }

    public function getNowPlayingPlaylist(GetNowPlayingPlaylistRequestData $data): Model|Playlist
    {
        return $this->stationModel->findOrFail($data->stationId)->playingPlaylist()->firstOrFail();
    }
}
