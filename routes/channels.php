<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('station.{stationName}', function ($user = null, $stationId) {
    return true; // Можно ограничить для авторизованных
});

Broadcast::channel('user.{userId}.upload-progress', function ($user = null, $stationId) {
    return true; // Можно ограничить для авторизованных
});
