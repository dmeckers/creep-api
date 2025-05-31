<?php

declare(strict_types=1);

namespace App\Http\Controllers;

class SyncController extends Controller
{
    public function __invoke()
    {
        $startTime = microtime(true);
        $response = response()->json(
            [
                'server_time' => microtime(true),
                'server_latency' => 0,
            ]
        );

        // Calculate response time in milliseconds
        $processingTime = (microtime(true) - $startTime) * 1000;

        // gpt said to do it idk why
        return $response->header('X-Response-Time', round($processingTime, 2) . 'ms');
    }
}
