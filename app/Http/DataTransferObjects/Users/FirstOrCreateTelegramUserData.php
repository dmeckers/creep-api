<?php

declare(strict_types=1);

namespace App\Http\DataTransferObjects\Users;

use Spatie\LaravelData\Data;

class FirstOrCreateTelegramUserData extends Data
{
    public function __construct(
        public int $telegram_user_id,
        public string $telegram_user_first_name,
        public ?string $telegram_user_username,
        public ?string $telegram_user_last_name = null,
        public ?string $photo_url = null,
    ) {
    }
}
