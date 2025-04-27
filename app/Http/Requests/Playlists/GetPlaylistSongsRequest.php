<?php

declare(strict_types=1);

namespace App\Http\Requests\Playlists;

use App\Http\DataTransferObjects\Playlists\GetPlaylistSongsRequestData;
use App\Http\Resources\GetPlaylistSongsRequestResource;
use App\Http\Resources\PlaylistResource;
use App\Models\Playlist;
use Illuminate\Foundation\Http\FormRequest;

class GetPlaylistSongsRequest extends FormRequest
{
    public function data(): GetPlaylistSongsRequestData
    {
        return GetPlaylistSongsRequestData::from([
            'playlistId' => (int) $this->route('playlist_id'),
            'page' => (int) $this->input('page', 1),
            'perPage' => (int) $this->input('per_page', 10),
        ]);
    }

    public function responseResource(Playlist $playlist): GetPlaylistSongsRequestResource
    {
        return GetPlaylistSongsRequestResource::make($playlist);
    }
}
