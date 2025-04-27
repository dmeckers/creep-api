<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Managers\AuthManager;
use Illuminate\Http\JsonResponse;

class TelegramAuthController extends Controller
{
    public function __construct(private readonly AuthManager $authManager)
    {
    }

    public function login(): JsonResponse
    {
        return response()->json(['user' => $this->authManager->telegramAuth()]);
    }

    public function setCookies(): JsonResponse
    {
        $cookies = request()->cookie('XSRF-TOKEN');
        
        return response()->json(['cookies' => $cookies]);
    }
}
