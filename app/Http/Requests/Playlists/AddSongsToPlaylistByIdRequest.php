<?php

declare(strict_types=1);

namespace App\Http\Requests\Playlists;

use App\Http\DataTransferObjects\Playlists\AddSongsToPlaylistByIdRequestData;
use App\Http\Resources\PlaylistResource;
use App\Models\Playlist;
use Illuminate\Foundation\Http\FormRequest;

class AddSongsToPlaylistByIdRequest extends FormRequest
{
    public function responseResource(Playlist $playlist): PlaylistResource
    {
        return PlaylistResource::make($playlist);
    }
    
    public function data(): AddSongsToPlaylistByIdRequestData
    {
        return resolve(AddSongsToPlaylistByIdRequestData::class);
    }
}