<?php

declare(strict_types=1);

namespace App\Services\Repositories\Playlists;

use App\Http\DataTransferObjects\Playlists\AddSongsToPlaylistByIdRequestData;
use App\Http\DataTransferObjects\Playlists\CreatePlaylistRequestData;
use App\Http\DataTransferObjects\Playlists\GetPlaylistSongsRequestData;
use App\Http\DataTransferObjects\Playlists\GetStationPlaylistRequestData;
use App\Http\DataTransferObjects\Playlists\RemoveSongFromPlaylistRequestData;
use App\Http\DataTransferObjects\Playlists\UpdatePlaylistRequestData;
use App\Models\Playlist;
use App\Models\PlaylistSongPivot;
use App\Models\Song;
use App\Models\Station\Station;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

class PlaylistRepository
{
    public function __construct(
        private readonly Playlist $playlist,
        private readonly Song $song
    ) {
    }

    public function getStationPlaylists(GetStationPlaylistRequestData $data): Collection
    {
        $data = $this->playlist
            ->whereRelation(
                relation: Playlist::STATIONS_RELATION,
                column: Station::TABLE_NAME . "." . Station::ID,
                operator: '=',
                value: $data->stationId
            )
            ->get();

        return $data;
    }

    public function updatePlaylist(UpdatePlaylistRequestData $data): Playlist
    {
        $playlist = $this->playlist->findOrFail($data->playlistId);

        $playlist->update($data->toArray());

        return $playlist->refresh();
    }

    public function getPlaylistSongs(GetPlaylistSongsRequestData $data): Playlist
    {
        $paginated = $this->song->latest()
            ->whereRelation(
                relation: Song::PLAYLISTS_RELATION,
                column: PlaylistSongPivot::TABLE_NAME . "." . PlaylistSongPivot::PLAYLIST_ID,
                operator: '=',
                value: $data->playlistId
            )
            ->paginate(perPage: $data->per_page, page: $data->page);

        $playlist = $this->playlist->findOrFail($data->playlistId);

        return $playlist->setSongsPaginated($paginated);
    }

    public function createPlaylist(CreatePlaylistRequestData $data): Playlist
    {
        $playlist = $this->playlist->create($data->toArray());

        if ($data->station_id) {
            $playlist->stations()->attach($data->station_id);
        }

        // if ($data->songs) {
        //     $playlist->songs()->attach($data->songs);
        // }

        return $playlist->refresh();
    }

    public function addSongsToPlaylistById(AddSongsToPlaylistByIdRequestData $data): Playlist
    {
        $playlist = $this->playlist->findOrFail($data->playlistId);

        $songs = $this->song->where(Song::ID, $data->songId)->firstOrFail();

        $playlist->songs()->attach($songs);

        return $playlist->refresh();
    }

    /**
     * @throws ModelNotFoundException
     */
    public function findOrFail(int $playlistId): Playlist
    {
        return $this->playlist->findOrFail($playlistId);
    }

    /**
     * @throws ModelNotFoundException
     */
    public function removeSongFromPlaylist(RemoveSongFromPlaylistRequestData $data): void
    {
        $this->findOrFail($data->playlistId)->songs()->detach($data->songId);
    }
}
