<?php

use App\Http\Controllers\JingleController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\StationController;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\TelegramAuthController;
use App\Http\Controllers\UrlSongUploadControlller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use YoutubeDl\Options;
use YoutubeDl\YoutubeDl;

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
Route::get('/test', function () {
    $yt = new YoutubeDl();
    $collection = $yt->download(
        Options::create()
            ->downloadPath('/var/www/storage/app/public/songs')
            ->extractAudio(true)
            ->audioFormat('mp3')
            ->audioQuality('0') // best
            ->output('%(title)s.%(ext)s')
            ->url('https://youtu.be/hgoCxqQFAxs?si=iZS27_mnMMs5J-PD')
    );

    foreach ($collection->getVideos() as $video) {
        if ($video->getError() !== null) {
            echo "Error downloading video: {$video->getError()}.";
        } else {
            $video->getFile(); // audio file
        }
    }
});


Route::prefix('v1/auth')->middleware(['web'])->group(function () {
    Route::prefix('login')->group(function () {
        Route::post('/', [TelegramAuthController::class, 'login']);
    });
});

Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {

    Route::get('/sync', SyncController::class);

    /**
     * User routes
     */
    Route::prefix('/user')->group(function () {

        Route::get('/', function (Request $request) {
            return response()->json(['usr' => $request->user()]);
        });

        Route::prefix('stations')->group(function () {
            Route::get('/', [StationController::class, 'getUserStations']);
        });
    });

    /**
     * Songs routes
     */
    Route::prefix('songs')->group(function () {

        Route::post('/', [SongController::class, 'upload']);
        Route::get('/', [SongController::class, 'getSongs']);

        Route::post('/youtube/upload', [UrlSongUploadControlller::class, 'uploadFromYoutube']);
        Route::post('/youtu.be/upload', [UrlSongUploadControlller::class, 'uploadFromYoutube']);
        Route::post('/vkontakte/upload', [UrlSongUploadControlller::class, 'uploadFromVkontakte']);

        Route::prefix('/{code}')->group(function () {
            Route::get('/', [SongController::class, 'getByCode']);
            Route::delete('/', [SongController::class, 'deleteByCode']);
            Route::get('/stream', [SongController::class, 'streamedSong']);
        });
    });

    /**
     * Playlist routes
     */
    Route::prefix('playlists')->group(function () {
        Route::post('/', [PlaylistController::class, 'createPlaylist']);

        Route::prefix('/{playlist_id}')->where(['playlist_id' => '[0-9]+'])->group(function () {
            Route::patch('/', [PlaylistController::class, 'updatePlaylist']);

            Route::prefix('songs')->group(function () {
                Route::get('/', [PlaylistController::class, 'getPlaylistSongs']);
                Route::patch('/', [PlaylistController::class, 'addSongsToPlaylistById']);

                Route::prefix('/{song_id}')->where(['song_id' => '[0-9]+'])->group(function () {
                    Route::delete('/', [PlaylistController::class, 'removeSongFromPlaylist']);
                });
            });
        });
    });

    /**
     * Stations routes
     */
    Route::prefix('stations')->group(function () {
        Route::get('/search', [StationController::class, 'searchStations']);
        Route::post('/', [StationController::class, 'createStation']);

        Route::prefix('/{station_mount_point}')->where(['station_mount_point' => '[a-zA-Z0-9_-]+'])->group(function () {
            Route::prefix('queue')->group(function () {
                Route::get('/current', [StationController::class, 'getCurrentSongFromQueue']);
            });
        });

        Route::prefix('/{station_id}')->where(['station_id' => '[0-9]+'])->group(function () {

            Route::prefix('spin')->group(function () {
                Route::post('/up', [StationController::class, 'spinUpStation']);
                Route::post('/down', [StationController::class, 'spinDownStation']);
            });

            Route::delete('/', [StationController::class, 'deleteStation']);
            Route::patch('/', [StationController::class, 'updateStation']);

            Route::prefix('playlists')->group(function () {
                Route::get('/', [PlaylistController::class, 'getStationPlaylists']);
            });

            /**
             * Station jingles routes
             */
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
});
