<?php

declare(strict_types=1);

namespace App\Http\DataTransferObjects\Auth\Telegram;

use Spatie\LaravelData\Data;

class TelegramLoginRequestUserData extends Data
{
    public function __construct(
        public int $id,
        public string $first_name,
        public ?string $language_code = '',
        public ?string $last_name = '',
        public ?string $username = '',
        public ?string $photo_url = '',
    ) {
    }
}
