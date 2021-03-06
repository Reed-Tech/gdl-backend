<?php

return [
    'defaults' => [
        'guard' => env("AUTH_API", "api"),
        'passwords' => 'users',
    ],

    'guards' => [
        'api' => [
            'driver' => env("AUTH_GUARDS_DRIVES", "jwt"),
            'provider' => 'users',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => \App\Models\User::class
        ]
    ]
];
