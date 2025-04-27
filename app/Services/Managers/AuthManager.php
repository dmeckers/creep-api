<?php

declare(strict_types=1);

namespace App\Services\Managers;

use App\Enums\AppEnvEnum;
use App\Http\DataTransferObjects\Auth\Telegram\TelegramLoginRequestData;
use App\Models\User;
use Arr;
use Auth;
use Carbon\Carbon;
use Exception;
use Hash;
use Illuminate\Database\Eloquent\Model;

class AuthManager
{
    private const HASH_QUERY_PARAMETER_KEY = 'hash';

    private const USER_QUERY_PARAMETER_KEY = 'user';

    private const AUTH_DATE_QUERY_PARAMETER_KEY = 'auth_date';

    private const SHA256_TOKEN_HASH_KEY = 'WebAppData';

    private const DEFAULT_AUTH_DATE_LIFETIME = 10;

    public function __construct(private readonly User $userModel)
    {
    }

    public function telegramAuth(): Model|User
    {
        if (config('app.env') === AppEnvEnum::LOCAL->value) {
            $user = $this->userModel->firstOrFail();

            Auth::login($user);

            return $user;
        }

        abort_if($this->isSignatureValid() === false, 403, 'GTFO');

        $userFromTelegram = json_decode(request()->query(self::USER_QUERY_PARAMETER_KEY), true);

        $user = $this->userModel->firstOrNew(
            [
                User::TELEGRAM_ID => strval($userFromTelegram['id']),
            ],
            [
                User::TELEGRAM_USERNAME => strval($userFromTelegram['username']),
                User::PHOTO_URL => strval($userFromTelegram['photo_url']),
                User::LAST_NAME => strval($userFromTelegram['last_name']),
                User::NAME => strval($userFromTelegram['first_name']),
                User::PASSWORD => Hash::make(strval($userFromTelegram['id'])),
            ]
        );

        $user->save();

        Auth::login($user);

        request()->session()->regenerate();

        return $user;
    }

    public function isSignatureValid(): bool
    {
        $queryParams = request()->query();

        if (!$this->telegramInitDataValid($queryParams)) {
            return false;
        }

        $requestHash = $queryParams[self::HASH_QUERY_PARAMETER_KEY];

        $hashFromQueryString = $this->createHashFromQueryString($queryParams);

        return $requestHash === $hashFromQueryString;
    }

    private function createHashFromQueryString(array $queryParams): string
    {
        $dataDigestKey = hash_hmac('sha256', config('telegram.bot.token'), self::SHA256_TOKEN_HASH_KEY, true);

        $dataWithoutHash = array_filter(
            $queryParams,
            fn($key) => $key !== self::HASH_QUERY_PARAMETER_KEY,
            ARRAY_FILTER_USE_KEY
        );

        ksort($dataWithoutHash);

        $dataCheckString = implode( // 3 * O(N) ~ O(N)
            PHP_EOL,
            array_map(
                fn(string $key, string $value) => "$key=$value",
                array_keys($dataWithoutHash),
                $dataWithoutHash
            )
        );

        return hash_hmac('sha256', $dataCheckString, $dataDigestKey);
    }

    private function telegramInitDataValid(array $telegramInitData): bool
    {
        return array_key_exists(self::USER_QUERY_PARAMETER_KEY, $telegramInitData)
            && !$this->authDateExpired(intval($telegramInitData[self::AUTH_DATE_QUERY_PARAMETER_KEY]));
    }

    private function authDateExpired(int $authDate): bool
    {
        $authDateLifetime = 60 * 60 * 24;  // 24 hours

        if ($authDateLifetime <= self::DEFAULT_AUTH_DATE_LIFETIME) {
            return false;
        }

        return $authDate + $authDateLifetime < time();
    }
}

