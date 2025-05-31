<?php

use App\Http\Controllers\TelegramAuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/sanctum/token', [TelegramAuthController::class, 'setCookies']);

Route::get('/test-s3', function () {
    try {
        $disk = Storage::disk('s3')->directories();
        return ['exists' => $disk];
    } catch (\Throwable $e) {
        return [
            'error' => $e->getMessage(),
            'exception' => get_class($e),
            'trace' => $e->getTrace()
        ];
    }
});

Route::get('/debug-env', function () {
    return [
        'env' => env('AWS_BUCKET'),
        'config' => config('filesystems.disks.s3.bucket'),
        'raw_env' => $_ENV['AWS_BUCKET'] ?? null,
        'server' => $_SERVER['AWS_BUCKET'] ?? null,
    ];
});
