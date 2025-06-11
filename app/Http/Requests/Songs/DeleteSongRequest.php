<?php

declare(strict_types=1);

namespace App\Http\Requests\Songs;

use App\Http\DataTransferObjects\Songs\DeleteSongRequestData;
use App\Http\Resources\SongResource;
use App\Models\Song;
use Illuminate\Foundation\Http\FormRequest;

class DeleteSongRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function data(): DeleteSongRequestData
    {
        return resolve(DeleteSongRequestData::class);
    }

    public function resourceResponse(Song $track): SongResource
    {
        return new SongResource($track);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }
}
