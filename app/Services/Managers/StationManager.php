<?php

declare(strict_types=1);

namespace App\Services\Managers;

use App\Events\StationStartedEvent;
use App\Jobs\CreateStationQueueJob;
use App\Jobs\StopStationBroadcast;
use App\Models\Station\Station;
use Exception;
use Illuminate\Redis\RedisManager;

class StationManager
{
    public function __construct(private readonly RedisManager $redis)
    {
    }

    public function spinUpStation(Station $station, int $playlistId = null)
    {
        $this->checkIfCanSpinUpStation($station);

        $station->setPlayingPlaylist(
            $playlistId
            ? $station->playlists()->find($playlistId)
            : $station->playlists()->first()
        );

        dispatch(new CreateStationQueueJob($station));
    }

    public function spinDownStation(Station $station)
    {
        $station->setIsLive(false);
        $station->save();

        dispatch(new StopStationBroadcast($station));
    }

    private function checkIfCanSpinUpStation(Station $station)
    {
        if ($station->isLive()) {
            throw new Exception('Station is already live');
        }

        if ($station->playlists()->count() === 0) {
            throw new Exception('Station has no playlists');
        }

        if ($station->playlists()->whereHas('songs')->count() === 0) {
            throw new Exception('Station has no songs');
        }
    }
}
