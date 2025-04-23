<?php

declare(strict_types=1);

namespace App\Http\DataTransferObjects\Auth\Telegram;

use Spatie\LaravelData\Data;

class TelegramLoginRequestData extends Data
{
    public function __construct(
        public string $tg_data
    ) {
    }
}
