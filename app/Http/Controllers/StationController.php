<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Stations\CreateStationReqest;
use App\Http\Requests\Stations\DeleteStationRequest;
use App\Http\Requests\Stations\GetCurrentSongFromQueueRequest;
use App\Http\Requests\Stations\GetNowPlayingPlaylistRequest;
use App\Http\Requests\Stations\GetStationByMountPointRequest;
use App\Http\Requests\Stations\SearchStationsRequest;
use App\Http\Requests\Stations\SpinDownStationRequest;
use App\Http\Requests\Stations\SpinUpStationRequest;
use App\Http\Requests\Stations\UpdateStationRequest;
use App\Http\Resources\PlaylistResource;
use App\Http\Resources\StationResource;
use App\Http\Resources\StationResourceCollection;
use App\Http\Resources\TrackBroadcastResource;
use App\Services\Managers\StationManager;
use App\Services\Repositories\Stations\StationRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class StationController extends Controller
{
    public function __construct(
        private readonly StationRepository $stationRepository,
        private readonly StationManager $stationManager
    ) {
    }

    public function getNowPlayingPlaylist(GetNowPlayingPlaylistRequest $request): PlaylistResource
    {
        return $request->resourceResponse(
            $this->stationRepository->getNowPlayingPlaylist($request->data())
        );
    }

    public function getUserStations(): JsonResponse
    {
        return response()->json(['stations' => $this->stationRepository->getUserStations()]);
    }

    public function updateStation(UpdateStationRequest $request): StationResource
    {
        return $request->responseResource(
            $this->stationRepository->updateStation($request->data())
        );
    }

    public function getCurrentSongFromQueue(GetCurrentSongFromQueueRequest $request): TrackBroadcastResource
    {
        return $request->responseResource(
            $this->stationRepository->getCurrentSongFromQueue($request->data())
        );
    }

    /**
     * @throws ModelNotFoundException
     */
    public function spinUpStation(SpinUpStationRequest $request): JsonResponse
    {
        $payload = $request->data();

        $this->stationManager->spinUpStation(
            $this->stationRepository->findOrFail($payload->stationId),
            $payload->playlistId
        );

        return response()->json(['message' => 'Station is spinning up']);
    }

    /**
     * @throws ModelNotFoundException
     */
    public function spinDownStation(SpinDownStationRequest $request): JsonResponse
    {
        $payload = $request->data();

        $this->stationManager->spinDownStation(
            $this->stationRepository->findOrFail($payload->stationId)
        );

        return response()->json(['message' => 'Station is spinning down']);
    }

    public function searchStations(SearchStationsRequest $request): StationResourceCollection
    {
        return $request->responseResource(
            $this->stationRepository->searchStations($request->data())
        );
    }

    public function createStation(CreateStationReqest $request): StationResource
    {
        return $request->responseResource(
            $this->stationRepository->createStation($request->data())
        );
    }

    /**
     * @throws ModelNotFoundException
     */
    public function deleteStation(DeleteStationRequest $request): JsonResponse
    {
        $this->stationRepository->deleteStation($request->data());

        return $request->responseResource();
    }

    public function getStationByMountPoint(GetStationByMountPointRequest $request): StationResource
    {
        return $request->responseResource(
            $this->stationRepository->getStationByMountPoint($request->data())
        );
    }
}
