<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Stations\GetNowPlayingPlaylistRequest;
use App\Http\Resources\PlaylistResource;
use App\Services\Repositories\Stations\StationRepository;

class StationController extends Controller
{
    public function __construct(private readonly StationRepository $stationRepository)
    {
    }

    public function getNowPlayingPlaylist(GetNowPlayingPlaylistRequest $request): PlaylistResource
    {
        return $request->resourceResponse(
            $this->stationRepository->getNowPlayingPlaylist($request->data())
        );
    }

    // public function goLive()
    // {
    // }

    // public function goOffline()
    // {
    // }
}
