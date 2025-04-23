<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\JingleController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\StationController;
use App\Http\Controllers\TelegramAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth.session')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {

    Route::prefix('songs')->group(function () {

        Route::post('/', [SongController::class, 'upload']);

        Route::prefix('/{code}')->group(function () {
            Route::get('/', [SongController::class, 'getByCode']);
            Route::delete('/', [SongController::class, 'deleteByCode']);
            Route::get('/stream', [SongController::class, 'streamedSong']);
        });
    });

    Route::prefix('stations')->group(function () {
        Route::prefix('/{station_id}')->where(['station_id' => '[0-9]+'])->group(function () {

            Route::prefix('jingles')->group(function () {

                Route::get('/random/stream', [JingleController::class, 'streamRandomJingle']);
                Route::post('/', [JingleController::class, 'uploadJingle']);
                Route::get('/', [JingleController::class, 'getAllJingles']);

                Route::prefix('/{code}')->group(function () {
                    Route::get('/', [JingleController::class, 'getJingleByCode']);
                    Route::delete('/', [JingleController::class, 'deleteJingleByCode']);
                    Route::get('/stream', [JingleController::class, 'streamedJingle']);
                });

            });

            Route::get('/now-playing-playlist', [StationController::class, 'getNowPlayingPlaylist']);
        });
    });

    Route::prefix('auth')->group(function () {
        Route::prefix('login')->group(function () {
            Route::post('/telegram', [TelegramAuthController::class, 'login']);
        });
    });

    Route::get('/test', function () {
        return response()->json(['message' => 'Test route is working!']);
    });
});
