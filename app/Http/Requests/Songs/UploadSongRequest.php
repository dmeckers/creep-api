<?php

declare(strict_types=1);

namespace App\Http\Requests\Songs;

use App\Http\DataTransferObjects\Songs\StoreSongRequestData;
use App\Http\Resources\SongResource;
use App\Models\Song;
use Illuminate\Foundation\Http\FormRequest;

class UploadSongRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function data(): StoreSongRequestData
    {
        return resolve(StoreSongRequestData::class);
    }

    public function resourceResponse(Song $track): SongResource
    {
        return new SongResource($track);
    }
}
