<?php

declare(strict_types=1);

namespace App\Http\Requests\Playlists;

use App\Http\DataTransferObjects\Playlists\UpdatePlaylistRequestData;
use App\Http\Resources\PlaylistResource;
use App\Models\Playlist;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePlaylistRequest extends FormRequest
{
    public function data(): UpdatePlaylistRequestData
    {
        return resolve(UpdatePlaylistRequestData::class);
    }

    public function responseResource(Playlist $playlist): PlaylistResource
    {
        return PlaylistResource::make($playlist);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'playlistId' => $this->route('playlist_id'),
        ]);
    }
}
