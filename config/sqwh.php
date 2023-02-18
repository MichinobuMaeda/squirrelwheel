<?php

return [
    'auth_provider' => env('SQWH_AUTH_PROVIDER', null),
    'auth_session_life_time' => intval(env('SQWH_AUTH_SESSION_LIFE_TIME', '864000')),
    'auth_session_refresh_time' => intval(env('SQWH_AUTH_SESSION_REFRESH_TIME', '600')),
    'post_targets' => preg_split('/[\s,]+/', env('SQWH_POST_TARGETS', '')),
    'doku' => [
        'base_path' => env('SQWH_DOKU_BASE_PATH', null),
        'login_url' => env('SQWH_DOKU_LOGIN_URL', null),
        'groups' => preg_split('/[\s,]+/', env('SQWH_DOKU_GROUPS', '')),
    ],
    'tw' => [
        'consumer_key' => env('SQWH_TW_CONSUMER_KEY', null),
        'consumer_secret' => env('SQWH_TW_CONSUMER_SECRET', null),
        'access_token' => env('SQWH_TW_ACCESS_TOKEN', null),
        'access_token_secret' => env('SQWH_TW_ACCESS_TOKEN_SECRET', null),
    ],
    'mstdn' => [
        'server' => env('SQWH_MSTDN_SERVER', null),
        'users' => preg_split('/[\s,]+/', env('SQWH_MSTDN_USERS', '')),
        'client_key' => env('SQWH_MSTDN_CLIENT_KEY', null),
        'client_secret' => env('SQWH_MSTDN_CLIENT_SECRET', null),
        'access_token' => env('SQWH_MSTDN_ACCESS_TOKEN', null),
    ],
    'tumblr' => [
        'users' => preg_split('/[\s,]+/', env('SQWH_TUMBLR_USERS', '')),
        'consumer_key' => env('SQWH_TUMBLR_CONSUMER_KEY', ''),
        'consumer_secret' => env('SQWH_TUMBLR_SECRET_KEY', ''),
        'access_token' => env('SQWH_TUMBLR_ACCESS_TOKEN', null),
        'access_token_secret' => env('SQWH_TUMBLR_ACCESS_TOKEN_SECRET', null),
    ],
];
