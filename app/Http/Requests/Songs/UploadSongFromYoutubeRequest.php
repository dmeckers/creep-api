<?php

declare(strict_types=1);

namespace App\Http\Requests\Songs;

use App\Http\DataTransferObjects\Songs\UploadSongFromYoutubeRequestData;
use Illuminate\Foundation\Http\FormRequest;


class UploadSongFromYoutubeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function data(): UploadSongFromYoutubeRequestData
    {
        return UploadSongFromYoutubeRequestData::validateAndCreate([
            'youtubeUrl' => $this->input('url'),
            'userId' => auth()->id(),
            'playlistId' => $this->input('playlist_id'),
        ]);
    }

    public function response()
    {
        return response()->json(['message' => 'Song upload putted in queue',], 201);
    }
}
