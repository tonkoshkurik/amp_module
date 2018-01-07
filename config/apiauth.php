<?php

return [
    'services' => [
        
        'ZHIRKILLER_ADMIN' => [
            'token' => env('ZHIRKILLER_ADMIN_TOKEN'),
            'tokenName' => 'api_token',

            'allowJsonToken' => true,
            'allowBearerToken' => true,
            'allowRequestToken' => true,
        ]
    ],
];
