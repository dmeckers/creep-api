<?php

declare(strict_types=1);

namespace App\Services\Repositories\Users;

use App\Http\DataTransferObjects\Users\FirstOrCreateTelegramUserData;
use App\Models\User;
use Hash;

class UserRepository
{
    public function __construct(private readonly User $userModel)
    {
    }

    public function firstOrCreate(FirstOrCreateTelegramUserData $data): User
    {
        return $this->userModel->firstOrCreate(
            [
                User::TELEGRAM_ID => $data->telegram_user_id,
            ],
            [
                User::TELEGRAM_USERNAME => $data->telegram_user_username,
                User::PHOTO_URL => $data->photo_url,
                User::LAST_NAME => $data->telegram_user_last_name,
                User::NAME => $data->telegram_user_first_name,
                User::PASSWORD => Hash::make((string) $data->telegram_user_id)
            ]
        );
    }
}
