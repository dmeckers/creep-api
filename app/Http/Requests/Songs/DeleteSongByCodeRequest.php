<?php

declare(strict_types=1);

namespace App\Http\Requests\Songs;

use App\Http\DataTransferObjects\Songs\DeleteSongByCodeRequestData;
use App\Http\Resources\SongResource;
use App\Models\Song;
use Illuminate\Foundation\Http\FormRequest;

class DeleteSongByCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function data(): DeleteSongByCodeRequestData
    {
        return resolve(DeleteSongByCodeRequestData::class);
    }

    public function resourceResponse(Song $track): SongResource
    {
        return new SongResource($track);
    }
}
