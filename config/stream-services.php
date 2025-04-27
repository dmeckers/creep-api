<?php

declare(strict_types=1);

return [
    'janus' => [
        'admin_secret' => env('JANUS_ADMIN_SECRET', 'janusoverlord'),
        'admin-api-host' => env('JANUS_HOST', 'http://janus:7088/admin'),
        'api-host' => env('JANUS_HOST', 'http://janus:8188/janus'),
    ]
];
