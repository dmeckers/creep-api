<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth\Telegram;

use App\Http\DataTransferObjects\Auth\Telegram\TelegramLoginRequestData;
use Illuminate\Foundation\Http\FormRequest;

class TelegramLoginRequest extends FormRequest
{
    public function data(): TelegramLoginRequestData
    {
        return resolve(TelegramLoginRequestData::class);
    }
}
