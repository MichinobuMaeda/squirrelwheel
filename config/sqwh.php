<?php

return [
    'auth_provider' => env('SQWH_AUTH_PROVIDER', null),
    'post_target' => explode(',', env('SQWH_POST_TARGET', '')),
    'doku' => [
        'base_path' => env('SQWH_DOKU_BASE_PATH', null),
        'login_url' => env('SQWH_DOKU_LOGIN_URL', null),
        'groups' => explode(',', env('SQWH_DOKU_GROUPS', '')),
    ],
    'tw' => [
        'consumer_key' => env('SQWH_TW_CONSUMER_KEY', null),
        'consumer_secret' => env('SQWH_TW_CONSUMER_SECRET', null),
        'access_token' => env('SQWH_TW_ACCESS_TOKEN', null),
        'access_token_secret' => env('SQWH_TW_ACCESS_TOKEN_SECRET', null),
    ],
    'mstdn' => [
        'server' => env('SQWH_MSTDN_SERVER', null),
        'users' => explode(',', env('SQWH_MSTDN_USERS', '')),
        'client_key' => env('SQWH_MSTDN_CLIENT_KEY', null),
        'client_secret' => env('SQWH_MSTDN_CLIENT_SECRET', null),
        'access_token' => env('SQWH_MSTDN_ACCESS_TOKEN', null),
    ],
    'tumblr' => [
        'users' => explode(',', env('SQWH_TUMBLR_USERS', '')),
        'consumer_key' => explode(',', env('SQWH_TUMBLR_CONSUMER_KEY', '')),
        'consumer_secret' => explode(',', env('SQWH_TUMBLR_SECRET_KEY', '')),
    ],
];
