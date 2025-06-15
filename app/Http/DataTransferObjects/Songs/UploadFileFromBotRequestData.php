<?php

declare(strict_types=1);

namespace App\Http\DataTransferObjects\Songs;

use App\Http\DataTransferObjects\Users\FirstOrCreateTelegramUserData;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Attributes\Validation\Regex;
use Spatie\LaravelData\Data;

class UploadFileFromBotRequestData extends Data
{
    public function __construct(
        public string $file_id,
        public int $telegram_user_id,
        public string $telegram_user_first_name,
        public ?string $telegram_user_username,
        public string $filename,
        public UploadedFile $file,
    ) {
    }

    public function toUserData(): FirstOrCreateTelegramUserData
    {
        return new FirstOrCreateTelegramUserData(
            telegram_user_id: $this->telegram_user_id,
            telegram_user_first_name: $this->telegram_user_first_name,
            telegram_user_username: $this->telegram_user_username,
        );
    }
}
