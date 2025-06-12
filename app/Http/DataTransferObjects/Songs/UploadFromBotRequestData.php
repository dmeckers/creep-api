<?php

declare(strict_types=1);

namespace App\Http\DataTransferObjects\Songs;

use App\Http\DataTransferObjects\Users\FirstOrCreateTelegramUserData;
use Spatie\LaravelData\Attributes\Validation\Regex;
use Spatie\LaravelData\Data;

class UploadFromBotRequestData extends Data
{
    public function __construct(
        #[Regex(pattern: '/^https:\/\/api\.telegram\.org\//')]
        public string $file_url,
        public string $file_id,
        public int $telegram_user_id,
        public string $telegram_user_first_name,
        public ?string $telegram_user_username,
        public string $filename,
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
