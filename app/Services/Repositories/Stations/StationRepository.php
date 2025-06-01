<?php

declare(strict_types=1);

namespace App\Services\Repositories\Stations;

use App\Http\DataTransferObjects\Stations\CreateStationReqestData;
use App\Http\DataTransferObjects\Stations\DeleteStationRequestData;
use App\Http\DataTransferObjects\Stations\GetCurrentSongFromQueueRequestData;
use App\Http\DataTransferObjects\Stations\GetNowPlayingPlaylistRequestData;
use App\Http\DataTransferObjects\Stations\GetStationByMountPointRequestData;
use App\Http\DataTransferObjects\Stations\UpdateStationRequestData;
use App\Http\DataTransferObjects\Stations\SearchStationsRequestData;
use App\Http\Requests\Stations\DeleteStationRequest;
use App\Jobs\StopStationBroadcast;
use App\Models\Playlist;
use App\Models\Station\Station;
use App\Models\TrackBroadcast;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Laravel\Scout\Builder;

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

    public function getUserStations(): Collection
    {
        return $this->stationModel
            ->where(column: 'owner_id', operator: '=', value: auth()->id())
            ->with(
                [
                    Station::PLAYLIST_RELATION => fn($query) => $query->select([
                        Playlist::TABLE_NAME . '.' . Playlist::ID,
                        Playlist::TABLE_NAME . '.' . Playlist::NAME,
                    ]),
                    Station::PLAYLIST_RELATION . '.' . Playlist::SONGS_RELATION => fn($query) => $query->select([
                        'songs.id',
                    ]),
                ]
            )
            ->get();
    }

    public function updateStation(UpdateStationRequestData $data): Model|Station
    {
        $station = $this->stationModel->findOrFail($data->stationId);

        $station->update([
            'name' => $data->name,
            'description' => $data->description,
        ]);

        return $station->refresh();
    }

    /**
     * @throws ModelNotFoundException
     */
    public function getCurrentSongFromQueue(string $mountPoint): TrackBroadcast
    {
        $stationQueue = $this->findByMountPoint($mountPoint)?->relatedStationQueue();

        if ($stationQueue === null) {
            throw new ModelNotFoundException('Station queue not found');
        }

        return $stationQueue->trackBroadcasts()
            ->where(TrackBroadcast::START_AT, '<=', now())
            ->with([
                TrackBroadcast::SONG_RELATION
            ])
            ->orderByDesc(TrackBroadcast::ORDER)
            ->firstOrFail();
    }

    /**
     * @throws ModelNotFoundException
     */
    public function findOrFail(int $stationId): Station
    {
        return $this->stationModel->findOrFail($stationId);
    }

    public function findByMountPoint(string $mountPoint): ?Station
    {
        return $this->stationModel->firstWhere(
            Station::MOUNT_POINT,
            '=',
            $mountPoint
        );
    }

    public function searchStations(SearchStationsRequestData $data): LengthAwarePaginator
    {
        return $this->stationModel
            ->search($data->query)
            ->where(Station::IS_LIVE, true)
            ->when(
                $data->filterBy,
                fn(Builder $query) => $query->where(
                    $data->filterBy,
                    $data->query
                )
            )
            ->when(
                $data->sortBy,
                fn(Builder $query) => $query->orderBy(
                    $data->sortBy,
                    $data->sortDirection ?? 'asc'
                )
            )
            ->paginate(
                perPage: $data->perPage,
                page: $data->page
            );
    }

    public function createStation(CreateStationReqestData $data): Station
    {
        $mountPoint = $data->mountPoint ?? Str::of($data->name)
            ->slug()
            ->kebab()
            ->toString();

        return $this->stationModel->create(
            [
                ...$data->toArray(),
                Station::MOUNT_POINT => $mountPoint,
                Station::STREAM_URL => config('app.url') . $mountPoint . '/stream',
                Station::OWNER_ID => auth()->id(),
            ]
        );
    }

    /**
     * @throws ModelNotFoundException
     */
    public function deleteStation(DeleteStationRequestData $data): void
    {
        $station = $this->findOrFail($data->stationId);

        if ($station->isLive()) {
            StopStationBroadcast::dispatch($station);
        }

        $station->delete();
    }

    /**
     * @throws ModelNotFoundException
     */
    function getStationByMountPoint(GetStationByMountPointRequestData $data): Station
    {
        return $this->stationModel
            ->where(
                Station::MOUNT_POINT,
                '=',
                $data->stationMountPoint
            )
            ->firstOrFail();
    }
}
