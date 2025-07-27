<?php

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
$local = [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,
];

switch (env('APP_ENV')) {
    case 'local':
        return $local;

    case 'production':
        return [

            'paths' => ['api/*', 'sanctum/csrf-cookie'],

            'allowed_methods' => ['*'],

            'allowed_origins' => ['https://*.bayizone.com', 'server.cuk.com', '89.252.184.251:8880'],

            'allowed_origins_patterns' => [],

            'allowed_headers' => ['*'],

            'exposed_headers' => [],

            'max_age' => 0,

            'supports_credentials' => false,
        ];

    default:
        return $local;
}
