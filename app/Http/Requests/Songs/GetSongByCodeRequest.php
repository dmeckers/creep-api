<?php

declare(strict_types=1);

namespace App\Http\Requests\Songs;

use App\Http\DataTransferObjects\Songs\GetSongByCodeRequestData;
use App\Http\Resources\SongResource;
use App\Models\Song;
use Illuminate\Foundation\Http\FormRequest;

class GetSongByCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|exists:songs,code',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => $this->route('code'),
        ]);
    }

    public function data(): GetSongByCodeRequestData
    {
        return resolve(GetSongByCodeRequestData::class);
    }

    public function code(): string
    {
        return (string) $this->route('code');
    }

    public function resourceResponse(Song $track): SongResource
    {
        return new SongResource($track);
    }
}
