<?php

declare(strict_types=1);

namespace App\Http\Requests\Songs;

use App\Http\DataTransferObjects\Songs\UploadFileFromBotRequestData;
use App\Http\Resources\SongResource;
use App\Models\Song;
use Illuminate\Foundation\Http\FormRequest;

class UploadFileFromBotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function data(): UploadFileFromBotRequestData
    {
        return UploadFileFromBotRequestData::validateAndCreate(
            [
                'file_id' => (string) $this->input('file_id'),
                'telegram_user_id' => $this->integer('telegram_user_id'),
                'telegram_user_first_name' => $this->input('telegram_user_first_name'),
                'telegram_user_username' => $this->input('telegram_user_username', null),
                'filename' => $this->input('filename'),
                'file' => $this->file('file'),
            ]
        );
    }

    public function responseResource(Song $song): SongResource
    {
        return SongResource::make($song)->additional(
            [
                'message' => 'Song uploaded successfully from bot.',
            ]
        );
    }
}
