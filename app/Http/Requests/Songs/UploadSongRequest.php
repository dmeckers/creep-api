<?php

declare(strict_types=1);

namespace App\Http\Requests\Songs;

use App\Http\DataTransferObjects\Songs\StoreSongRequestData;
use App\Http\Resources\SongResource;
use App\Models\Song;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UploadSongRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function data(): StoreSongRequestData
    {
        return StoreSongRequestData::from([
            'file' => $this->file('file'),
            'name' => $this->input('name'),
            'artistId' => $this->input('artistId'),
            'ownerId' => $this->input('ownerId'),
            'playlistId' => $this->input('playlistId'),
        ]);
    }

    public function resourceResponse(Song $track): SongResource
    {
        return new SongResource($track);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'ownerId' => $this->user()?->id ?? 1,
            'code' => null
                ?? $this->input('code')
                ?? Str::of($this->file('file')->hashName())
                    ->replaceMatches('/\.[^.]+$/', '')
                    ->replace(' ', '_')
                    ->replace('/', '_')
                    ->replace('\\', '_')
        ]);
    }
}
