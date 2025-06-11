<?php

declare(strict_types=1);

namespace App\Http\Requests\Songs;

use App\Http\DataTransferObjects\Songs\UploadFromBotRequestData;
use App\Http\Resources\SongResource;
use App\Models\Song;
use Illuminate\Foundation\Http\FormRequest;

class UploadFromBotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function data(): UploadFromBotRequestData
    {
        return UploadFromBotRequestData::validateAndCreate(
            [
                'file_url' => (string) $this->input('file_url'),
                'file_id' => (string) $this->input('file_id'),
                'telegram_user_id' => $this->integer('telegram_user_id'),
                'telegram_user_first_name' => $this->input('telegram_user_first_name'),
                'telegram_user_username' => $this->input('telegram_user_username', null),
                'filename' => $this->input('filename', null),
            ]
        );
    }

    public function response(Song $song): SongResource
    {
        return SongResource::make($song)->additional(
            [
                'message' => 'Song uploaded successfully from bot.',
            ]
        );
    }
}
