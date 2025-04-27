<?php

declare(strict_types=1);

namespace App\Http\Requests\Playlists;

use App\Http\DataTransferObjects\Playlists\RemoveSongFromPlaylistRequestData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class RemoveSongFromPlaylistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization logic can be added here if needed.
    }

    public function data(): RemoveSongFromPlaylistRequestData
    {
        $data = RemoveSongFromPlaylistRequestData::validateAndCreate(
            $this->all(),
        );

        return $data;
    }

    public function responseResource(): JsonResponse
    {
        return response()->json(
            [
                'message' => 'Song removed from playlist successfully.',
            ],
            JsonResponse::HTTP_OK
        );
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'playlistId' => (int) $this->route('playlist_id'),
            'songId' => (int) $this->route('song_id'),
        ]);
    }
}
