<?php

return [

    'doku' => [
        'base_path' => env('SW_DOKU_BASE_PATH', null),
        'login_url' => env('SW_DOKU_LOGIN_URL', null),
        'groups' => explode(',', env('SW_DOKU_GROUPS', '')),
    ],
    'tw' => [
        'consumer_key' => env('SW_TW_CONSUMER_KEY', null),
        'consumer_secret' => env('SW_TW_CONSUMER_SECRET', null),
        'access_token' => env('SW_TW_ACCESS_TOKEN', null),
        'access_token_secret' => env('SW_TW_ACCESS_TOKEN_SECRET', null),
    ],
];
