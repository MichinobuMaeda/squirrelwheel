<?php

return [

    'base_path' => env('DOKU_BASE_PATH', null),
    'login_url' => env('DOKU_LOGIN_URL', null),
    'groups' => explode(',', env('DOKU_GROUPS', '')),

];
