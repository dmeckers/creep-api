<?php

declare(strict_types=1);

namespace App\Http\Requests\Playlists;

use App\Http\DataTransferObjects\Playlists\CreatePlaylistRequestData;
use App\Http\Resources\PlaylistResource;
use App\Models\Playlist;
use Illuminate\Foundation\Http\FormRequest;

class CreatePlaylistRequest extends FormRequest
{
    public function data(): CreatePlaylistRequestData
    {
        return resolve(CreatePlaylistRequestData::class);
    }

    public function responseResource(Playlist $playlist): PlaylistResource
    {
        return PlaylistResource::make($playlist);
    }

    public function prepareForValidation()
    {
        $this->merge([
            'owner_id' => auth()->user()->id,
        ]);
    }
}
