<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Playlists\AddSongsToPlaylistByIdRequest;
use App\Http\Requests\Playlists\CreatePlaylistRequest;
use App\Http\Requests\Playlists\GetPlaylistSongsRequest;
use App\Http\Requests\Playlists\GetStationPlaylistRequest;
use App\Http\Requests\Playlists\RemoveSongFromPlaylistRequest;
use App\Http\Requests\Playlists\UpdatePlaylistRequest;
use App\Http\Resources\GetPlaylistSongsRequestResource;
use App\Http\Resources\PlaylistResource;
use App\Http\Resources\PlaylistResourceCollection;
use App\Services\Repositories\Playlists\PlaylistRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class PlaylistController extends Controller
{
    public function __construct(private readonly PlaylistRepository $playlistRepository)
    {
    }

    public function getStationPlaylists(GetStationPlaylistRequest $request): PlaylistResourceCollection
    {
        return $request->responseResource(
            $this->playlistRepository->getStationPlaylists($request->data()),
        );
    }

    public function updatePlaylist(UpdatePlaylistRequest $request): PlaylistResource
    {
        return $request->responseResource(
            $this->playlistRepository->updatePlaylist($request->data()),
        );
    }

    public function getPlaylistSongs(GetPlaylistSongsRequest $request): GetPlaylistSongsRequestResource
    {
        return $request->responseResource(
            $this->playlistRepository->getPlaylistSongs($request->data()),
        );
    }

    public function createPlaylist(CreatePlaylistRequest $request): PlaylistResource
    {
        return $request->responseResource(
            $this->playlistRepository->createPlaylist($request->data()),
        );
    }

    public function addSongsToPlaylistById(AddSongsToPlaylistByIdRequest $request): PlaylistResource
    {
        return $request->responseResource(
            $this->playlistRepository->addSongsToPlaylistById($request->data()),
        );
    }

    /**
     * @throws ModelNotFoundException
     */
    public function removeSongFromPlaylist(RemoveSongFromPlaylistRequest $request): JsonResponse
    {
        $this->playlistRepository->removeSongFromPlaylist($request->data());

        return $request->responseResource();
    }
}
