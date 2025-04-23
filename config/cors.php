<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['*'], // Allow all paths

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [
        '^https:\/\/[a-z0-9\-]+\.ngrok\-free\.app$',
        '^https:\/\/[a-z0-9\-]+\.a\.free\.pinggy\.link$',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => ['*'], // Expose all headers

    'max_age' => 0,

    'supports_credentials' => true, // Enable credentials support

];
